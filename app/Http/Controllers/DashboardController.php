<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Koleksi;
use App\Models\Aktivitas;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Ambil data koleksi langsung dari database
            $koleksis = Koleksi::all();
            
            $totalBuku = 0;
            $totalJurnal = 0;
            $totalSkripsi = 0;
            
            // Hitung total berdasarkan kategori
            foreach ($koleksis as $koleksi) {
                $kategori = strtolower($koleksi->kategori ?? '');
                
                switch ($kategori) {
                    case 'buku':
                        $totalBuku++;
                        break;
                    case 'jurnal':
                        $totalJurnal++;
                        break;
                    case 'skripsi':
                    case 'dokumen skripsi':
                        $totalSkripsi++;
                        break;
                }
            }
            
            // Hitung aktivitas dengan status 'dipinjam'
            $totalDipinjam = Aktivitas::where('status', 'dipinjam')->count();
            

            
            Log::info('Dashboard data calculated successfully', [
                'total_buku' => $totalBuku,
                'total_jurnal' => $totalJurnal,
                'total_skripsi' => $totalSkripsi,
                'total_dipinjam' => $totalDipinjam
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception while calculating dashboard data:', ['error' => $e->getMessage()]);
            
            // Set default values jika terjadi error
            $totalBuku = 0;
            $totalJurnal = 0;
            $totalSkripsi = 0;
            $totalDipinjam = 0;
        }
        
        return view('dashboard', compact(
            'totalBuku',
            'totalJurnal', 
            'totalSkripsi',
            'totalDipinjam'
        ));
    }
}
