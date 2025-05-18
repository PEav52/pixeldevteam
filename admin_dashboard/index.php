<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login_system.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixel IT Solution Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col p-6">
            <div class="flex items-center mb-8">
                <span class="text-2xl font-bold">Pixel</span>
            </div>
            <nav class="space-y-4">
                <a href="index.php" class="flex items-center space-x-2 text-pink-400">
                    <span>🏠</span><span>Dashboard</span>
                </a>
                <a href="analytics.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>📈</span><span>Analytics</span>
                </a>
                <a href="services.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🛠️</span><span>My Services</span>
                </a>
                <a href="projects.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>📋</span><span>My Projects</span>
                </a>
                <a href="inquiries.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>📬</span><span>Inquiry</span>
                </a>                
                <a href="testimonials.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🌟</span><span>Testimonials</span>
                </a>
                <a href="domain_checks.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🌐</span><span>Domain Checks</span>
                </a>
                <a href="setting.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>⚙️</span><span>Settings</span>
                </a>
                <a href="profile-management.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>👤</span><span>Profile Management</span>
                </a>
            </nav>
            <div class="mt-auto text-gray-400 text-sm">Version 1.0.1</div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-gray-800 text-white flex items-center justify-between p-4">
                <div class="flex items-center space-x-4">
                    <input type="text" placeholder="Search" class="bg-gray-700 text-white rounded-full px-4 py-2 focus:outline-none">
                </div>
                <div class="flex items-center space-x-4">
                    <span>🔔</span>
                    <span>📧</span>
                    <div class="flex items-center space-x-2">
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <span>👤</span>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </header>

            <!-- Main Section -->
            <div class="p-6 flex-1 overflow-auto">
                <!-- Greeting Section -->
                <div class="bg-yellow-200 rounded-lg p-6 flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                        <p class="text-gray-700">Track and analyze your website visitors with real-time insights.</p>
                        <a href="analytics.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg">View Analytics</a>
                    </div>
                    <div>
                        <img src="assets/images/report-analysis-5-79.svg" alt="Illustration" class="w-32">
                    </div>
                </div>

                <div class="flex space-x-6">
                    <!-- Left Section: Visitor Analytics -->
                    <div class="flex-1">
                        <!-- Visitor Summary -->
                        <div class="bg-white rounded-lg p-6 shadow mb-6">
                            <h2 class="text-lg font-semibold mb-4">Visitor Summary</h2>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p id="daily-visitors" class="text-2xl font-bold">0</p>
                                    <p class="text-sm text-gray-500">Total Visitors (Today)</p>
                                </div>
                                <div class="text-center">
                                    <p id="weekly-visitors" class="text-2xl font-bold">0</p>
                                    <p class="text-sm text-gray-500">Total Visitors (This Week)</p>
                                </div>
                                <div class="text-center">
                                    <p id="monthly-visitors" class="text-2xl font-bold">0</p>
                                    <p class="text-sm text-gray-500">Total Visitors (This Month)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Visitor Trends Chart -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Visitor Trends</h2>
                                <select id="trends-period" class="border rounded px-2 py-1">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            <canvas id="visitorChart" class="w-full h-64"></canvas>
                        </div>
                    </div>

                    <!-- Right Section: Historical Comparison and Quick Stats -->
                    <div class="w-80 space-y-6">
                        <!-- Historical Data Comparison -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <h2 class="text-lg font-semibold mb-4">Compare Historical Data</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm text-gray-500">Select Period 1</label>
                                    <select id="compare-period1" class="w-full border rounded px-2 py-1">
                                        <option value="2025-04">April 2025</option>
                                        <option value="2025-03">March 2025</option>
                                        <option value="2025-02">February 2025</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Select Period 2</label>
                                    <select id="compare-period2" class="w-full border rounded px-2 py-1">
                                        <option value="2025-03">March 2025</option>
                                        <option value="2025-02">February 2025</option>
                                        <option value="2025-01">January 2025</option>
                                    </select>
                                </div>
                                <button id="compare-btn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg">Compare</button>
                            </div>
                            <div id="comparison-results" class="mt-4">
                                <p class="text-sm text-gray-500">Comparison Results</p>
                                <div class="flex justify-between mt-2">
                                    <div>
                                        <p id="period1-label" class="font-semibold">Apr 2025</p>
                                        <p id="period1-visitors">0 Visitors</p>
                                    </div>
                                    <div>
                                        <p id="period2-label" class="font-semibold">Mar 2025</p>
                                        <p id="period2-visitors">0 Visitors</p>
                                    </div>
                                </div>
                                <p id="percentage-change" class="text-sm text-green-500 mt-2">0%</p>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <h2 class="text-lg font-semibold mb-4">Quick Stats</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <p class="text-sm text-gray-500">Avg. Session Duration</p>
                                    <p id="avg-session" class="font-semibold">0m</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm text-gray-500">Bounce Rate</p>
                                    <p id="bounce-rate" class="font-semibold">0%</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm text-gray-500">Returning Visitors</p>
                                    <p id="returning-visitors" class="font-semibold">0%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Dynamic Data -->
    <script>
        let visitorChart;

        // Initialize Chart
        function initChart() {
            const ctx = document.getElementById('visitorChart').getContext('2d');
            visitorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Visitors',
                        data: [],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Fetch and Update Metrics
        async function fetchMetrics() {
            const response = await fetch('api/analytics.php?type=metrics');
            const data = await response.json();
            document.getElementById('daily-visitors').textContent = data.daily_visitors.toLocaleString();
            document.getElementById('weekly-visitors').textContent = data.weekly_visitors.toLocaleString();
            document.getElementById('monthly-visitors').textContent = data.monthly_visitors.toLocaleString();
            document.getElementById('avg-session').textContent = `${data.avg_session_duration}m`;

            // Calculate bounce rate (simplified: visits with duration < 30s)
            const bounceResponse = await fetch('api/analytics.php?type=metrics&bounce=true');
            const bounceData = await bounceResponse.json();
            const bounceRate = bounceData.bounce_rate || 0;
            document.getElementById('bounce-rate').textContent = `${bounceRate}%`;

            // Calculate returning visitors (simplified: distinct IPs with multiple sessions)
            const returningResponse = await fetch('api/analytics.php?type=metrics&returning=true');
            const returningData = await returningResponse.json();
            const returningRate = returningData.returning_visitors || 0;
            document.getElementById('returning-visitors').textContent = `${returningRate}%`;
        }

        // Fetch and Update Trends
        async function fetchTrends() {
            const period = document.getElementById('trends-period').value;
            const date = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
            const response = await fetch(`api/analytics.php?type=trends&period=${period}&date=${date}`);
            const data = await response.json();
            visitorChart.data.labels = data.map(d => d.label);
            visitorChart.data.datasets[0].data = data.map(d => d.count);
            visitorChart.update();
        }

        // Fetch and Update Comparison
        async function fetchComparison() {
            const period1 = document.getElementById('compare-period1').value;
            const period2 = document.getElementById('compare-period2').value;
            const response = await fetch(`api/analytics.php?type=comparison&period1=${period1}&period2=${period2}`);
            const data = await response.json();
            document.getElementById('period1-label').textContent = data.period1.label;
            document.getElementById('period1-visitors').textContent = `${data.period1.visitors.toLocaleString()} Visitors`;
            document.getElementById('period2-label').textContent = data.period2.label;
            document.getElementById('period2-visitors').textContent = `${data.period2.visitors.toLocaleString()} Visitors`;
            document.getElementById('percentage-change').textContent = `${data.percentage_change}%`;
            document.getElementById('percentage-change').className = `text-sm mt-2 ${data.percentage_change >= 0 ? 'text-green-500' : 'text-red-500'}`;
        }

        // Event Listeners
        document.getElementById('trends-period').addEventListener('change', fetchTrends);
        document.getElementById('compare-btn').addEventListener('click', fetchComparison);

        // Initial Load
        initChart();
        fetchMetrics();
        fetchTrends();
        fetchComparison();
    </script>
</body>
</html>