<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $kasbonChartData = $this->getKasbonChartData();
        return view('dashboard.index', compact('kasbonChartData'));
    }

    private function getKasbonChartData()
    {
    $data = Kasbon::selectRaw('status, SUM(jumlah) as total_nominal')
        ->groupBy('status')
        ->pluck('total_nominal', 'status')
        ->toArray();

    $statuses = ['menunggu', 'disetujui', 'ditolak'];
    $result = [];
    foreach ($statuses as $status) {
        $result[$status] = $data[$status] ?? 0;
    }

    return $result;
    }
}
