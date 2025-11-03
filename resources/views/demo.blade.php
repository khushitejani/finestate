<canvas id="cryptoChart" width="800" height="400"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('/demo')
.then(res => res.json())
.then(data => {
    const labels = Object.keys(data.chart);
    const prices = Object.values(data.chart);

    if(labels.length === 0) {
        console.error('No chart data found');
        return;
    }

    new Chart(document.getElementById('cryptoChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: data.crypto + ' Price',
                data: prices,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Time' } },
                y: { title: { display: true, text: 'Price (ETH)' } }
            }
        }
    });
});
</script>
