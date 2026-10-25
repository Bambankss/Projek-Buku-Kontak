<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // Dipanggil lewat fetch() dari modal "Kategori baru" di form kontak, balasannya JSON
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'nama')->where('user_id', $request->user()->id),
            ],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.max' => 'Nama kategori maksimal 50 karakter.',
            'nama.unique' => 'Kategori ini sudah ada.',
        ]);

        $category = $request->user()->categories()->create($data);

        return response()->json([
            'id' => $category->id,
            'nama' => $category->nama,
        ], 201);
    }
}
