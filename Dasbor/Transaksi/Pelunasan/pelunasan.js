document.addEventListener('DOMContentLoaded', function() {
    const idPinjamanInput = document.getElementById('id-pinjaman');
    const anggsuranDisplay = document.getElementById('anggsuran');
    const jumlahDisplay = document.getElementById('jumlah');

    const pinjamanData = window.pinjamanData || [];

    idPinjamanInput.addEventListener('input', function() {
        const selectedOption = this.value;
        if (selectedOption) {
            const pinjaman = pinjamanData.find(p => p.id_pinjaman_custom === selectedOption);
            if (pinjaman) {
                anggsuranDisplay.textContent = pinjaman.pelunasan_terakhir ? pinjaman.pelunasan_terakhir + 1 : 1;
                jumlahDisplay.textContent = pinjaman.nilai_pokok_angsuran.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                });
            } else {
                anggsuranDisplay.textContent = '';
                jumlahDisplay.textContent = '';
            }
        } else {
            anggsuranDisplay.textContent = '';
            jumlahDisplay.textContent = '';
        }
    });
});
