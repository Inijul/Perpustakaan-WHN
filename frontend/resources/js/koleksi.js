// Koleksi JavaScript Functions

// Function untuk update table row
function updateTableRow(updatedData) {
    // Update data di tabel berdasarkan kode
    const row = document.querySelector(`tr[data-kode='${updatedData.kode}']`);
    if (row) {
        // Update kategori
        const kategoriCell = row.querySelector('td:nth-child(2)');
        if (kategoriCell) {
            kategoriCell.textContent = updatedData.kategori || '';
        }
        
        // Update tahun terbit
        const tahunCell = row.querySelector('td:nth-child(4)');
        if (tahunCell) {
            tahunCell.textContent = updatedData.tahun_terbit || '';
        }
        
        // Update lokasi rak
        const lokasiCell = row.querySelector('td:nth-child(5)');
        if (lokasiCell) {
            lokasiCell.textContent = updatedData.lokasi_rak || '';
        }
        
        // Update status
        const statusCell = row.querySelector('td:nth-child(6)');
        if (statusCell) {
            if (updatedData.status === 'Dipinjam') {
                statusCell.innerHTML = '<div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#ded000]/70 text-base font-medium text-black">Dipinjam</div>';
            } else {
                statusCell.innerHTML = '<div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#00d836]/70 text-base font-medium text-black">Tersedia</div>';
            }
        }
        
        // Update data attributes untuk filter
        row.setAttribute('data-kategori', updatedData.kategori || '');
        row.setAttribute('data-tahun', updatedData.tahun_terbit || '');
        row.setAttribute('data-status', updatedData.status || '');
        row.setAttribute('data-judul', updatedData.judul || '');
    }
}

// Function untuk remove table row
function removeTableRow(kode) {
    // Remove row dari tabel berdasarkan kode
    const row = document.querySelector(`tr[data-kode='${kode}']`);
    if (row) {
        row.remove();
    }
}

// Function untuk add new table row
function addTableRow(koleksiData) {
    const tbody = document.querySelector('tbody');
    if (!tbody) return;
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-kode', koleksiData.kode || '');
    newRow.setAttribute('data-kategori', koleksiData.kategori || '');
    newRow.setAttribute('data-tahun', koleksiData.tahun_terbit || '');
    newRow.setAttribute('data-status', koleksiData.status || '');
    newRow.setAttribute('data-judul', koleksiData.judul || '');
    newRow.setAttribute('data-kode-search', koleksiData.kode || '');
    newRow.className = 'filterable-row';
    
    const statusBadge = koleksiData.status === 'Dipinjam' 
        ? '<div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#ded000]/70 text-base font-medium text-black">Dipinjam</div>'
        : '<div class="inline-flex justify-center items-center px-4 py-1 rounded-[30px] bg-[#00d836]/70 text-base font-medium text-black">Tersedia</div>';
    
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="checkbox" 
                   class="w-5 h-5 rounded-[5px] border border-black cursor-pointer checked:bg-[#024088] checked:border-[#024088]"
                   @click="toggleRow('${koleksiData.kode || ''}')"
                   :checked="checkedRows.includes('${koleksiData.kode || ''}')">
        </td>
        <td class="px-6 py-4 whitespace text-base font-medium text-black">${koleksiData.kategori || ''}</td>
        <td class="px-6 py-4 whitespace text-base font-medium text-black">${koleksiData.kode || ''}</td>
        <td class="px-6 py-4 whitespace text-base font-medium text-black">${koleksiData.tahun_terbit || ''}</td>
        <td class="px-6 py-4 whitespace text-base font-medium text-black">${koleksiData.lokasi_rak || ''}</td>
        <td class="px-6 py-4 whitespace">
            ${statusBadge}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex gap-2 justify-end">
                <button @click="window.dispatchEvent(new CustomEvent('show-detail-koleksi', { detail: ${JSON.stringify(koleksiData)} }))" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-md">
                    <svg width="30" height="37" viewBox="0 0 30 37" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-[35px]">
                        <path d="M18.5 23.75L21.125 26.375M8.875 19.375C8.875 20.7674 9.42812 22.1027 10.4127 23.0873C11.3973 24.0719 12.7326 24.625 14.125 24.625C15.5174 24.625 16.8527 24.0719 17.8373 23.0873C18.8219 22.1027 19.375 20.7674 19.375 19.375C19.375 17.9826 18.8219 16.6473 17.8373 15.6627C16.8527 14.6781 15.5174 14.125 14.125 14.125C12.7326 14.125 11.3973 14.6781 10.4127 15.6627C9.42812 16.6473 8.875 17.9826 8.875 19.375Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M1 34.95V2.05C1 1.77152 1.11062 1.50445 1.30754 1.30754C1.50445 1.11062 1.77152 1 2.05 1H22.441C22.7194 1.00025 22.9863 1.11103 23.183 1.308L28.692 6.817C28.79 6.91482 28.8676 7.03105 28.9204 7.15899C28.9733 7.28694 29.0003 7.42407 29 7.5625V34.95C29 35.0879 28.9729 35.2244 28.9201 35.3518C28.8673 35.4792 28.79 35.595 28.6925 35.6925C28.595 35.79 28.4792 35.8673 28.3518 35.9201C28.2244 35.9728 28.0879 36 27.95 36H2.05C1.91211 36 1.77557 35.9728 1.64818 35.9201C1.52079 35.8673 1.40504 35.79 1.30754 35.6925C1.21004 35.595 1.13269 35.4792 1.07993 35.3518C1.02716 35.2244 1 35.0879 1 34.95Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M22 1V6.95C22 7.22848 22.1106 7.49555 22.3075 7.69246C22.5045 7.88937 22.7715 8 23.05 8H29" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <button @click="window.dispatchEvent(new CustomEvent('show-edit-koleksi', { detail: ${JSON.stringify(koleksiData)} }))" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-md">
                    <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 cursor-pointer">
                        <path d="M26.684 5.95734L31.0426 10.3159M29.4861 2.1176L17.6951 13.9086C17.0841 14.5156 16.6683 15.2913 16.501 16.1363L15.4119 21.5881L20.8637 20.4969C21.7078 20.3281 22.4819 19.9142 23.0914 19.3048L34.8824 7.51383C35.2367 7.15951 35.5177 6.73887 35.7095 6.27592C35.9013 5.81298 36 5.3168 36 4.81571C36 4.31463 35.9013 3.81845 35.7095 3.3555C35.5177 2.89256 35.2367 2.47192 34.8824 2.1176C34.528 1.76327 34.1074 1.48221 33.6445 1.29045C33.1815 1.0987 32.6853 1 32.1842 1C31.6832 1 31.187 1.0987 30.724 1.29045C30.2611 1.48221 29.8404 1.76327 29.4861 2.1176Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M31.8827 25.7058V31.8823C31.8827 32.9744 31.4488 34.0217 30.6766 34.7939C29.9044 35.5662 28.8571 36 27.765 36H5.11769C4.02561 36 2.97826 35.5662 2.20604 34.7939C1.43383 34.0217 1 32.9744 1 31.8823V9.235C1 8.14292 1.43383 7.09557 2.20604 6.32335C2.97826 5.55114 4.02561 5.11731 5.11769 5.11731H11.2942" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </td>
    `;
    
    tbody.appendChild(newRow);
}

// Function untuk refresh filter setelah data berubah
function refreshFilter() {
    // Trigger Alpine.js filter function jika ada
    const filterComponent = document.querySelector('[x-data*="filterData"]');
    if (filterComponent && filterComponent.__x) {
        filterComponent.__x.$data.extractAvailableYears();
        filterComponent.__x.$data.extractAvailableKategoris();
        filterComponent.__x.$data.extractAvailableStatuses();
        filterComponent.__x.$data.applyFilters();
    }
}

// Export functions untuk penggunaan global
window.updateTableRow = updateTableRow;
window.removeTableRow = removeTableRow;
window.addTableRow = addTableRow;
window.refreshFilter = refreshFilter;
