<div class="p-6 bg-[#222121] border border-[#585757] rounded-lg shadow-lg m-4 mb-4 flex flex-col items-center mt-4 w-1/2 max-w-lg" style="height: 400px;">
    <h2 class="text-2xl font-bold mb-4 text-white text-left">Total Revenue</h2>
    <canvas id="revenueChart" style="max-height: 320px; width: 100%;"></canvas>
</div>

<script>
fetch('app/includes/revenuedata.php')
    .then(response => response.json())
    .then(data => {
        const labels = data.map(item => item.payment_month);
        const revenues = data.map(item => item.total_revenue);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Revenue (₱)',
                    data: revenues,
                    backgroundColor: '#800080',
                    borderColor: '#800080',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (₱)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });
    });
</script>
