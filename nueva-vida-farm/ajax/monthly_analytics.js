// MONTHLY ANALYTICS
let myChart;

function fetchAnalyticsData(year) {
    fetch(`../functions/monthly_analytics.php?year=${year}`)
        .then(response => response.json())
        .then(data => {
            const allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            const monthsWithData = data.map(item => item.month);
            const sales = data.map(item => item.total_sales);

            const salesData = allMonths.map(month => {
                const index = monthsWithData.indexOf(month);
                return index !== -1 ? sales[index] : 0;
            });

            const ctx = document.getElementById('myChart').getContext('2d');

            if (myChart) {
                myChart.destroy();
            }

            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: allMonths,
                    datasets: [{
                        label: `For the year of ${year}`,
                        data: salesData,
                        backgroundColor: '#049547',
                        borderColor: 'white',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMin: 0,
                            suggestedMax: 15000,
                            ticks: {
                                stepSize: 1000
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

fetchAnalyticsData(document.getElementById('yearSelector').value);

document.getElementById('yearSelector').addEventListener('change', function () {
    const selectedYear = this.value;
    fetchAnalyticsData(selectedYear);
});