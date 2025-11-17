<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Koleksi;
use App\Models\Aktivitas;

class KoleksiController extends Controller
{
    public function index()
    {
        try {
            // Ambil data koleksi langsung dari database dengan status dinamis
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
                ->orderBy('k.kode')
                ->get()
                ->map(function($item) {
                    return (array) $item; // Convert stdClass ke array
                })
                ->toArray();
            
            Log::info('Fetched koleksi data with status:', [
                'count' => count($koleksis),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $koleksis = [];
            Log::error('Exception while fetching koleksi:', ['error' => $e->getMessage()]);
        }

        return view('koleksi', compact('koleksis'));
    }

    public function store(Request $request)
    {
        // Log request data untuk debugging
        Log::info('Store request received', [
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('sampul'),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        // Siapkan data untuk dikirim ke backend
        $data = [
            'kategori' => $request->input('kategori'),
            'kode' => $request->input('kode'),
            'topik' => $request->input('topik'),
            'judul' => $request->input('judul'),
            'penulis' => $request->input('penulis'),
            'penerbit' => $request->input('penerbit'),
            'tahun_terbit' => $request->input('tahun_terbit'),
            'lokasi_rak' => $request->input('lokasi_rak'),
            'deskripsi' => $request->input('deskripsi'),
            'tautan' => $request->input('tautan'),
        ];

        Log::info('Prepared data for backend', ['data' => $data]);

        // Handle upload file sampul jika ada
         if ($request->hasFile('sampul') && $request->file('sampul')->isValid()) {
        $file = $request->file('sampul');
        $fileName = time() . '_' . $file->getClientOriginalName();

        $filePath = Storage::disk('public')->putFileAs('sampul', $file, $fileName);
        if ($filePath) {
            $data['sampul'] = $fileName;
            Log::info('File uploaded', ['path' => $filePath]);
        } else {
            Log::error('Failed to upload file');
        }
    } else {
        // Pakai sampul lama kalau tidak upload file baru
        $data['sampul'] = $request->input('sampul_lama');
    }

        try {
            // Insert langsung ke database
            Log::info('Inserting data to database', ['data' => $data]);
            
            $koleksi = Koleksi::create($data);
            
            Log::info('Koleksi berhasil ditambahkan', ['kode' => $data['kode']]);
            
            return redirect()->route('koleksi.index')->with('success', 'Koleksi berhasil ditambahkan');
            
        } catch (\Exception $e) {
            Log::error('Exception saat menambahkan koleksi', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            return redirect()->back()->withErrors([
                'error' => 'Terjadi kesalahan saat menambahkan koleksi: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($kode)
    {
        try {
            // Ambil data koleksi berdasarkan kode
            $koleksi = Koleksi::where('kode', $kode)->first();
            
            if (!$koleksi) {
                return redirect()->route('koleksi.index')
                    ->withErrors(['error' => 'Koleksi tidak ditemukan']);
            }
            
            // Ambil semua koleksi untuk tampilan list
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
                ->orderBy('k.kode')
                ->get()
                ->map(function($item) {
                    return (array) $item; // Convert stdClass ke array
                })
                ->toArray();
            
            return view('koleksi', compact('koleksis', 'koleksi'));
            
        } catch (\Exception $e) {
            Log::error('Exception while fetching koleksi for edit:', [
                'kode' => $kode,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('koleksi.index')
                ->withErrors(['error' => 'Terjadi kesalahan saat mengambil data koleksi']);
        }
    }

    public function update(Request $request, $kode)
    {
        // Validasi ringan agar error tampil jelas (bukan 500)
        $validated = $request->validate([
            'kategori' => 'nullable|in:buku,jurnal,skripsi',
            'topik' => 'nullable|string',
            'judul' => 'nullable|string',
            'penulis' => 'nullable|string',
            'penerbit' => 'nullable|string',
            'tahun_terbit' => 'nullable|digits_between:1,4',
            'lokasi_rak' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'tautan' => 'nullable|string',
        ]);

        // Siapkan data update
        $data = [
            'kategori' => $validated['kategori'] ?? $request->input('kategori'),
            'topik' => $validated['topik'] ?? $request->input('topik'),
            'judul' => $validated['judul'] ?? $request->input('judul'),
            'penulis' => $validated['penulis'] ?? $request->input('penulis'),
            'penerbit' => $validated['penerbit'] ?? $request->input('penerbit'),
            'tahun_terbit' => $validated['tahun_terbit'] ?? $request->input('tahun_terbit'),
            'lokasi_rak' => $validated['lokasi_rak'] ?? $request->input('lokasi_rak'),
            'deskripsi' => $validated['deskripsi'] ?? $request->input('deskripsi'),
            'tautan' => $validated['tautan'] ?? $request->input('tautan'),
        ];

        // Jika kategori bukan "buku", set topik ke '-' agar konsisten
        if (($data['kategori'] ?? '') !== 'buku') {
            $data['topik'] = '-';
        }

        // Normalisasi nilai tahun_terbit kosong -> null
        if (isset($data['tahun_terbit']) && $data['tahun_terbit'] === '') {
            $data['tahun_terbit'] = null;
        }

        // Handle upload file sampul jika ada
        // Handle upload file sampul
        if ($request->hasFile('sampul') && $request->file('sampul')->isValid()) {
            $file = $request->file('sampul');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $filePath = Storage::disk('public')->putFileAs('sampul', $file, $fileName);
            if ($filePath) {
                $data['sampul'] = $fileName;
                Log::info('File uploaded', ['path' => $filePath]);
            } else {
                Log::error('Failed to upload file');
            }
        } else {
            // Tidak upload → pakai sampul lama
            $data['sampul'] = $request->input('sampul_lama');
        }

        try {
            Log::info('Updating koleksi', ['kode' => $kode, 'data' => $data]);
            // Update langsung ke database
            $koleksi = Koleksi::where('kode', $kode)->first();
            
            if (!$koleksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koleksi tidak ditemukan'
                ], 404);
            }
            
            $koleksi->update($data);
            
            Log::info('Koleksi berhasil diperbarui', ['kode' => $kode]);
            
            return response()->json([
                'success' => true,
                'message' => 'Koleksi berhasil diperbarui',
                'data' => $koleksi->refresh()->toArray(), // kirim data terbaru (termasuk sampul)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Kembalikan error validasi sebagai 422 untuk ditangani di frontend
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception saat memperbarui koleksi', [
                'kode' => $kode,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui koleksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($kode)
    {
        try {
            // Ambil koleksi dengan status dinamis
            $koleksi = DB::table('koleksi as k')
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
                ->where('k.kode', $kode)
                ->first();
            
            if (!$koleksi) {
                return response()->json(['error' => 'Koleksi tidak ditemukan'], 404);
            }
            
            // Convert stdClass ke array untuk konsistensi
            return response()->json((array) $koleksi);
            
        } catch (\Exception $e) {
            Log::error('Exception while fetching koleksi:', [
                'kode' => $kode,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    public function destroy($kode)
    {
        try {
            Log::info('Delete koleksi request received', ['kode' => $kode]);
            
            // Cek apakah koleksi ada
            $koleksi = Koleksi::where('kode', $kode)->first();
            
            if (!$koleksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koleksi tidak ditemukan'
                ], 404);
            }
            
            // Cek apakah masih ada aktivitas dengan status 'dipinjam'
            $aktivitasDipinjam = Aktivitas::where('kode', $kode)
                ->where('status', 'dipinjam')
                ->exists();
            
            if ($aktivitasDipinjam) {
                Log::warning('Cannot delete koleksi, still has active aktivitas', ['kode' => $kode]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus koleksi karena masih ada aktivitas yang aktif (dipinjam). Silakan kembalikan buku terlebih dahulu.'
                ], 400);
            }
            
            // Hapus semua aktivitas terkait (yang sudah dikembalikan)
            Aktivitas::where('kode', $kode)->delete();
            
            // Hapus koleksi
            $koleksi->delete();
            
            Log::info('Koleksi berhasil dihapus', ['kode' => $kode]);
            
            return response()->json([
                'success' => true,
                'message' => 'Koleksi berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception while deleting koleksi:', [
                'kode' => $kode,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus koleksi'
            ], 500);
        }
    }
}
