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
    <title>Domain Checks - Pixel IT Solution</title>
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
                <a href="domain_checks.php" class="flex items-center text-pink-400 space-x-2">
                    <span>🌐</span><span>Domain Checks</span>
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
                    <input type="text" placeholder="Search domains..." class="bg-gray-700 text-white rounded-full px-4 py-2 focus:outline-none" id="searchInput">
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
                <!-- Analytics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-2">Total Checks</h3>
                        <p class="text-3xl font-bold" id="totalChecks">0</p>
                    </div>
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-2">Available Domains</h3>
                        <p class="text-3xl font-bold text-green-500" id="availableDomains">0</p>
                    </div>
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-2">Most Checked TLD</h3>
                        <p class="text-3xl font-bold text-blue-500" id="mostCheckedTLD">-</p>
                    </div>
                </div>

                <!-- Domain Checks Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Domain Check History</h2>
                        <div class="flex space-x-2">
                            <select id="filterStatus" class="bg-gray-100 rounded px-3 py-2">
                                <option value="all">All Status</option>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                                <option value="null">Null</option>
                            </select>
                            <select id="filterTLD" class="bg-gray-100 rounded px-3 py-2">
                                <option value="all">All TLDs</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Check ID</th>
                                    <th class="py-3 px-4">Domain</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Checked At</th>
                                    <th class="py-3 px-4">Error Message</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="domain-checks-table-body">
                                <!-- Domain checks will be populated via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Analytics Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-4">Domain Availability Distribution</h3>
                        <canvas id="availabilityChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-4">Top TLDs Checked</h3>
                        <canvas id="tldChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // Fetch and display domain checks
        async function fetchDomainChecks() {
            try {
                const response = await fetch(`${apiBaseUrl}domain_checks.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                updateTable(data.checks);
                updateAnalytics(data.analytics);
                updateCharts(data.analytics);
            } catch (error) {
                console.error('Error fetching domain checks:', error);
                alert('Failed to load domain checks.');
            }
        }

        function updateTable(checks) {
            const tableBody = document.getElementById('domain-checks-table-body');
            tableBody.innerHTML = '';

            checks.forEach(check => {
                const row = document.createElement('tr');
                row.className = 'border-b';
                row.innerHTML = `
                    <td class="py-3 px-4">${check.check_id}</td>
                    <td class="py-3 px-4">${check.domain}</td>
                    <td class="py-3 px-4">
                        ${check.availability_status === 'Available'
                            ? '<span class="text-green-500">Available</span>'
                            : check.availability_status === 'Unavailable'
                                ? '<span class="text-red-500">Unavailable</span>'
                                : '<span class="text-gray-500">Null</span>'}
                    </td>
                    <td class="py-3 px-4">${new Date(check.checked_at).toLocaleString()}</td>
                    <td class="py-3 px-4">${check.error_message || '-'}</td>
                    <td class="py-3 px-4">
                        <button onclick="viewDetails(${check.check_id})" 
                                class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600">
                            View Details
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function updateAnalytics(analytics) {
            document.getElementById('totalChecks').textContent = analytics.total_checks;
            document.getElementById('availableDomains').textContent = analytics.available_domains;
            document.getElementById('mostCheckedTLD').textContent = analytics.most_checked_tld;
        }

        function updateCharts(analytics) {
            // Count null checks
            const nullChecks = analytics.total_checks - analytics.available_domains - analytics.unavailable_domains;

            // Availability Distribution Chart
            const availabilityCtx = document.getElementById('availabilityChart').getContext('2d');
            new Chart(availabilityCtx, {
                type: 'pie',
                data: {
                    labels: ['Available', 'Unavailable', 'Null'],
                    datasets: [{
                        data: [
                            analytics.available_domains,
                            analytics.unavailable_domains || analytics.total_checks - analytics.available_domains - nullChecks,
                            nullChecks
                        ],
                        backgroundColor: ['#10B981', '#EF4444', '#6B7280']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // TLD Distribution Chart
            const tldCtx = document.getElementById('tldChart').getContext('2d');
            new Chart(tldCtx, {
                type: 'bar',
                data: {
                    labels: analytics.top_tlds.map(tld => tld.tld),
                    datasets: [{
                        label: 'Number of Checks',
                        data: analytics.top_tlds.map(tld => tld.count),
                        backgroundColor: '#3B82F6'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function viewDetails(checkId) {
            window.location.href = `domain-check-details.php?id=${checkId}`;
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#domain-checks-table-body tr');
            
            rows.forEach(row => {
                const domain = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                row.style.display = domain.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter functionality
        document.getElementById('filterStatus').addEventListener('change', function(e) {
            const status = e.target.value;
            const rows = document.querySelectorAll('#domain-checks-table-body tr');
            
            rows.forEach(row => {
                const statusText = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (status === 'all' || 
                    (status === 'available' && statusText.includes('available')) || 
                    (status === 'unavailable' && statusText.includes('unavailable')) ||
                    (status === 'null' && statusText.includes('null'))) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Load data on page load
        document.addEventListener('DOMContentLoaded', fetchDomainChecks);
    </script>
</body>
</html>