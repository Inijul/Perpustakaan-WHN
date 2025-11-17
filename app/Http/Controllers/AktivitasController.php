<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Aktivitas;
use App\Models\Koleksi;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class AktivitasController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data aktivitas langsung dari database dengan Eloquent
            Log::info('Fetching aktivitas from database...');
            
            $aktivitasCollection = Aktivitas::with(['mahasiswa', 'koleksi'])
                ->select('aktivitas.*')
                ->orderBy('tanggal_peminjaman', 'desc')
                ->get();
            
            // Transform data ke format array yang sesuai dengan view
            $aktivitas = $aktivitasCollection->map(function($item) {
                return [
                    'id_aktivitas' => $item->id_aktivitas,
                    'kode' => $item->kode,
                    'nrm' => $item->nrm,
                    'tanggal_peminjaman' => $item->tanggal_peminjaman,
                    'jatuh_tempo' => $item->jatuh_tempo,
                    'status_aktivitas' => $item->status,
                    'nama_mahasiswa' => $item->mahasiswa->namam ?? '-',
                    'judul_buku' => $item->koleksi->judul ?? '-',
                    'kategori' => $item->koleksi->kategori ?? '-',
                    'topik' => $item->koleksi->topik ?? '-',
                ];
            })->toArray();
            
            Log::info('Aktivitas data received:', [
                'count' => count($aktivitas),
                'sample' => array_slice($aktivitas, 0, 2)
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
        } catch (\Exception $e) {
            $aktivitas = [];
            Log::error('Exception while fetching aktivitas:', ['error' => $e->getMessage()]);
        }

        // Ambil data koleksi buku saja untuk dropdown (hanya yang tersedia)
        $koleksis = [];
        $availableTopics = [];
        try {
            Log::info('Attempting to fetch koleksi buku data from database...');
            
            // Query dengan status dinamis berdasarkan aktivitas
            $koleksis = DB::table('koleksi as k')
                ->select(
                    'k.*',
                    DB::raw("CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM aktivitas a 
                            WHERE a.kode = k.kode 
                            AND a.status = 'dipinjam'
                        ) THEN 'Dipinjam'
                        ELSE 'Tersedia'
                    END as status")
                )
                ->where('k.kategori', 'Buku')
                ->having('status', 'Tersedia')
                ->get()
                ->map(function($item) {
                    return (array) $item; // Convert stdClass ke array
                })
                ->toArray();
            
            // Ekstrak topik yang tersedia dari buku
            $topics = array_filter(array_column($koleksis, 'topik'));
            $availableTopics = array_unique($topics);
            sort($availableTopics);
            
            Log::info('Koleksi buku data fetched successfully', [
                'buku_count' => count($koleksis),
                'available_topics' => $availableTopics,
                'available_topics_count' => count($availableTopics)
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while fetching koleksi buku:', [
                'error' => $e->getMessage()
            ]);
        }

        // Ambil data mahasiswa untuk dropdown
        $mahasiswas = [];
        try {
            Log::info('Attempting to fetch mahasiswa data from database...');
            
            $mahasiswas = Mahasiswa::select('nrm', 'nim', 'namam')
                ->orderBy('namam')
                ->get()
                ->toArray();
            
            Log::info('Mahasiswa data fetched successfully', ['count' => count($mahasiswas)]);
        } catch (\Exception $e) {
            Log::error('Exception while fetching mahasiswa:', [
                'error' => $e->getMessage()
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

        try {
            Log::info('Attempting to create aktivitas', ['id_aktivitas' => $id_aktivitas]);
            
            DB::beginTransaction();
            
            // Cek apakah buku masih dipinjam
            $existingAktivitas = Aktivitas::where('kode', $request->kode)
                ->where('status', 'dipinjam')
                ->first();
            
            if ($existingAktivitas) {
                DB::rollBack();
                Log::warning('Buku masih dipinjam', ['kode' => $request->kode]);
                return redirect()->back()->withErrors([
                    'error' => 'Buku masih dipinjam dan belum dikembalikan. Tidak bisa dipinjam lagi.'
                ]);
            }
            
            // Cek apakah koleksi ada
            $koleksi = Koleksi::where('kode', $request->kode)->first();
            if (!$koleksi) {
                DB::rollBack();
                Log::error('Koleksi tidak ditemukan', ['kode' => $request->kode]);
                return redirect()->back()->withErrors(['error' => 'Koleksi tidak ditemukan']);
            }
            
            // Insert aktivitas dengan status 'dipinjam'
            $aktivitas = Aktivitas::create([
                'id_aktivitas' => $id_aktivitas,
                'kode' => $request->kode,
                'nrm' => $request->nrm,
                'tanggal_peminjaman' => $request->tanggal_peminjaman,
                'jatuh_tempo' => $request->jatuh_tempo,
                'status' => 'dipinjam'
            ]);
            
            DB::commit();
            
            Log::info('Aktivitas berhasil ditambahkan', [
                'id_aktivitas' => $id_aktivitas,
                'kode' => $request->kode
            ]);
            
            return redirect()->route('aktivitas.index')->with('success', 'Aktivitas berhasil ditambahkan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception saat menambahkan aktivitas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withErrors([
                'error' => 'Terjadi kesalahan saat menambahkan aktivitas: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id_aktivitas)
    {
        try {
            // Ambil data aktivitas berdasarkan ID dari database
            $aktivitas = Aktivitas::with(['mahasiswa', 'koleksi'])
                ->where('id_aktivitas', $id_aktivitas)
                ->first();
            
            if (!$aktivitas) {
                return response()->json(['error' => 'Aktivitas tidak ditemukan'], 404);
            }
            
            $data = [
                'id_aktivitas' => $aktivitas->id_aktivitas,
                'kode' => $aktivitas->kode,
                'nrm' => $aktivitas->nrm,
                'tanggal_peminjaman' => $aktivitas->tanggal_peminjaman,
                'jatuh_tempo' => $aktivitas->jatuh_tempo,
                'status_aktivitas' => $aktivitas->status,
                'nama_mahasiswa' => $aktivitas->mahasiswa->namam ?? '-',
                'judul_buku' => $aktivitas->koleksi->judul ?? '-',
                'kategori' => $aktivitas->koleksi->kategori ?? '-',
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('Exception while fetching aktivitas:', [
                'id_aktivitas' => $id_aktivitas,
                'error' => $e->getMessage()
            ]);
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



            // Update status langsung di database
            DB::beginTransaction();
            
            // Cari aktivitas
            $aktivitas = Aktivitas::where('id_aktivitas', $id_aktivitas)->first();
            
            if (!$aktivitas) {
                DB::rollBack();
                Log::error('Aktivitas tidak ditemukan', ['id_aktivitas' => $id_aktivitas]);
                return response()->json(['error' => 'Aktivitas tidak ditemukan'], 404);
            }
            
            // Update status
            $aktivitas->status = $status;
            $aktivitas->save();
            
            DB::commit();
            
            // Ambil data dengan relasi untuk response
            $aktivitas = Aktivitas::with(['mahasiswa', 'koleksi'])
                ->where('id_aktivitas', $id_aktivitas)
                ->first();
            
            $responseData = [
                'id_aktivitas' => $aktivitas->id_aktivitas,
                'kode' => $aktivitas->kode,
                'nrm' => $aktivitas->nrm,
                'tanggal_peminjaman' => $aktivitas->tanggal_peminjaman,
                'jatuh_tempo' => $aktivitas->jatuh_tempo,
                'status_aktivitas' => $aktivitas->status,
                'nama_mahasiswa' => $aktivitas->mahasiswa->namam ?? '-',
                'judul_buku' => $aktivitas->koleksi->judul ?? '-',
                'kategori' => $aktivitas->koleksi->kategori ?? '-',
            ];
            
            Log::info('Status aktivitas berhasil diupdate', [
                'id_aktivitas' => $id_aktivitas,
                'status' => $status
            ]);

            // Jika request AJAX, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diupdate',
                    'data' => $responseData
                ]);
            }
            
            return redirect()->route('aktivitas.index')
                ->with('success', 'Status aktivitas berhasil diupdate menjadi ' . $status);
        } catch (\Exception $e) {
            DB::rollBack();
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