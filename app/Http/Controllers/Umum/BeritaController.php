<?php

namespace App\Http\Controllers\Umum;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->string('kategori')->toString();
        $keyword = trim($request->string('keyword')->toString());

        $query = Berita::published()->with('author');

        if (in_array($kategori, ['berita', 'pengumuman'], true)) {
            $query->where('kategori', $kategori);
        }

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('isi', 'like', "%{$keyword}%");
            });
        }

        $berita = $query->latest('published_at')->paginate(9)->withQueryString();

        return view('umum.berita.index', compact('berita', 'kategori', 'keyword'));
    }

    public function show($id)
    {
        $berita = Berita::published()->with('author')->findOrFail($id);

        return view('umum.berita.show', compact('berita'));
    }
}
