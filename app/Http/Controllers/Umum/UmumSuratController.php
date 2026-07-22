<?php

namespace App\Http\Controllers\Umum;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Setting;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UmumSuratController extends Controller
{
    public function index(Request $request)
    {
        $base = Surat::where('user_id', auth()->id());
        $stats = [
            'total' => (clone $base)->count(),
            'diajukan' => (clone $base)->whereIn('status', ['menunggu', 'diajukan'])->count(),
            'diproses' => (clone $base)->whereIn('status', ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'])->count(),
            'perbaikan' => (clone $base)->whereIn('status', ['dikembalikan', 'ditolak'])->count(),
            'selesai' => (clone $base)->whereIn('status', ['selesai', 'terkirim', 'diarsipkan'])->count(),
        ];
        $statusGroups = [
            'diajukan' => ['menunggu', 'diajukan'],
            'diproses' => ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'],
            'perbaikan' => ['dikembalikan', 'ditolak'],
            'selesai' => ['selesai', 'terkirim', 'diarsipkan'],
        ];
        $surats = $base
            ->when($request->keyword, fn ($q, $keyword) => $q->where(fn ($sub) => $sub->where('nomor_surat', 'like', "%{$keyword}%")->orWhere('perihal', 'like', "%{$keyword}%")->orWhere('kategori_pengajuan', 'like', "%{$keyword}%")))
            ->when($request->kategori, fn ($q, $kategori) => $q->where('kategori_pengajuan', $kategori))
            ->when($request->status && isset($statusGroups[$request->status]), fn ($q) => $q->whereIn('status', $statusGroups[$request->status]))
            ->latest('updated_at')->paginate(10)->withQueryString();

        return view('umum.surat.index', compact('surats', 'stats'));
    }

    public function create() { return view('umum.surat.create'); }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        if ($request->hasFile('file_path')) $data['file_path'] = $request->file('file_path')->store('surat-umum', 'public');
        $data['user_id'] = auth()->id();
        $data['jenis_surat'] = 'masuk';
        $data['nomor_surat'] = $this->generateSubmissionNumber();
        $data['tanggal_surat'] = now()->toDateString();
        $data['status'] = 'diajukan';
        $data['asal_surat'] = auth()->user()->name;
        $data['tujuan_surat'] = 'Administrasi Umum';
        $data['metode'] = 'Sistem';
        $data['is_priority'] = false;
        $surat = Surat::create($data);
        $this->log($surat, 'Pengajuan Surat', 'Surat '.$surat->nomor_surat.' diajukan untuk diverifikasi admin.');

        return redirect()->route('umum.surat.index')->with('success', 'Surat berhasil diajukan dan menunggu verifikasi admin.');
    }

    public function show($id)
    {
        $surat = Surat::with(['logs' => fn ($q) => $q->latest(), 'logs.user'])->where('user_id', auth()->id())->findOrFail($id);
        return view('umum.surat.show', compact('surat'));
    }

    public function download($id)
    {
        $surat = Surat::where('user_id', auth()->id())->findOrFail($id);

        abort_unless($surat->file_path && Storage::disk('public')->exists($surat->file_path), 404);

        return Storage::disk('public')->download($surat->file_path, basename($surat->file_path));
    }

    public function edit($id)
    {
        return view('umum.surat.edit', ['surat' => $this->editableLetter($id)]);
    }

    public function update(Request $request, $id)
    {
        $surat = $this->editableLetter($id);
        $data = $request->validate($this->rules($surat->id));
        if ($request->hasFile('file_path')) {
            if ($surat->file_path) Storage::disk('public')->delete($surat->file_path);
            $data['file_path'] = $request->file('file_path')->store('surat-umum', 'public');
        }
        $data['status'] = 'diajukan';
        $data['asal_surat'] = auth()->user()->name;
        $surat->update($data);
        $this->log($surat, 'Perbaikan Surat', 'Surat diperbaiki dan diajukan kembali kepada admin.');

        return redirect()->route('umum.surat.index')->with('success', 'Perbaikan surat berhasil diajukan kembali.');
    }

    public function destroy($id)
    {
        $surat = Surat::where('user_id', auth()->id())->where('status', 'menunggu')->findOrFail($id);
        $this->log($surat, 'Hapus Pengajuan', 'Pengajuan '.$surat->nomor_surat.' dipindahkan ke histori terhapus.');
        $surat->delete();
        return redirect()->route('umum.surat.index')->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function cariBerkasForm()
    {
        $pengajuanTerbaru = Surat::where('user_id', auth()->id())->latest()->limit(5)->get();
        return view('umum.cari', compact('pengajuanTerbaru'));
    }

    public function cariBerkas(Request $request)
    {
        $data = $request->validate(['nomor_berkas' => 'required|string|max:100']);
        $number = trim($data['nomor_berkas']);
        $surat = Surat::where('user_id', auth()->id())->where('nomor_surat', $number)->first();
        return $surat
            ? redirect()->route('umum.surat.show', $surat->id)->with('success', 'Berkas ditemukan.')
            : back()->withInput()->with('error', 'Berkas tidak ditemukan pada akun Anda.');
    }

    public function layanan()
    {
        $informasi = [
            'jam' => Setting::getValue('public_service_hours', 'Senin–Jumat, 08.00–16.00'),
            'email' => Setting::getValue('public_help_email', ''),
            'telepon' => Setting::getValue('public_help_phone', ''),
            'maksimal_lampiran' => (int) Setting::getValue('max_upload_mb', 5),
        ];
        return view('umum.layanan.index', [
            'informasi' => $informasi,
            'layanan' => $this->serviceCatalog(),
        ]);
    }

    public function detailLayanan(string $layanan)
    {
        $service = $this->serviceCatalog()[$layanan] ?? null;
        abort_unless($service, 404);

        return view('umum.layanan.show', compact('service', 'layanan'));
    }

    private function serviceCatalog(): array
    {
        return [
            'informasi' => [
                'title' => 'Permohonan Informasi', 'icon' => 'bi-info-circle',
                'description' => 'Meminta penjelasan mengenai prosedur, persyaratan, atau informasi layanan.',
                'requirements' => ['Pokok informasi yang dibutuhkan', 'Nomor kontak aktif'],
                'steps' => ['Jelaskan informasi yang dicari', 'Admin memeriksa dan meneruskan pertanyaan', 'Jawaban dicatat pada status pengajuan'],
                'action' => 'Ajukan Pertanyaan',
            ],
            'dokumen' => [
                'title' => 'Permohonan Dokumen', 'icon' => 'bi-file-earmark-text',
                'description' => 'Mengajukan permintaan dokumen atau salinan informasi yang tersedia.',
                'requirements' => ['Nama dokumen secara jelas', 'Tujuan penggunaan dokumen', 'Bukti pendukung bila diperlukan'],
                'steps' => ['Sebutkan dokumen secara spesifik', 'Admin memeriksa ketersediaan dan kewenangan akses', 'Hasil permohonan disampaikan melalui status pengajuan'],
                'action' => 'Minta Dokumen',
            ],
            'penyampaian-surat' => [
                'title' => 'Penyampaian Surat', 'icon' => 'bi-envelope-paper',
                'description' => 'Menyampaikan surat resmi atau dokumen kepada bagian administrasi.',
                'requirements' => ['Pokok dan tujuan surat', 'Instansi asal jika ada', 'Surat resmi sebagai lampiran'],
                'steps' => ['Isi identitas dan pokok surat', 'Unggah surat resmi', 'Admin memverifikasi dan mencatat penerimaan'],
                'action' => 'Sampaikan Surat',
            ],
            'pengaduan' => [
                'title' => 'Pengaduan', 'icon' => 'bi-chat-left-dots',
                'description' => 'Melaporkan kendala atau permasalahan layanan untuk ditindaklanjuti.',
                'requirements' => ['Kronologi secara berurutan', 'Waktu kejadian', 'Bukti pendukung bila tersedia'],
                'steps' => ['Tuliskan kronologi secara objektif', 'Lampirkan bukti bila tersedia', 'Pantau tindak lanjut dan catatan admin'],
                'action' => 'Buat Pengaduan',
            ],
            'lainnya' => [
                'title' => 'Lainnya', 'icon' => 'bi-grid',
                'description' => 'Pengajuan lain yang tidak termasuk dalam kategori layanan utama.',
                'requirements' => ['Uraian kebutuhan secara lengkap', 'Kontak yang dapat dihubungi'],
                'steps' => ['Jelaskan kebutuhan secara lengkap', 'Admin menentukan unit tujuan', 'Pantau arahan berikutnya pada pengajuan'],
                'action' => 'Ajukan Kebutuhan',
            ],
        ];
    }

    private function editableLetter(int $id): Surat
    {
        return Surat::where('user_id', auth()->id())->whereIn('status', ['menunggu', 'dikembalikan', 'ditolak'])->findOrFail($id);
    }

    private function rules(?int $id = null): array
    {
        $maxKb = (int) Setting::getValue('max_upload_mb', 5) * 1024;
        return [
            'kategori_pengajuan' => ['required', Rule::in(['Permohonan Informasi', 'Permohonan Dokumen', 'Penyampaian Surat', 'Pengaduan', 'Lainnya'])],
            'nomor_kontak' => ['required', 'string', 'max:25', 'regex:/^[0-9+()\-\s]+$/'],
            'asal_instansi' => 'nullable|string|max:255',
            'perihal' => 'required|string|max:500',
            'deskripsi' => 'required|string|max:2000',
            'file_path' => "nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:{$maxKb}",
        ];
    }

    private function generateSubmissionNumber(): string
    {
        do {
            $number = 'UMUM/'.now()->format('Ymd').'/'.Str::upper(Str::random(6));
        } while (Surat::withTrashed()->where('nomor_surat', $number)->exists());

        return $number;
    }

    private function log(Surat $surat, string $action, string $description): void
    {
        LogAktivitas::create(['user_id' => auth()->id(), 'surat_id' => $surat->id, 'action' => $action, 'description' => $description]);
    }
}
