<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KoleksiController extends Controller
{
    public function index()
    {
        try {
            // Ambil data koleksi dari backend API dengan timeout dan cache
            $response = Http::timeout(30)
                ->withHeaders([
                    'Cache-Control' => 'max-age=60', // Cache selama 1 menit
                    'Accept' => 'application/json'
                ])
                ->get('http://backend:5000/api/koleksi');
            
            if ($response->successful()) {
                $koleksis = $response->json() ?: [];
                Log::info('Fetched koleksi data:', ['count' => count($koleksis), 'timestamp' => now()]);
            } else {
                $koleksis = [];
                Log::error('Failed to fetch koleksi:', ['status' => $response->status(), 'body' => $response->body()]);
            }
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
            // Kirim data ke backend API dengan timeout yang lebih lama
            Log::info('Sending data to backend API', ['url' => 'http://backend:5000/api/koleksi', 'data' => $data]);
            
            $response = Http::timeout(60)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post('http://backend:5000/api/koleksi', $data);
            
            Log::info('Backend response received', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);
            
            if ($response->successful()) {
                Log::info('Koleksi berhasil ditambahkan ke backend', ['data' => $data]);
                return redirect()->route('koleksi.index')->with('success', 'Koleksi berhasil ditambahkan');
            } else {
                Log::error('Gagal menambahkan koleksi ke backend', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'data' => $data
                ]);
                
                $errorMessage = 'Gagal menambahkan koleksi: ' . $response->body();
                return redirect()->back()->withErrors(['error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            Log::error('Exception saat menambahkan koleksi ke backend', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat menambahkan koleksi: ' . $e->getMessage();
            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }
    }

    public function edit($kode)
    {
        try {
            // Ambil data koleksi berdasarkan kode dari backend API
            $response = Http::timeout(30)->get("http://backend:5000/api/koleksi/{$kode}");
            
            if ($response->successful()) {
                $koleksi = $response->json();
                // Untuk edit, kita perlu mengambil semua koleksi juga untuk tampilan list
                $allResponse = Http::timeout(30)->get('http://backend:5000/api/koleksi');
                $koleksis = $allResponse->successful() ? $allResponse->json() : [];
                
                return view('koleksi', compact('koleksis', 'koleksi'));
            } else {
                Log::error('Failed to fetch koleksi for edit:', ['kode' => $kode, 'status' => $response->status()]);
                return redirect()->route('koleksi.index')->withErrors(['error' => 'Koleksi tidak ditemukan']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching koleksi for edit:', ['kode' => $kode, 'error' => $e->getMessage()]);
            return redirect()->route('koleksi.index')->withErrors(['error' => 'Terjadi kesalahan saat mengambil data koleksi']);
        }
    }

    public function update(Request $request, $kode)
    {
        // Siapkan data untuk dikirim ke backend
        $data = [
            'kategori' => $request->input('kategori'),
            'topik' => $request->input('topik'),
            'judul' => $request->input('judul'),
            'penulis' => $request->input('penulis'),
            'penerbit' => $request->input('penerbit'),
            'tahun_terbit' => $request->input('tahun_terbit'),
            'lokasi_rak' => $request->input('lokasi_rak'),
            'deskripsi' => $request->input('deskripsi'),
        ];

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
            // Update data koleksi ke backend API (Node.js)
            $response = Http::timeout(30)->put("http://backend:5000/api/koleksi/{$kode}", $data);

            if ($response->successful()) {
                Log::info('Koleksi berhasil diperbarui di backend', ['kode' => $kode]);
                return response()->json(['success' => true, 'message' => 'Koleksi berhasil diperbarui']);
            } else {
                Log::error('Gagal memperbarui koleksi di backend', [
                    'kode' => $kode,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return response()->json(['success' => false, 'message' => 'Gagal memperbarui koleksi'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Exception saat memperbarui koleksi di backend', ['kode' => $kode, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui koleksi'], 500);
        }
    }

    public function show($kode)
    {
        try {
            // Ambil data koleksi berdasarkan kode dari backend API
            $response = Http::timeout(30)->get("http://backend:5000/api/koleksi/{$kode}");
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Koleksi tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching koleksi:', ['kode' => $kode, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    public function destroy($kode)
    {
        try {
            // Hapus data koleksi dari backend API
            $response = Http::timeout(30)->delete("http://backend:5000/api/koleksi/{$kode}");
            
            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Koleksi berhasil dihapus']);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus koleksi'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Exception while deleting koleksi:', ['kode' => $kode, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus koleksi'], 500);
        }
    }
}
