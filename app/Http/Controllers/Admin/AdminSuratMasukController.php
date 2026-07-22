<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Surat;
use App\Models\LogAktivitas;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSuratMasukController extends Controller
{
    /**
     * Daftar Surat Masuk
     */
    public function index(Request $request)
    {
    $baseQuery = Surat::where('jenis_surat', 'masuk')
        ->where('status', '!=', 'draft');

    $totalSurat = (clone $baseQuery)->count();

    $menunggu = (clone $baseQuery)
        ->where('status','diajukan')
        ->count();

    $disetujui = (clone $baseQuery)
        ->where('status','diverifikasi')
        ->count();

    $ditolak = (clone $baseQuery)
        ->where('status','dikembalikan')
        ->count();

    $diproses = (clone $baseQuery)
        ->where('status','diteruskan_ke_pimpinan')
        ->count();

    $selesai = (clone $baseQuery)
        ->where('status','selesai')
        ->count();

    $query = clone $baseQuery;

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $surat = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.surat.masuk.index', compact(
        'surat',
        'totalSurat',
        'menunggu',
        'disetujui',
        'ditolak',
        'diproses',
        'selesai'
    ));
}
    public function create()
    {
        return view('admin.surat.masuk.create');
    }

    /**
     * Simpan
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_surat'   => 'required|string|max:100|unique:surats,nomor_surat',
            'perihal'       => 'required|string|max:500',
            'tanggal_surat' => 'required|date',
            'asal_surat'    => 'required|string|max:255',
            'nomor_agenda'  => 'nullable|string|max:100',
            'metode'        => 'required|in:Email,Kurir,Pos,Langsung',
            'deskripsi'     => 'nullable|string',
            'is_priority'   => 'nullable|boolean',
            'file_path'     => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:'.((int) Setting::getValue('max_upload_mb', 5) * 1024),
        ]);

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('surat-masuk', 'public');
        }

        $data['jenis_surat'] = 'masuk';
        $data['status'] = Setting::getValue('incoming_default_status', 'diajukan');
        $data['user_id'] = auth()->id();
        $data['is_priority'] = $request->boolean('is_priority');

        $surat = Surat::create($data);


        // Log Admin
        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'action'      => 'Tambah Surat Masuk',
            'description' => 'Menambahkan surat '.$surat->nomor_surat,
        ]);

        // Log User
        if ($surat->user_id) {

            LogAktivitas::create([
                'user_id'     => $surat->user_id,
                'surat_id'    => $surat->id,
                'action'      => 'Pengajuan',
                'description' => 'Surat berhasil diajukan',
            ]);

        }

        return redirect()
            ->route('admin.surat.masuk.index')
            ->with('success','Surat berhasil ditambahkan');
    }

    /**
     * Detail
     */
    public function show($id)
    {
        $surat = Surat::findOrFail($id);

        return view('admin.surat.masuk.show', compact('surat'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $surat = Surat::findOrFail($id);

        return view('admin.surat.masuk.edit', compact('surat'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
{
    $request->validate([
        'nomor_surat'   => 'required',
        'perihal'       => 'required',
        'tanggal_surat' => 'required|date',
    ]);

    $surat = Surat::findOrFail($id);

    // Simpan status lama
    $statusLama = $surat->status;

    // Update data surat
    $surat->update([
        'nomor_surat'   => $request->nomor_surat,
        'perihal'       => $request->perihal,
        'tanggal_surat' => $request->tanggal_surat,
        'jenis_surat'   => 'masuk',
    ]);

    // Log aktivitas Admin
    LogAktivitas::create([
        'user_id'     => auth()->id(),
        'surat_id'    => $surat->id,
        'action'      => 'Update Surat',
        'description' => 'Admin memperbarui data surat ' . $surat->nomor_surat,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Log aktivitas untuk pemilik surat
    | Hanya dibuat jika status berubah
    |--------------------------------------------------------------------------
    */
    if ($surat->user_id && $statusLama != $surat->status) {

        switch ($surat->status) {

            case 'menunggu':

                LogAktivitas::create([
                    'user_id'     => $surat->user_id,
                    'surat_id'    => $surat->id,
                    'action'      => 'Verifikasi',
                    'description' => 'Surat sedang diverifikasi oleh Admin.',
                ]);

                break;

            case 'proses':

                LogAktivitas::create([
                    'user_id'     => $surat->user_id,
                    'surat_id'    => $surat->id,
                    'action'      => 'Proses',
                    'description' => 'Surat sedang diproses oleh Petugas.',
                ]);

                break;

            case 'selesai':

                LogAktivitas::create([
                    'user_id'     => $surat->user_id,
                    'surat_id'    => $surat->id,
                    'action'      => 'Selesai',
                    'description' => 'Surat telah selesai diproses dan dapat diunduh.',
                ]);

                break;

            case 'ditolak':

                LogAktivitas::create([
                    'user_id'     => $surat->user_id,
                    'surat_id'    => $surat->id,
                    'action'      => 'Ditolak',
                    'description' => 'Surat ditolak oleh Admin.',
                ]);

                break;
        }
    }

    return redirect()
        ->route('admin.surat.masuk.index')
        ->with('success', 'Surat berhasil diperbarui.');
}

    /**
     * Setujui surat (lolos verifikasi)
     */
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'diajukan') {
            return back()->with('error', 'Hanya surat yang telah diajukan yang dapat diverifikasi.');
        }

        $surat->update([
            'status'        => 'diverifikasi',
            'catatan_admin' => $request->catatan_admin,
        ]);

        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'surat_id'    => $surat->id,
            'action'      => 'Verifikasi',
            'description' => 'Surat ' . $surat->nomor_surat . ' disetujui oleh Admin.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id'     => $surat->user_id,
                'surat_id'    => $surat->id,
                'action'      => 'Disetujui',
                'description' => 'Surat Anda telah disetujui.' . ($surat->catatan_admin ? ' Catatan: ' . $surat->catatan_admin : ''),
            ]);
        }

        return back()->with('success', 'Surat berhasil disetujui.');
    }

    /**
     * Tolak surat (gagal verifikasi)
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:1000',
        ], [
            'catatan_admin.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'diajukan') {
            return back()->with('error', 'Hanya surat yang telah diajukan yang dapat dikembalikan.');
        }

        $surat->update([
            'status'        => 'dikembalikan',
            'catatan_admin' => $request->catatan_admin,
        ]);

        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'surat_id'    => $surat->id,
            'action'      => 'Verifikasi',
            'description' => 'Surat ' . $surat->nomor_surat . ' ditolak oleh Admin.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id'     => $surat->user_id,
                'surat_id'    => $surat->id,
                'action'      => 'Ditolak',
                'description' => 'Surat Anda ditolak. Catatan: ' . $surat->catatan_admin,
            ]);
        }

        return back()->with('success', 'Surat dikembalikan kepada pegawai untuk diperbaiki.');
    }

    /** Catat penyaluran administratif ke pimpinan (tanpa akun/modul pimpinan). */
    public function teruskanKePimpinan(Request $request, $id)
    {
        $request->validate([
            'catatan_pengantar' => 'nullable|string|max:2000',
            'metode_penerusan' => 'required|in:fisik,email,lainnya',
        ]);

        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'diverifikasi') {
            return back()->with('error', 'Surat harus diverifikasi sebelum diteruskan ke pimpinan.');
        }

        if (!$surat->jabatan_pimpinan_id && !$surat->nama_pimpinan) {
            return back()->with('error', 'Tujuan pimpinan pada surat belum diisi.');
        }

        $surat->update([
            'status' => 'diteruskan_ke_pimpinan',
            'diteruskan_oleh' => auth()->id(),
            'diteruskan_pada' => now(),
            'catatan_pengantar' => $request->catatan_pengantar,
            'metode_penerusan' => $request->metode_penerusan,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'surat_id' => $surat->id,
            'action' => 'Diteruskan ke Pimpinan',
            'description' => 'Admin meneruskan surat ' . $surat->nomor_surat
                . ' kepada ' . ($surat->nama_pimpinan ?: optional($surat->jabatanPimpinan)->nama)
                . ' melalui ' . $request->metode_penerusan . '.',
        ]);

        if ($surat->user_id) {
            LogAktivitas::create([
                'user_id' => $surat->user_id,
                'surat_id' => $surat->id,
                'action' => 'Diteruskan ke Pimpinan',
                'description' => 'Surat Anda telah diteruskan oleh admin ke pimpinan.',
            ]);
        }

        return back()->with('success', 'Penerusan surat ke pimpinan berhasil dicatat.');
    }

    /**
     * Hapus
     */
    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);

        if (!in_array($surat->status, ['draft', 'dikembalikan'], true) || $surat->disposisi()->exists()) {
            return back()->with('error', 'Surat yang sudah diproses atau memiliki disposisi tidak dapat dihapus.');
        }

        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        LogAktivitas::create([
            'user_id'     => auth()->id(),
            'action'      => 'Hapus Surat Masuk',
            'description' => 'Menghapus surat '.$surat->nomor_surat,
        ]);

        $surat->delete();

        return redirect()
            ->route('admin.surat.masuk.index')
            ->with('success','Surat berhasil dipindahkan dari daftar aktif.');
    }
}
