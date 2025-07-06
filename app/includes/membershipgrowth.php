<div class="p-6 bg-[#222121] border border-[#585757] rounded-lg shadow-lg m-4 mb-4 flex flex-col items-center mt-4 w-1/2 max-w-lg" style="height: 400px;">
    <h2 class="text-2xl font-bold mb-4 text-white text-left">Membership Growth</h2>
    <canvas id="membershipChart" style="max-height: 320px; width: 100%;"></canvas>
</div>

<script>
fetch('app/includes/memberdata.php')
    .then(response => response.json())
    .then(data => {
        const labels = data.map(item => item.join_date);
        const totals = data.map(item => item.total_members);

        const ctx = document.getElementById('membershipChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Members',
                    data: totals,
                    fill: false,
                    borderColor: '#800080',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Members'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Join Date'
                        }
                    }
                }
            }
        });
    });
</script>
