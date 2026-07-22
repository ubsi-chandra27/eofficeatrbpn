<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Setting;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    private const DEFAULTS = [
        'app_name' => 'E-Office', 'app_subtitle' => 'Administrasi Digital',
        'incoming_default_status' => 'diajukan', 'outgoing_default_status' => 'draft',
        'max_upload_mb' => '5', 'disposition_deadline_days' => '3',
        'notify_new_letter' => '1', 'notify_disposition' => '1', 'notify_deadline' => '1',
        'report_signer_name' => '', 'report_signer_title' => '', 'report_header' => 'Laporan Administrasi Persuratan',
        'public_announcement_title' => 'Informasi Layanan',
        'public_announcement_message' => 'Pastikan data pengajuan dan dokumen pendukung sudah benar sebelum dikirim.',
        'public_service_hours' => 'Senin–Jumat, 08.00–16.00',
        'public_help_email' => '', 'public_help_phone' => '',
    ];

    public function index()
    {
        $settings = collect(self::DEFAULTS)->mapWithKeys(fn ($default, $key) => [$key => Setting::getValue($key, $default)]);
        $trash = [
            'users' => User::onlyTrashed()->latest('deleted_at')->get(),
            'pegawai' => Pegawai::onlyTrashed()->latest('deleted_at')->get(),
            'surats' => Surat::onlyTrashed()->latest('deleted_at')->get(),
            'disposisi' => Disposisi::onlyTrashed()->latest('deleted_at')->get(),
        ];

        return view('admin.settings.index', compact('settings', 'trash'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:80', 'app_subtitle' => 'required|string|max:120',
            'incoming_default_status' => ['required', Rule::in(['diajukan', 'diverifikasi'])],
            'outgoing_default_status' => ['required', Rule::in(['draft', 'diajukan'])],
            'max_upload_mb' => 'required|integer|min:1|max:20',
            'disposition_deadline_days' => 'required|integer|min:1|max:30',
            'report_header' => 'required|string|max:150',
            'report_signer_name' => 'nullable|string|max:120', 'report_signer_title' => 'nullable|string|max:120',
            'public_announcement_title' => 'required|string|max:120',
            'public_announcement_message' => 'required|string|max:500',
            'public_service_hours' => 'required|string|max:120',
            'public_help_email' => 'nullable|email|max:150',
            'public_help_phone' => 'nullable|string|max:30',
            'notify_new_letter' => 'nullable|boolean', 'notify_disposition' => 'nullable|boolean', 'notify_deadline' => 'nullable|boolean',
        ]);

        foreach (['notify_new_letter', 'notify_disposition', 'notify_deadline'] as $checkbox) {
            $data[$checkbox] = $request->boolean($checkbox);
        }
        foreach ($data as $key => $value) {
            $group = str_starts_with($key, 'report_') ? 'report' : (str_starts_with($key, 'notify_') ? 'notification' : (str_starts_with($key, 'public_') ? 'public' : (in_array($key, ['app_name', 'app_subtitle']) ? 'general' : 'letter')));
            Setting::putValue($key, $value, $group);
        }
        LogAktivitas::create(['user_id' => auth()->id(), 'action' => 'Perbarui Pengaturan', 'description' => 'Memperbarui konfigurasi sistem.']);

        return back()->with('success', 'Pengaturan berhasil disimpan dan mulai digunakan.');
    }

    public function restore(string $type, int $id)
    {
        $models = ['users' => User::class, 'pegawai' => Pegawai::class, 'surats' => Surat::class, 'disposisi' => Disposisi::class];
        abort_unless(isset($models[$type]), 404);
        $item = $models[$type]::onlyTrashed()->findOrFail($id);
        $item->restore();
        LogAktivitas::create(['user_id' => auth()->id(), 'action' => 'Pulihkan Data', 'description' => "Memulihkan {$type} ID {$id} dari tempat sampah."]);

        return back()->with('success', 'Data berhasil dipulihkan.');
    }
}
