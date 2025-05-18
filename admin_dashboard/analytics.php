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
    <title>Pixel IT Solution Analytics</title>
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
                <a href="index.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🏠</span><span>Dashboard</span>
                </a>
                <a href="analytics.php" class="flex items-center space-x-2 text-pink-400">
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
                <a href="profile-management.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>👤</span><span>Profile Management</span>
                </a>
                <a href="setting.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>⚙️</span><span>Settings</span>
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
                <!-- Page Title -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Analytics Overview</h1>
                        <p class="text-gray-600">Detailed insights into visitor behavior and performance trends.</p>
                    </div>
                    <button id="export-csv" class="bg-green-600 text-white px-4 py-2 rounded-lg">Export as CSV</button>
                </div>

                <div class="flex space-x-6">
                    <!-- Left Section: Charts and Detailed Metrics -->
                    <div class="flex-1">
                        <!-- Visitor Metrics -->
                        <div class="bg-white rounded-lg p-6 shadow mb-6">
                            <h2 class="text-lg font-semibold mb-4">Key Visitor Metrics</h2>
                            <div class="grid grid-cols-4 gap-4">
                                <div class="text-center">
                                    <p id="daily-visitors" class="text-2xl font-bold">0</p>
                                    <p class="text-sm text-gray-500">Daily Visitors</p>
                                </div>
                                <div class="text-center">
                                    <p id="weekly-visitors" class="text-2xl font-bold">0</p>
                                    <p class="text-sm text-gray-500">Weekly Visitors</p>
                                </div>
                                <div class="text-center">
                                    <p id="monthly-visitors" class="text-2xl font-bold">0</p>
                                    <p class="text-sm text-gray-500">Monthly Visitors</p>
                                </div>
                                <div class="text-center">
                                    <p id="avg-session" class="text-2xl font-bold">0m</p>
                                    <p class="text-sm text-gray-500">Avg. Session</p>
                                </div>
                            </div>
                        </div>

                        <!-- Visitor Trends Chart -->
                        <div class="bg-white rounded-lg p-6 shadow mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Visitor Trends</h2>
                                <div class="flex space-x-2">
                                    <select id="trends-period" class="border rounded px-2 py-1">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    <input type="date" id="trends-date" class="border rounded px-2 py-1" value="2025-04-30">
                                </div>
                            </div>
                            <canvas id="visitorTrendChart" class="w-full h-64"></canvas>
                        </div>

                        <!-- Traffic Sources Pie Chart -->
                        <div class="bg-white rounded-lg p-6 shadow mb-6">
                            <h2 class="text-lg font-semibold mb-4">Traffic Sources</h2>
                            <canvas id="trafficSourceChart" class="w-full h-48"></canvas>
                        </div>

                        <!-- Demographics Bar Chart -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Visitor Demographics (Region)</h2>
                                <select id="demographics-by" class="border rounded px-2 py-1">
                                    <option value="region">By Region</option>
                                    <option value="age">By Age Group</option>
                                </select>
                            </div>
                            <canvas id="demographicsChart" class="w-full h-48"></canvas>
                        </div>
                    </div>

                    <!-- Right Section: Historical Comparison, Filters, and More -->
                    <div class="w-80 space-y-6">
                        <!-- Real-Time Visitors -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <h2 class="text-lg font-semibold mb-4">Real-Time Visitors</h2>
                            <div class="text-center">
                                <p id="active-visitors" class="text-3xl font-bold">0</p>
                                <p class="text-sm text-gray-500">Active Now</p>
                            </div>
                            <canvas id="realtimeSparkline" class="w-full h-16 mt-4"></canvas>
                        </div>

                        <!-- Historical Comparison -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <h2 class="text-lg font-semibold mb-4">Historical Comparison</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm text-gray-500">Period 1</label>
                                    <input type="month" id="compare-period1" class="w-full border rounded px-2 py-1" value="2025-04">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Period 2</label>
                                    <input type="month" id="compare-period2" class="w-full border rounded px-2 py-1" value="2025-03">
                                </div>
                                <button id="compare-btn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg">Compare Data</button>
                            </div>
                            <div id="comparison-results" class="mt-4">
                                <p class="text-sm text-gray-500">Results</p>
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

                        <!-- Advanced Filters -->
                        <div class="bg-white rounded-lg p-6 shadow">
                            <h2 class="text-lg font-semibold mb-4">Advanced Filters</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm text-gray-500">Device Type</label>
                                    <select id="filter-device" class="w-full border rounded px-2 py-1">
                                        <option value="">All Devices</option>
                                        <option value="desktop">Desktop</option>
                                        <option value="mobile">Mobile</option>
                                        <option value="tablet">Tablet</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Traffic Source</label>
                                    <select id="filter-source" class="w-full border rounded px-2 py-1">
                                        <option value="">All Sources</option>
                                        <option value="direct">Direct</option>
                                        <option value="organic">Organic Search</option>
                                        <option value="social">Social Media</option>
                                        <option value="referral">Referral</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Location</label>
                                    <select id="filter-location" class="w-full border rounded px-2 py-1">
                                        <option value="">All Locations</option>
                                        <option value="US">United States</option>
                                        <option value="EU">Europe</option>
                                        <option value="AS">Asia</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Browser</label>
                                    <select id="filter-browser" class="w-full border rounded px-2 py-1">
                                        <option value="">All Browsers</option>
                                        <option value="Chrome">Chrome</option>
                                        <option value="Firefox">Firefox</option>
                                        <option value="Safari">Safari</option>
                                        <option value="Edge">Edge</option>
                                    </select>
                                </div>
                                <button id="apply-filters" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Pages Section -->
                <div class="mt-6 bg-white rounded-lg p-6 shadow">
                    <h2 class="text-lg font-semibold mb-4">Top Pages</h2>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-500">
                                <th class="pb-2">Page</th>
                                <th class="pb-2">Page Views</th>
                                <th class="pb-2">Unique Visitors</th>
                                <th class="pb-2">Avg. Time Spent</th>
                            </tr>
                        </thead>
                        <tbody id="top-pages-table">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Dynamic Data -->
    <script>
        let visitorTrendChart, trafficSourceChart, demographicsChart, realtimeSparkline;

        // Initialize Charts
        function initCharts() {
            const trendCtx = document.getElementById('visitorTrendChart').getContext('2d');
            visitorTrendChart = new Chart(trendCtx, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Visitors', data: [], borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)', fill: true, tension: 0.4 }] },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            const sourceCtx = document.getElementById('trafficSourceChart').getContext('2d');
            trafficSourceChart = new Chart(sourceCtx, {
                type: 'pie',
                data: { labels: [], datasets: [{ data: [], backgroundColor: ['rgb(59, 130, 246)', 'rgb(34, 197, 94)', 'rgb(249, 115, 22)', 'rgb(139, 92, 246)'] }] },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });

            const demoCtx = document.getElementById('demographicsChart').getContext('2d');
            demographicsChart = new Chart(demoCtx, {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Visitors', data: [], backgroundColor: 'rgb(59, 130, 246)' }] },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            const sparklineCtx = document.getElementById('realtimeSparkline').getContext('2d');
            realtimeSparkline = new Chart(sparklineCtx, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Active Visitors', data: [], borderColor: 'rgb(34, 197, 94)', backgroundColor: 'rgba(34, 197, 94, 0.1)', fill: true, tension: 0.4 }] },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } }
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
        }

        // Set default date for trends to today
        const date = new Date().toISOString().split('T')[0];
        document.getElementById('trends-date').value = date; // Set default date to today

        // Fetch and Update Trends
        async function fetchTrends() {
            const period = document.getElementById('trends-period').value;
            const date = document.getElementById('trends-date').value;
            const response = await fetch(`api/analytics.php?type=trends&period=${period}&date=${date}`);
            const data = await response.json();
            visitorTrendChart.data.labels = data.map(d => d.label);
            visitorTrendChart.data.datasets[0].data = data.map(d => d.count);
            visitorTrendChart.update();
        }

        // Fetch and Update Traffic Sources
        async function fetchTrafficSources() {
            const response = await fetch('api/analytics.php?type=traffic_sources');
            const data = await response.json();
            trafficSourceChart.data.labels = data.map(d => d.label);
            trafficSourceChart.data.datasets[0].data = data.map(d => d.count);
            trafficSourceChart.update();
        }

        // Fetch and Update Demographics
        async function fetchDemographics() {
            const by = document.getElementById('demographics-by').value;
            const response = await fetch(`api/analytics.php?type=demographics&by=${by}`);
            const data = await response.json();
            demographicsChart.data.labels = data.map(d => d.label);
            demographicsChart.data.datasets[0].data = data.map(d => d.count);
            demographicsChart.update();
        }

        // Fetch and Update Real-Time Visitors
        async function fetchRealtime() {
            const response = await fetch('api/analytics.php?type=realtime');
            const data = await response.json();
            document.getElementById('active-visitors').textContent = data.active_visitors;
            realtimeSparkline.data.labels = data.sparkline.map((_, i) => i);
            realtimeSparkline.data.datasets[0].data = data.sparkline.map(d => d.count);
            realtimeSparkline.update();
        }

        // Fetch and Update Top Pages
        async function fetchTopPages() {
            const response = await fetch('api/analytics.php?type=top_pages');
            const data = await response.json();
            const tableBody = document.getElementById('top-pages-table');
            tableBody.innerHTML = data.map(row => `
                <tr class="border-t">
                    <td class="py-2">${row.page_url}</td>
                    <td class="py-2">${row.page_views.toLocaleString()}</td>
                    <td class="py-2">${row.unique_visitors.toLocaleString()}</td>
                    <td class="py-2">${row.avg_time_spent}m</td>
                </tr>
            `).join('');
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

        // Apply Filters (Placeholder for Future Implementation)
        document.getElementById('apply-filters').addEventListener('click', () => {
            // Future: Add query parameters to API calls based on filter values
            fetchMetrics();
            fetchTrends();
            fetchTrafficSources();
            fetchDemographics();
            fetchTopPages();
        });

        // Event Listeners
        document.getElementById('trends-period').addEventListener('change', fetchTrends);
        document.getElementById('trends-date').addEventListener('change', fetchTrends);
        document.getElementById('demographics-by').addEventListener('change', fetchDemographics);
        document.getElementById('compare-btn').addEventListener('click', fetchComparison);

        // Export CSV (Placeholder)
        document.getElementById('export-csv').addEventListener('click', () => {
            alert('CSV export not implemented yet. Requires backend endpoint.');
        });

        // Initial Load
        initCharts();
        fetchMetrics();
        fetchTrends();
        fetchTrafficSources();
        fetchDemographics();
        fetchTopPages();
        fetchComparison();

        // Poll for Real-Time Updates
        setInterval(fetchRealtime, 30000); // Update every 30 seconds
    </script>
</body>
</html>