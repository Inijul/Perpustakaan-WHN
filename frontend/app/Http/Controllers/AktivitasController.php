<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AktivitasController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data aktivitas dari backend API
            Log::info('Fetching aktivitas from backend...');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'max-age=60',
                    'Accept' => 'application/json'
                ])
                ->get('http://backend:5000/api/aktivitas');
            
            Log::info('Backend response status:', ['status' => $response->status()]);
            
            if ($response->successful()) {
                $aktivitas = $response->json() ?: [];
                Log::info('Aktivitas data received:', [
                    'count' => count($aktivitas),
                    'sample' => array_slice($aktivitas, 0, 2)
                ]);
                
                // Ambil data koleksi untuk mendapatkan topik
                $koleksiMap = [];
                try {
                    $koleksiResponse = Http::timeout(30)->get('http://backend:5000/api/koleksi');
                    if ($koleksiResponse->successful()) {
                        $allKoleksis = $koleksiResponse->json() ?: [];
                        // Buat map kode -> topik
                        foreach ($allKoleksis as $koleksi) {
                            if (isset($koleksi['kode']) && isset($koleksi['topik'])) {
                                $koleksiMap[$koleksi['kode']] = $koleksi['topik'];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to fetch koleksi for topik mapping:', ['error' => $e->getMessage()]);
                }
                
                // Tambahkan topik dan mapping status ke setiap aktivitas
                foreach ($aktivitas as &$item) {
                    if (isset($item['kode']) && isset($koleksiMap[$item['kode']])) {
                        $item['topik'] = $koleksiMap[$item['kode']];
                    } else {
                        $item['topik'] = '-';
                    }

                    // Status aktivitas diambil dari backend API (tabel aktivitas)
                    // Gunakan status_aktivitas dari tabel aktivitas
                    if (isset($item['status_aktivitas'])) {
                        // Status sudah ada dari backend
                    } else {
                        $item['status_aktivitas'] = 'dipinjam'; // fallback default jika tidak ada
                    }
                }
                
                // Log untuk debugging
                Log::info('Aktivitas data with topik and status mapping:', [
                    'total_aktivitas' => count($aktivitas),
                    'sample_aktivitas' => array_slice($aktivitas, 0, 2),
                    'koleksi_map_count' => count($koleksiMap),
                    'sample_koleksi_map' => array_slice($koleksiMap, 0, 3, true)
                ]);
                
                // Filter berdasarkan pencarian
                $search = $request->get('search');
                if ($search) {
                    $aktivitas = array_filter($aktivitas, function($item) use ($search) {
                        return stripos($item['nama_mahasiswa'], $search) !== false ||
                               stripos($item['judul_buku'], $search) !== false ||
                               stripos($item['nrm'], $search) !== false ||
                               stripos($item['kode'], $search) !== false ||
                               stripos($item['kategori'] ?? '', $search) !== false ||
                               stripos($item['topik'] ?? '', $search) !== false;
                    });
                }
                
                // Filter berdasarkan status (sekarang menggunakan status_aktivitas dari aktivitas)
                $status = $request->get('status');
                if ($status && $status !== 'Status:') {
                    $aktivitas = array_filter($aktivitas, function($item) use ($status) {
                        return $item['status_aktivitas'] === $status;
                    });
                }
                
                // Filter berdasarkan topik
                $topik = $request->get('topik');
                if ($topik && $topik !== 'Topik:') {
                    $aktivitas = array_filter($aktivitas, function($item) use ($topik) {
                        return stripos($item['topik'] ?? '', $topik) !== false;
                    });
                }
                
                // Filter berdasarkan kode
                $kode = $request->get('kode');
                if ($kode && $kode !== 'Kode Buku:') {
                    $aktivitas = array_filter($aktivitas, function($item) use ($kode) {
                        return stripos($item['kode'] ?? '', $kode) !== false;
                    });
                }
                
                // Sorting: Status "dipinjam" dulu, lalu nama mahasiswa A-Z
                usort($aktivitas, function($a, $b) {
                    // Urutkan berdasarkan status: "dipinjam" dulu
                    $statusA = $a['status_aktivitas'] ?? 'dipinjam';
                    $statusB = $b['status_aktivitas'] ?? 'dipinjam';
                    
                    if ($statusA === 'dipinjam' && $statusB !== 'dipinjam') {
                        return -1; // A lebih dulu
                    } elseif ($statusA !== 'dipinjam' && $statusB === 'dipinjam') {
                        return 1; // B lebih dulu
                    } else {
                        // Jika status sama, urutkan berdasarkan nama mahasiswa A-Z
                        $namaA = $a['nama_mahasiswa'] ?? '';
                        $namaB = $b['nama_mahasiswa'] ?? '';
                        return strcasecmp($namaA, $namaB);
                    }
                });
                
                Log::info('Fetched aktivitas data:', ['count' => count($aktivitas), 'timestamp' => now()]);
            } else {
                $aktivitas = [];
                Log::error('Failed to fetch aktivitas:', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $aktivitas = [];
            Log::error('Exception while fetching aktivitas:', ['error' => $e->getMessage()]);
        }

        // Ambil data koleksi buku saja untuk dropdown (hanya yang tersedia)
        $koleksis = [];
        $availableTopics = [];
        try {
            Log::info('Attempting to fetch koleksi buku data from backend...');
            $koleksiResponse = Http::timeout(30)->get('http://backend:5000/api/koleksi');
            
            if ($koleksiResponse->successful()) {
                $allKoleksis = $koleksiResponse->json() ?: [];
                // Filter hanya koleksi dengan kategori 'Buku' dan status 'Tersedia'
                $koleksis = array_filter($allKoleksis, function($koleksi) {
                    return isset($koleksi['kategori']) && 
                           strtolower($koleksi['kategori']) === 'buku' &&
                           isset($koleksi['status']) && 
                           strtolower($koleksi['status']) === 'tersedia';
                });
                $koleksis = array_values($koleksis); // Reset array keys
                
                // Ekstrak topik yang tersedia dari buku
                $topics = array_filter(array_column($koleksis, 'topik'));
                $availableTopics = array_unique($topics);
                sort($availableTopics);
                
                Log::info('Koleksi buku data fetched successfully', [
                    'total_koleksi' => count($allKoleksis),
                    'buku_count' => count($koleksis),
                    'available_topics' => $availableTopics,
                    'available_topics_count' => count($availableTopics),
                    'sample_data' => array_slice($koleksis, 0, 2),
                    'response_status' => $koleksiResponse->status(),
                    'response_body_length' => strlen($koleksiResponse->body())
                ]);
            } else {
                Log::error('Failed to fetch koleksi data', [
                    'status' => $koleksiResponse->status(),
                    'body' => $koleksiResponse->body(),
                    'headers' => $koleksiResponse->headers()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching koleksi buku for aktivitas:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Ambil data mahasiswa untuk dropdown
        $mahasiswas = [];
        try {
            Log::info('Attempting to fetch mahasiswa data from backend...');
            $mahasiswaResponse = Http::timeout(30)->get('http://backend:5000/api/mahasiswa');
            
            if ($mahasiswaResponse->successful()) {
                $mahasiswas = $mahasiswaResponse->json() ?: [];
                Log::info('Mahasiswa data fetched successfully', [
                    'count' => count($mahasiswas), 
                    'sample_data' => array_slice($mahasiswas, 0, 2),
                    'response_status' => $mahasiswaResponse->status(),
                    'response_body_length' => strlen($mahasiswaResponse->body())
                ]);
            } else {
                Log::error('Failed to fetch mahasiswa data', [
                    'status' => $mahasiswaResponse->status(),
                    'body' => $mahasiswaResponse->body(),
                    'headers' => $mahasiswaResponse->headers()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching mahasiswa for aktivitas:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return view('aktivitas', compact('aktivitas', 'koleksis', 'mahasiswas', 'availableTopics'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nrm' => 'required|string|max:12',
            'kode' => 'required|string|max:50',
            'tanggal_peminjaman' => 'required|date',
            'jatuh_tempo' => 'required|date|after_or_equal:tanggal_peminjaman',
        ]);

        // Generate ID aktivitas
        $id_aktivitas = 'AKT' . date('YmdHis') . rand(100, 999);

        // Siapkan data untuk dikirim ke backend
        $data = [
            'id_aktivitas' => $id_aktivitas,
            'kode' => $request->input('kode'),
            'nrm' => $request->input('nrm'),
            'tanggal_peminjaman' => $request->input('tanggal_peminjaman'),
            'jatuh_tempo' => $request->input('jatuh_tempo'),
            'kategori' => 'Buku', // Pastikan kategori selalu 'Buku' untuk aktivitas
            'topik' => $request->input('topik'), // Ambil topik dari form
        ];

        try {
            Log::info('Attempting to create aktivitas with data:', $data);
            
            // Kirim data ke backend API
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post('http://backend:5000/api/aktivitas', $data);
            
            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Aktivitas berhasil ditambahkan ke backend', [
                    'data' => $data,
                    'response' => $responseData
                ]);
                
                // Cek apakah status koleksi berhasil diupdate
                if (isset($responseData['koleksi_status_updated']) && $responseData['koleksi_status_updated']) {
                    Log::info('Status koleksi berhasil diupdate menjadi: ' . $responseData['new_status']);
                } else {
                    Log::warning('Status koleksi mungkin tidak terupdate', ['response' => $responseData]);
                }
                
                return redirect()->route('aktivitas.index')->with('success', 'Aktivitas berhasil ditambahkan');
            } else {
                Log::error('Gagal menambahkan aktivitas ke backend', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'data' => $data
                ]);
                
                $errorMessage = 'Gagal menambahkan aktivitas: ' . $response->body();
                return redirect()->back()->withErrors(['error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            Log::error('Exception saat menambahkan aktivitas ke backend', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat menambahkan aktivitas: ' . $e->getMessage();
            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }
    }

    public function show($id_aktivitas)
    {
        try {
            // Ambil data aktivitas berdasarkan ID dari backend API
            $response = Http::timeout(30)->get("http://backend:5000/api/aktivitas/{$id_aktivitas}");
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Aktivitas tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching aktivitas:', ['id_aktivitas' => $id_aktivitas, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    public function updateStatus($id_aktivitas)
    {
        try {
            // Ambil status dari request
            $status = request()->json('status') ?? request()->input('status');
            
            if (!$status) {
                Log::error('Status tidak ditemukan dalam request', [
                    'id_aktivitas' => $id_aktivitas,
                    'request_body' => request()->all(),
                    'json_data' => request()->json()
                ]);
                return response()->json(['error' => 'Status tidak ditemukan'], 400);
            }

            Log::info('Attempting to update status', [
                'id_aktivitas' => $id_aktivitas,
                'status' => $status
            ]);

            // Validasi status yang diizinkan
            $allowedStatuses = ['dipinjam', 'dikembalikan'];
            if (!in_array($status, $allowedStatuses)) {
                Log::error('Status tidak valid', [
                    'id_aktivitas' => $id_aktivitas,
                    'status' => $status,
                    'allowed_statuses' => $allowedStatuses
                ]);
                return response()->json(['error' => 'Status tidak valid'], 400);
            }



            // Kirim request ke backend API untuk update status
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->patch("http://backend:5000/api/aktivitas/{$id_aktivitas}/status", [
                    'status' => $status
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Status aktivitas berhasil diupdate via backend API', [
                    'id_aktivitas' => $id_aktivitas,
                    'status' => $status,
                    'response' => $responseData
                ]);

                // Jika request AJAX, return JSON
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Status berhasil diupdate',
                        'data' => $responseData['data'] ?? null
                    ]);
                }
                
                return redirect()->route('aktivitas.index')->with('success', 'Status aktivitas berhasil diupdate menjadi ' . $status);
            } else {
                Log::error('Gagal mengupdate status aktivitas via backend API', [
                    'id_aktivitas' => $id_aktivitas,
                    'status' => $status,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                $errorMessage = 'Gagal mengupdate status aktivitas: ' . $response->body();
                
                if (request()->expectsJson()) {
                    return response()->json(['error' => $errorMessage], $response->status());
                }
                
                return redirect()->back()->withErrors(['error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            Log::error('Exception saat mengupdate status aktivitas', [
                'id_aktivitas' => $id_aktivitas,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat mengupdate status aktivitas: ' . $e->getMessage();
            
            if (request()->expectsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }
    }
}