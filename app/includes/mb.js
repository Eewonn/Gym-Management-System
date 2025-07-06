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
                    borderColor: 'rgba(75, 192, 192, 1)',
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
        }); // ✅ Close new Chart here
    })
    .catch(error => console.error('Error fetching data:', error)); 
