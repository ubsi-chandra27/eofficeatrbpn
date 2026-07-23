<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\DisposisiTujuan;
use App\Models\Pegawai;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DisposisiController extends Controller
{
    /**
     * Daftar disposisi pegawai
     */
    public function index(Request $request)
    {
        $pegawai = $this->pegawaiLogin();
        $query = DisposisiTujuan::with(['disposisi.surat', 'disposisi.pengirim'])
            ->where('pegawai_id', $pegawai->id)
            ->whereHas('disposisi');
        $base = DisposisiTujuan::where('pegawai_id', $pegawai->id)
            ->whereHas('disposisi');
        $stats = [
            'total' => (clone $base)->count(),
            'belum' => (clone $base)->where('status', 'Belum Dibaca')->count(),
            'dibaca' => (clone $base)->where('status', 'Sudah Dibaca')->count(),
            'selesai' => (clone $base)->where('status', 'Selesai')->count(),
        ];

        // Pencarian
        if ($request->filled('keyword')) {

            $keyword = $request->keyword;

            $query->whereHas('disposisi.surat', function ($q) use ($keyword) {

                $q->where('nomor_surat', 'like', "%{$keyword}%")
                  ->orWhere('perihal', 'like', "%{$keyword}%");

            });
        }

        // Filter Status
        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }

        $disposisi = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $dikirim = Disposisi::with(['surat', 'tujuans.pegawai'])
            ->where('pengirim_id', Auth::id())
            ->latest()
            ->paginate(10, ['*'], 'dikirim_page')
            ->withQueryString();

        return view(
            'pegawai.disposisi.index',
            compact('disposisi', 'dikirim', 'stats')
        );
    }

    public function create()
    {
        return view('pegawai.disposisi.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $surat = $this->suratDapatDidisposisikan((int) $data['surat_id']);

        DB::transaction(function () use ($data, $surat) {
            $disposisi = Disposisi::create([
                'surat_id' => $surat->id,
                'pengirim_id' => Auth::id(),
                'catatan' => trim($data['catatan']),
                'prioritas' => $data['prioritas'],
                'tanggal_disposisi' => $data['tanggal_disposisi'],
            ]);
            foreach ($data['pegawai_id'] as $pegawaiId) {
                DisposisiTujuan::create([
                    'disposisi_id' => $disposisi->id,
                    'pegawai_id' => $pegawaiId,
                    'status' => 'Belum Dibaca',
                ]);
            }
        });

        return redirect()->route('pegawai.disposisi.index')
            ->with('success', 'Disposisi berhasil dikirim kepada pegawai tujuan.');
    }

    public function sentShow(int $id)
    {
        $disposisi = $this->disposisiDikirim($id)->load([
            'surat', 'tujuans.pegawai.jabatan', 'tujuans.pegawai.unitKerja',
        ]);

        return view('pegawai.disposisi.sent-show', compact('disposisi'));
    }

    public function edit(int $id)
    {
        $disposisi = $this->disposisiDikirim($id)->load('tujuans');
        if (! $disposisi->is_editable) {
            return redirect()->route('pegawai.disposisi.sent.show', $disposisi)
                ->with('error', 'Disposisi yang sudah dibaca tidak dapat diubah.');
        }

        return view('pegawai.disposisi.edit', [
            ...$this->formData($disposisi),
            'disposisi' => $disposisi,
            'pegawaiDipilih' => $disposisi->tujuans->pluck('pegawai_id')->all(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $disposisi = $this->disposisiDikirim($id);
        $data = $this->validateData($request);
        $surat = $this->suratDapatDidisposisikan((int) $data['surat_id'], $disposisi);

        $updated = DB::transaction(function () use ($disposisi, $data, $surat) {
            $locked = Disposisi::lockForUpdate()->findOrFail($disposisi->id);
            if ($locked->tujuans()->where('status', '!=', 'Belum Dibaca')->exists()) {
                return false;
            }
            $locked->update([
                'surat_id' => $surat->id,
                'catatan' => trim($data['catatan']),
                'prioritas' => $data['prioritas'],
                'tanggal_disposisi' => $data['tanggal_disposisi'],
            ]);
            $locked->tujuans()->whereNotIn('pegawai_id', $data['pegawai_id'])->delete();
            foreach ($data['pegawai_id'] as $pegawaiId) {
                $locked->tujuans()->firstOrCreate(
                    ['pegawai_id' => $pegawaiId],
                    ['status' => 'Belum Dibaca']
                );
            }
            return true;
        });

        if (! $updated) {
            return back()->with('error', 'Disposisi yang sudah dibaca tidak dapat diubah.');
        }

        return redirect()->route('pegawai.disposisi.sent.show', $disposisi)
            ->with('success', 'Disposisi berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $disposisi = $this->disposisiDikirim($id);
        $deleted = DB::transaction(function () use ($disposisi) {
            $locked = Disposisi::lockForUpdate()->findOrFail($disposisi->id);
            if ($locked->tujuans()->where('status', '!=', 'Belum Dibaca')->exists()) {
                return false;
            }
            $locked->delete();
            return true;
        });

        return $deleted
            ? redirect()->route('pegawai.disposisi.index')->with('success', 'Disposisi terkirim berhasil dihapus.')
            : back()->with('error', 'Disposisi yang sudah dibaca tidak dapat dihapus.');
    }

    /**
     * Detail disposisi
     */
    public function show(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        if ($disposisi->isBelumDibaca()) {
            $disposisi->update([
                'status' => 'Sudah Dibaca',
                'dibaca_pada' => now(),
            ]);
        }

        return view(
            'pegawai.disposisi.show',
            compact('disposisi')
        );
    }

    /**
     * Cetak disposisi
     */
    public function cetak(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        return view(
            'pegawai.disposisi.cetak',
            compact('disposisi')
        );
    }

    /** Tandai disposisi milik pegawai sebagai telah dibaca. */
    public function dibaca(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        if ($disposisi->isBelumDibaca()) {
            $disposisi->update(['status' => 'Sudah Dibaca', 'dibaca_pada' => now()]);
        }

        return back()->with('success', 'Disposisi ditandai sudah dibaca.');
    }

    /** Selesaikan disposisi milik pegawai. */
    public function selesai(int $id)
    {
        $disposisi = $this->disposisiMilikPegawai($id);

        $disposisi->update([
            'status' => 'Selesai',
            'dibaca_pada' => $disposisi->dibaca_pada ?? now(),
            'selesai_pada' => now(),
        ]);

        return back()->with('success', 'Disposisi ditandai selesai.');
    }

    private function disposisiMilikPegawai(int $id): DisposisiTujuan
    {
        return DisposisiTujuan::with(['disposisi.surat', 'disposisi.pengirim'])
            ->where('pegawai_id', $this->pegawaiLogin()->id)
            ->findOrFail($id);
    }

    private function pegawaiLogin(): Pegawai
    {
        return Pegawai::where('user_id', Auth::id())->firstOrFail();
    }

    private function formData(?Disposisi $current = null): array
    {
        $pegawaiLogin = $this->pegawaiLogin();
        $surat = Surat::where('jenis_surat', 'masuk')
            ->whereIn('status', ['diverifikasi', 'diproses', 'diteruskan_ke_pimpinan'])
            ->where(function ($query) use ($pegawaiLogin, $current) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('disposisi.tujuans', fn ($target) => $target->where('pegawai_id', $pegawaiLogin->id));
                if ($current) {
                    $query->orWhere('id', $current->surat_id);
                }
            })
            ->latest('tanggal_surat')
            ->get();
        $pegawai = Pegawai::with(['jabatan', 'unitKerja'])
            ->where('id', '!=', $pegawaiLogin->id)
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query->where('role', 'pegawai'))
            ->orderBy('nama')
            ->get();

        return compact('surat', 'pegawai');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'surat_id' => 'required|exists:surats,id',
            'pegawai_id' => 'required|array|min:1',
            'pegawai_id.*' => [
                'distinct',
                Rule::exists('pegawai', 'id')->where(fn ($query) => $query
                    ->whereNotNull('user_id')->where('id', '!=', $this->pegawaiLogin()->id)),
            ],
            'catatan' => 'required|string|max:2000',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi',
            'tanggal_disposisi' => 'required|date',
        ]);
    }

    private function suratDapatDidisposisikan(int $id, ?Disposisi $current = null): Surat
    {
        $allowedIds = $this->formData($current)['surat']->pluck('id');
        abort_unless($allowedIds->contains($id), 403, 'Surat tidak dapat didisposisikan oleh akun ini.');
        return Surat::findOrFail($id);
    }

    private function disposisiDikirim(int $id): Disposisi
    {
        return Disposisi::where('pengirim_id', Auth::id())->findOrFail($id);
    }
}
