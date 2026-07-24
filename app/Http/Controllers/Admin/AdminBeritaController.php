<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminBeritaController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->string('keyword')->toString());
        $kategori = $request->string('kategori')->toString();
        $status = $request->string('status')->toString();

        $query = Berita::query()->with('author');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('isi', 'like', "%{$keyword}%");
            });
        }

        if (in_array($kategori, ['berita', 'pengumuman'], true)) {
            $query->where('kategori', $kategori);
        }

        if (in_array($status, ['published', 'draft'], true)) {
            $query->where('is_published', $status === 'published');
        }

        $berita = $query->latest('published_at')->paginate(12)->withQueryString();

        return view('admin.berita.index', compact('berita', 'keyword', 'kategori', 'status'));
    }

    public function create()
    {
        $authors = User::orderBy('name')->get(['id', 'name']);

        return view('admin.berita.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'isi'         => 'required|string|max:10000',
            'kategori'    => 'required|in:berita,pengumuman',
            'is_published'=> 'sometimes|boolean',
            'published_at'=> 'nullable|date',
            'file_path'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if (empty($data['published_at']) && !empty($data['is_published'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('berita', 'public');
        }

        $data['user_id'] = Auth::id();

        Berita::create($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function show(Berita $berita)
    {
        $berita->load('author');

        return view('admin.berita.show', compact('berita'));
    }

    public function edit(Berita $berita)
    {
        $authors = User::orderBy('name')->get(['id', 'name']);

        return view('admin.berita.edit', compact('berita', 'authors'));
    }

    public function update(Request $request, Berita $berita)
    {
        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'isi'         => 'required|string|max:10000',
            'kategori'    => 'required|in:berita,pengumuman',
            'is_published'=> 'sometimes|boolean',
            'published_at'=> 'nullable|date',
            'file_path'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if (empty($data['published_at']) && !empty($data['is_published'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('file_path')) {
            if ($berita->file_path) {
                Storage::disk('public')->delete($berita->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('berita', 'public');
        } else {
            $data['file_path'] = $berita->file_path;
        }

        $berita->update($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->file_path) {
            Storage::disk('public')->delete($berita->file_path);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}
