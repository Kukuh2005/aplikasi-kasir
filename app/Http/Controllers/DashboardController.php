<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $tahun = $now->year;
        $bulan = $now->month;
        $bulan_ini = $now->translatedFormat('F');
        $user = User::all();
        $barang = Barang::all();
        $transaksi = Transaksi::all();
        $detail = TransaksiDetail::orderBy('created_at', 'desc')->get();
        $omset_tahun_ini = TransaksiDetail::whereYear('created_at', $tahun)->get();
        $omset = 0;

        foreach($omset_tahun_ini as $obi){
            $omset = $obi->total + $omset;
        }
        
        $stok_kosong = Barang::where('stok', 0)->get();

        $hari_ini = Carbon::now()->format('Y-m-d');
        $transaksi_hari_ini = Transaksi::whereDate('tanggal', $hari_ini)->get();
        $transaksi_bulan_ini = Transaksi::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->get();

        $chart_transaksi = Transaksi::selectRaw('MONTH(tanggal) as bulan, SUM(total) as total_transaksi')
        ->whereYear('tanggal', $tahun)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->pluck('total_transaksi', 'bulan');

        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $total = [];

        for($i = 1; $i <= 12; $i++){
            $total[] = $chart_transaksi->get($i, 0);
        }

        $barang_laris = TransaksiDetail::selectRaw('barang_id, SUM(jumlah) as total')
        ->groupBy('barang_id')
        ->get();

        $label_barang = [];
        $data_barang = [];

        foreach($barang_laris as $bl){
            $barang_id = Barang::find($bl->barang_id);
            $label_barang[] = $barang_id->nama;
            $data_barang[] = $bl->total;
        }

        return view('dashboard.index', compact(
            'barang',
            'transaksi', 
            'detail', 
            'transaksi_hari_ini', 
            'transaksi_bulan_ini', 
            'stok_kosong', 
            'bulan', 
            'bulan_ini', 
            'total',
            'tahun',
            'label_barang',
            'data_barang',
            'omset',
            ));
    }
}
