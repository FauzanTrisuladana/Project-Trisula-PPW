// chart Anggota
function makechart(total_laki, total_perempuan, total_wajib, total_sukarela, total_pokok) {
    const ctx1 = document.getElementById('ChartAnggota').getContext('2d');
    const myChart1 = new Chart(ctx1, {
        type: 'pie', // "line", "bar", "doughnut", "pie", "radar", "polarArea", "bubble", "scatter", "horizontalBar"
        data: {
        labels: ['Laki-Laki', 'Perempuan'],
        datasets: [{
            label: 'Anggota',
            data: [total_laki, total_perempuan],
            backgroundColor: [
            'rgba(75, 192, 192, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            ],
            borderColor: 'rgba(255, 255, 255, 1)',
            borderWidth: 1
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
            position: 'top',
            }
        }
        }
    });
    // chart simpanan
    const ctx2 = document.getElementById('ChartSimpanan').getContext('2d');
    const myChart2 = new Chart(ctx2, {
        type: 'doughnut',
        data: {
        labels: ['Simpanan Wajib', 'Simpanan Sukarela', 'Simpanan Pokok'],
        datasets: [{
            label: 'Simpanan',
            data: [total_wajib,
            total_sukarela, 
            total_pokok],
            backgroundColor: [
            'rgba(75, 192, 192, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            ],
            borderColor: 'rgba(255, 255, 255, 1)',
            borderWidth: 1
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
            position: 'top',
            }
        }
        }
    });
}
