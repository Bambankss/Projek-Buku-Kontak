<?php

namespace App\Http\Controllers;

class DaruratController extends Controller
{
    // Daftar nomor darurat bawaan, diambil dari config/darurat.php
    public function index()
    {
        return view('darurat.index', [
            'daftar' => config('darurat'),
        ]);
    }
}
