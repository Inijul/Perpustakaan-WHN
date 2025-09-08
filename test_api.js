// Test script sederhana untuk memverifikasi status koleksi
const testStatusUpdate = async () => {
    console.log('🚀 Testing Status Update...\n');
    
    try {
        // Test 1: Cek status awal koleksi BK001
        console.log('1. Cek status awal koleksi BK001...');
        const koleksiResponse = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse.ok) {
            const koleksiData = await koleksiResponse.json();
            console.log('   Status awal:', koleksiData.status);
        }
        
        // Test 2: Update status aktivitas menjadi dikembalikan
        console.log('\n2. Update status aktivitas menjadi dikembalikan...');
        const updateResponse = await fetch('http://localhost:5000/api/aktivitas/AKT20250120123456/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'dikembalikan' })
        });
        
        if (updateResponse.ok) {
            const updateResult = await updateResponse.json();
            console.log('   ✅ Status berhasil diupdate:', updateResult.message);
        } else {
            console.log('   ❌ Gagal update status:', updateResponse.status);
        }
        
        // Test 3: Cek status koleksi setelah update
        console.log('\n3. Cek status koleksi setelah update...');
        const koleksiResponse2 = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse2.ok) {
            const koleksiData2 = await koleksiResponse2.json();
            console.log('   Status setelah update:', koleksiData2.status);
            
            if (koleksiData2.status === 'Tersedia') {
                console.log('   ✅ Status berhasil berubah menjadi Tersedia');
            } else {
                console.log('   ❌ Status masih:', koleksiData2.status);
            }
        }
        
        // Test 4: Refresh dan cek lagi (simulasi refresh)
        console.log('\n4. Simulasi refresh - cek status lagi...');
        const koleksiResponse3 = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse3.ok) {
            const koleksiData3 = await koleksiResponse3.json();
            console.log('   Status setelah refresh:', koleksiData3.status);
            
            if (koleksiData3.status === 'Tersedia') {
                console.log('   ✅ Status tetap Tersedia setelah refresh - SOLUSI BERHASIL!');
            } else {
                console.log('   ❌ Status berubah kembali menjadi:', koleksiData3.status);
            }
        }
        
    } catch (error) {
        console.error('❌ Error during testing:', error);
    }
};

// Test script untuk memverifikasi perbaikan status aktivitas dan koleksi
const testStatusConsistencyFix = async () => {
    console.log('🚀 Testing Status Consistency Fix...\n');
    
    try {
        // Test 1: Cek status awal koleksi BK001
        console.log('1. Cek status awal koleksi BK001...');
        const koleksiResponse = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse.ok) {
            const koleksiData = await koleksiResponse.json();
            console.log('   Status awal:', koleksiData.status);
        }
        
        // Test 2: Cek aktivitas yang ada untuk BK001
        console.log('\n2. Cek aktivitas yang ada untuk BK001...');
        const aktivitasResponse = await fetch('http://localhost:5000/api/aktivitas');
        if (aktivitasResponse.ok) {
            const aktivitasList = await aktivitasResponse.json();
            const aktivitasBK001 = aktivitasList.filter(a => a.kode === 'BK001');
            console.log('   Total aktivitas untuk BK001:', aktivitasBK001.length);
            
            aktivitasBK001.forEach((aktivitas, index) => {
                console.log(`   Aktivitas ${index + 1}:`, aktivitas.id_aktivitas, '- Status:', aktivitas.status_buku);
            });
        }
        
        // Test 3: Update status aktivitas menjadi dikembalikan
        console.log('\n3. Update status aktivitas menjadi dikembalikan...');
        const updateResponse = await fetch('http://localhost:5000/api/aktivitas/AKT20250120123456/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'dikembalikan' })
        });
        
        if (updateResponse.ok) {
            const updateResult = await updateResponse.json();
            console.log('   ✅ Status berhasil diupdate:', updateResult.message);
        } else {
            console.log('   ❌ Gagal update status:', updateResponse.status);
        }
        
        // Test 4: Cek status koleksi setelah update
        console.log('\n4. Cek status koleksi setelah update...');
        const koleksiResponse2 = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse2.ok) {
            const koleksiData2 = await koleksiResponse2.json();
            console.log('   Status setelah update:', koleksiData2.status);
            
            if (koleksiData2.status === 'Tersedia') {
                console.log('   ✅ Status berhasil berubah menjadi Tersedia');
            } else {
                console.log('   ❌ Status masih:', koleksiData2.status);
            }
        }
        
        // Test 5: Cek aktivitas setelah update (harus tetap konsisten)
        console.log('\n5. Cek aktivitas setelah update...');
        const aktivitasResponse2 = await fetch('http://localhost:5000/api/aktivitas');
        if (aktivitasResponse2.ok) {
            const aktivitasList2 = await aktivitasResponse2.json();
            const aktivitasBK001_2 = aktivitasList2.filter(a => a.kode === 'BK001');
            
            aktivitasBK001_2.forEach((aktivitas, index) => {
                console.log(`   Aktivitas ${index + 1}:`, aktivitas.id_aktivitas, '- Status:', aktivitas.status_buku);
            });
        }
        
        // Test 6: Simulasi refresh - cek status lagi
        console.log('\n6. Simulasi refresh - cek status lagi...');
        const koleksiResponse3 = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse3.ok) {
            const koleksiData3 = await koleksiResponse3.json();
            console.log('   Status setelah refresh:', koleksiData3.status);
            
            if (koleksiData3.status === 'Tersedia') {
                console.log('   ✅ Status tetap Tersedia setelah refresh - PERBAIKAN BERHASIL!');
            } else {
                console.log('   ❌ Status berubah kembali menjadi:', koleksiData3.status);
                console.log('   🔍 Masih ada masalah di logika lain');
            }
        }
        
        // Test 7: Cek apakah ada aktivitas lain yang masih aktif
        console.log('\n7. Cek aktivitas yang masih aktif untuk BK001...');
        const aktivitasResponse3 = await fetch('http://localhost:5000/api/aktivitas');
        if (aktivitasResponse3.ok) {
            const aktivitasList3 = await aktivitasResponse3.json();
            const aktivitasBK001_3 = aktivitasList3.filter(a => a.kode === 'BK001');
            console.log('   Total aktivitas untuk BK001:', aktivitasBK001_3.length);
            
            aktivitasBK001_3.forEach((aktivitas, index) => {
                console.log(`   Aktivitas ${index + 1}:`, aktivitas.id_aktivitas, '- Status:', aktivitas.status_buku);
            });
        }
        
    } catch (error) {
        console.error('❌ Error during testing:', error);
    }
};

// Test script untuk memverifikasi status koleksi tidak berubah kembali
const testStatusConsistency = async () => {
    console.log('🚀 Testing Status Consistency...\n');
    
    try {
        // Test 1: Cek status awal koleksi BK001
        console.log('1. Cek status awal koleksi BK001...');
        const koleksiResponse = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse.ok) {
            const koleksiData = await koleksiResponse.json();
            console.log('   Status awal:', koleksiData.status);
        }
        
        // Test 2: Update status aktivitas menjadi dikembalikan
        console.log('\n2. Update status aktivitas menjadi dikembalikan...');
        const updateResponse = await fetch('http://localhost:5000/api/aktivitas/AKT20250120123456/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'dikembalikan' })
        });
        
        if (updateResponse.ok) {
            const updateResult = await updateResponse.json();
            console.log('   ✅ Status berhasil diupdate:', updateResult.message);
        } else {
            console.log('   ❌ Gagal update status:', updateResponse.status);
        }
        
        // Test 3: Cek status koleksi setelah update
        console.log('\n3. Cek status koleksi setelah update...');
        const koleksiResponse2 = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse2.ok) {
            const koleksiData2 = await koleksiResponse2.json();
            console.log('   Status setelah update:', koleksiData2.status);
            
            if (koleksiData2.status === 'Tersedia') {
                console.log('   ✅ Status berhasil berubah menjadi Tersedia');
            } else {
                console.log('   ❌ Status masih:', koleksiData2.status);
            }
        }
        
        // Test 4: Simulasi refresh - cek status lagi
        console.log('\n4. Simulasi refresh - cek status lagi...');
        const koleksiResponse3 = await fetch('http://localhost:5000/api/koleksi/BK001');
        if (koleksiResponse3.ok) {
            const koleksiData3 = await koleksiResponse3.json();
            console.log('   Status setelah refresh:', koleksiData3.status);
            
            if (koleksiData3.status === 'Tersedia') {
                console.log('   ✅ Status tetap Tersedia setelah refresh - SOLUSI BERHASIL!');
            } else {
                console.log('   ❌ Status berubah kembali menjadi:', koleksiData3.status);
                console.log('   🔍 Masih ada masalah di logika lain');
            }
        }
        
        // Test 5: Cek apakah ada aktivitas lain yang masih aktif
        console.log('\n5. Cek aktivitas yang masih aktif untuk BK001...');
        const aktivitasResponse = await fetch('http://localhost:5000/api/aktivitas');
        if (aktivitasResponse.ok) {
            const aktivitasList = await aktivitasResponse.json();
            const aktivitasBK001 = aktivitasList.filter(a => a.kode === 'BK001');
            console.log('   Total aktivitas untuk BK001:', aktivitasBK001.length);
            
            aktivitasBK001.forEach((aktivitas, index) => {
                console.log(`   Aktivitas ${index + 1}:`, aktivitas.id_aktivitas, '- Status:', aktivitas.status_buku);
            });
        }
        
    } catch (error) {
        console.error('❌ Error during testing:', error);
    }
};

// Run test
if (typeof window === 'undefined') {
    // Node.js environment
    const fetch = require('node-fetch');
    console.log('=== RUNNING COMPREHENSIVE STATUS CONSISTENCY TEST ===\n');
    testStatusConsistencyFix();
} else {
    // Browser environment
    console.log('=== RUNNING COMPREHENSIVE STATUS CONSISTENCY TEST ===\n');
    testStatusConsistencyFix();
}
