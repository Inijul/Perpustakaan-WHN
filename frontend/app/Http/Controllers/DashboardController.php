<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Ambil data koleksi dari backend API
            $koleksiResponse = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'max-age=60',
                    'Accept' => 'application/json'
                ])
                ->get('http://backend:5000/api/koleksi');
            
            $totalBuku = 0;
            $totalJurnal = 0;
            $totalSkripsi = 0;
            
            if ($koleksiResponse->successful()) {
                $koleksis = $koleksiResponse->json() ?: [];
                
                // Hitung total berdasarkan kategori
                foreach ($koleksis as $koleksi) {
                    $kategori = strtolower($koleksi['kategori'] ?? '');
                    
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
            }
            
            // Ambil data aktivitas untuk menghitung buku yang dipinjam
            $aktivitasResponse = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'max-age=60',
                    'Accept' => 'application/json'
                ])
                ->get('http://backend:5000/api/aktivitas');
            
            $totalDipinjam = 0;
            
            if ($aktivitasResponse->successful()) {
                $aktivitas = $aktivitasResponse->json() ?: [];
                
                // Hitung aktivitas dengan status_aktivitas 'dipinjam'
                foreach ($aktivitas as $item) {
                    if (strtolower($item['status_aktivitas'] ?? '') === 'dipinjam') {
                        $totalDipinjam++;
                    }
                }
            }
            

            
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
