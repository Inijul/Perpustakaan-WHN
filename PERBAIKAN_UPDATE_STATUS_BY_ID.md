# Perbaikan Update Status Berdasarkan ID Aktivitas

## Masalah yang Ditemukan

**Masalah:** Meskipun kita sudah mengupdate `status_aktivitas` berdasarkan `id_aktivitas`, tapi kita masih mengupdate status koleksi yang mempengaruhi semua aktivitas untuk buku yang sama. Ini menyebabkan aktivitas yang sudah dikembalikan seolah-olah menjadi 'Dipinjam' lagi.

**Skenario Masalah:**
1. Aktivitas A: Buku B001 dipinjam → dikembalikan (`status_aktivitas` = "Dikembalikan")
2. Aktivitas B: Buku B001 dipinjam lagi (`status_aktivitas` = "Dipinjam")
3. **Masalah**: Aktivitas A seolah-olah menjadi 'Dipinjam' lagi karena status koleksi berubah

## Akar Masalah

1. **Update status koleksi**: Sistem masih mengupdate status koleksi yang mempengaruhi semua aktivitas untuk buku yang sama
2. **Status koleksi saling menimpa**: Ketika ada aktivitas baru atau update, status koleksi berubah dan mempengaruhi semua aktivitas
3. **Tidak konsisten dengan prinsip id_aktivitas**: Status aktivitas seharusnya independen berdasarkan `id_aktivitas`

## Solusi yang Diterapkan

### 1. Menghapus Update Status Koleksi

**Sebelum:**
```typescript
// Update status koleksi berdasarkan aktivitas yang masih aktif
const [otherAktivitas] = await db.query(
  "SELECT COUNT(*) as count FROM aktivitas WHERE kode = ? AND COALESCE(status_aktivitas, 'Dipinjam') = 'Dipinjam'",
  [kode]
);

const activeDipinjamCount = (otherAktivitas as any[])[0].count;

// Update status koleksi berdasarkan logika:
let newKoleksiStatus = '';
if (activeDipinjamCount > 0) {
  newKoleksiStatus = 'Dipinjam';
} else {
  newKoleksiStatus = 'Tersedia';
}

if (newKoleksiStatus !== currentKoleksiStatus) {
  await db.query(
    "UPDATE koleksi SET status = ? WHERE kode = ?",
    [newKoleksiStatus, kode]
  );
}
```

**Sesudah:**
```typescript
// Status koleksi tidak diupdate karena status aktivitas individual sudah diupdate berdasarkan id_aktivitas
// Status koleksi hanya untuk informasi saja dan tidak mempengaruhi status aktivitas individual
console.log(`Status koleksi tidak diupdate. Status aktivitas individual sudah diupdate berdasarkan id_aktivitas:`, id_aktivitas);
```

### 2. Menghapus Update Status Koleksi di Create Aktivitas

**Sebelum:**
```typescript
// Update status koleksi menjadi 'Dipinjam' (karena ada aktivitas baru yang dipinjam)
await db.query(
  "UPDATE koleksi SET status = 'Dipinjam' WHERE kode = ?",
  [kode]
);

console.log('Koleksi status updated to Dipinjam for kode:', kode, '(new aktivitas created)');
```

**Sesudah:**
```typescript
// Status koleksi tidak diupdate karena status aktivitas individual sudah diupdate berdasarkan id_aktivitas
// Status koleksi hanya untuk informasi saja dan tidak mempengaruhi status aktivitas individual
console.log('Status koleksi tidak diupdate. Status aktivitas individual sudah diupdate berdasarkan id_aktivitas:', id_aktivitas);
```

### 3. Response yang Disederhanakan

**Sebelum:**
```typescript
const responseData = { 
  message: `Status aktivitas berhasil diupdate menjadi ${status}`,
  id_aktivitas: id_aktivitas,
  kode_buku: kode,
  status_aktivitas_sebelumnya: currentAktivitasStatus,
  status_aktivitas_sekarang: status,
  status_koleksi_sebelumnya: currentKoleksiStatus,
  status_koleksi_sekarang: newKoleksiStatus,
  data: (updatedRows as any[])[0] 
};
```

**Sesudah:**
```typescript
const responseData = { 
  message: `Status aktivitas berhasil diupdate menjadi ${status}`,
  id_aktivitas: id_aktivitas,
  kode_buku: kode,
  status_aktivitas_sebelumnya: currentAktivitasStatus,
  status_aktivitas_sekarang: status,
  data: (updatedRows as any[])[0] 
};
```

## Prinsip Baru

### 1. **Status Aktivitas Berdasarkan ID Aktivitas**
- Setiap aktivitas memiliki status yang independen berdasarkan `id_aktivitas`
- Status aktivitas tidak dipengaruhi oleh aktivitas lain untuk buku yang sama
- Update status aktivitas hanya mengupdate field `status_aktivitas` di tabel `aktivitas`

### 2. **Status Koleksi untuk Informasi Saja**
- Status koleksi tidak diupdate ketika ada perubahan aktivitas
- Status koleksi hanya untuk informasi apakah buku tersedia atau dipinjam
- Status koleksi tidak mempengaruhi status aktivitas individual

### 3. **Logika Bisnis yang Konsisten**
- Buku yang sudah dikembalikan bisa dipinjam lagi
- Buku yang masih dipinjam tidak bisa dipinjam lagi
- Status aktivitas individual tetap akurat dan independen

## Keuntungan Perbaikan

### 1. **Status Individual yang Benar-benar Independen**
- Setiap aktivitas memiliki status yang benar-benar independen
- Status aktivitas tidak dipengaruhi oleh aktivitas lain
- Aktivitas yang sudah dikembalikan tetap memiliki status "Dikembalikan"

### 2. **Tidak Ada Duplikasi Status**
- Status aktivitas tidak saling menimpa antar aktivitas
- Setiap aktivitas memiliki status yang akurat berdasarkan `id_aktivitas`
- Tidak ada konflik status antar aktivitas

### 3. **Logika yang Konsisten**
- Semua operasi menggunakan `id_aktivitas` sebagai pengidentifikasi utama
- Status aktivitas diupdate berdasarkan `id_aktivitas`, bukan `kode` buku
- Sistem bekerja dengan prinsip yang konsisten

## Skenario Testing

### Skenario 1: Update Status Aktivitas
1. Aktivitas ACT001: Buku B001 dipinjam (`status_aktivitas` = "Dipinjam")
2. Update ACT001 menjadi "dikembalikan"
3. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan"
   - Status koleksi tidak berubah (tetap untuk informasi saja)

### Skenario 2: Peminjaman Ulang
1. Aktivitas ACT001: Buku B001 dipinjam → dikembalikan (`status_aktivitas` = "Dikembalikan")
2. Buat aktivitas ACT002 untuk buku B001 (`status_aktivitas` = "Dipinjam")
3. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan" (tetap)
   - ACT002: `status_aktivitas` = "Dipinjam"
   - Status koleksi tidak berubah (tetap untuk informasi saja)

### Skenario 3: Multiple Aktivitas
1. ACT001: Buku B001 dipinjam (`status_aktivitas` = "Dipinjam")
2. ACT002: Buku B001 dipinjam (`status_aktivitas` = "Dipinjam")
3. Update ACT001 menjadi "dikembalikan"
4. **Expected**: 
   - ACT001: `status_aktivitas` = "Dikembalikan"
   - ACT002: `status_aktivitas` = "Dipinjam" (tetap)
   - Status koleksi tidak berubah (tetap untuk informasi saja)

## Kesimpulan

Perbaikan ini mengatasi masalah duplikasi status dengan:
1. **Menghapus update status koleksi**: Status koleksi tidak diupdate ketika ada perubahan aktivitas
2. **Status aktivitas berdasarkan id_aktivitas**: Setiap aktivitas memiliki status yang benar-benar independen
3. **Logika yang konsisten**: Semua operasi menggunakan `id_aktivitas` sebagai pengidentifikasi utama

Sistem sekarang dapat menangani multiple aktivitas untuk buku yang sama tanpa saling menimpa status, dengan setiap aktivitas memiliki status yang benar-benar independen berdasarkan `id_aktivitas`.
