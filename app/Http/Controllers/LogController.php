<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    // Riwayat aktivitas lengkap. Data diambil dulu (supaya label "Baru" masih tampil), baru semuanya ditandai dibaca.
    public function index(Request $request)
    {
        $user = $request->user();

        $logs = $user->activityLogs()->latest()->orderByDesc('id')->paginate(15);

        $user->activityLogs()->whereNull('dibaca_pada')->update(['dibaca_pada' => now()]);

        return view('logs.index', compact('logs'));
    }

    // Tombol "Tandai dibaca" di lonceng
    public function bacaSemua(Request $request)
    {
        $request->user()->activityLogs()->whereNull('dibaca_pada')->update(['dibaca_pada' => now()]);

        return back();
    }
}
