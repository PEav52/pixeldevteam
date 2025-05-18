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
    <title>My Services - Pixel Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <a href="services.php" class="flex items-center text-pink-400 space-x-2">
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
                <div class="bg-white rounded-lg p-6 shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">My Services</h2>
                        <a href="service-insert.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Service</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Service ID</th>
                                    <th class="py-3 px-4">Title</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Min Price</th>
                                    <th class="py-3 px-4">Max Price</th>
                                    <th class="py-3 px-4">Is Visible</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTableBody">
                                <!-- Services will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        async function fetchServices() {
            try {
                const response = await fetch(`${apiBaseUrl}services.php`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) {
                    throw new Error('Failed to fetch services');
                }
                const services = await response.json();
                populateServicesTable(services);
            } catch (error) {
                console.error('Error fetching services:', error);
                alert('Error fetching services: ' + error.message);
            }
        }

        function populateServicesTable(services) {
            const tableBody = document.getElementById('servicesTableBody');
            tableBody.innerHTML = '';
            services.forEach(service => {
                const row = document.createElement('tr');
                row.className = 'border-b';
                row.innerHTML = `
                    <td class="py-3 px-4">${service.service_id}</td>
                    <td class="py-3 px-4">${service.title}</td>
                    <td class="py-3 px-4">${service.description}</td>
                    <td class="py-3 px-4">$${parseFloat(service.min_price).toFixed(2)}</td>
                    <td class="py-3 px-4">$${parseFloat(service.max_price).toFixed(2)}</td>
                    <td class="py-3 px-4 ${service.is_visible ? 'text-green-500' : 'text-red-500'}">${service.is_visible ? 'True' : 'False'}</td>
                    <td class="py-3 px-4">
                        <a href="service-details.php?id=${service.service_id}" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600">Edit</a>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Fetch services on page load
        document.addEventListener('DOMContentLoaded', fetchServices);
    </script>
</body>
</html>