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
    <title>Testimonials - Pixel IT Solution</title>
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
                <a href="services.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🛠️</span><span>My Services</span>
                </a>
                <a href="projects.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>📋</span><span>My Projects</span>
                </a>
                <a href="inquiries.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>📬</span><span>Inquiry</span>
                </a>
                <a href="testimonials.php" class="flex items-center text-pink-400 space-x-2">
                    <span>🌟</span><span>Testimonials</span>
                </a>
                <a href="domain_checks.php" class="flex items-center space-x-2 hover:text-gray-300">
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
                <!-- Testimonials Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Testimonials</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Testimonial ID</th>
                                    <th class="py-3 px-4">Client Name</th>
                                    <th class="py-3 px-4">Client Title</th>
                                    <th class="py-3 px-4">Content</th>
                                    <th class="py-3 px-4">Rating</th>
                                    <th class="py-3 px-4">Visibility</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="testimonials-table-body">
                                <!-- Testimonials will be populated via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // Fetch and display testimonials
        async function fetchTestimonials() {
            try {
                const response = await fetch(`${apiBaseUrl}testimonials.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const testimonials = await response.json();

                const tableBody = document.getElementById('testimonials-table-body');
                tableBody.innerHTML = '';

                testimonials.forEach(testimonial => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${testimonial.testimonial_id}</td>
                        <td class="py-3 px-4">${testimonial.client_name}</td>
                        <td class="py-3 px-4">${testimonial.client_title}</td>
                        <td class="py-3 px-4">${testimonial.content.substring(0, 50)}${testimonial.content.length > 50 ? '...' : ''}</td>
                        <td class="py-3 px-4">${testimonial.rating}</td>
                        <td class="py-3 px-4">
                            ${testimonial.is_visible === 1 ? '<span class="text-green-500">Visible</span>' : '<span class="text-red-500">Invisible</span>'}
                        </td>
                        <td class="py-3 px-4">
                            <a href="testimonial-details.php?id=${testimonial.testimonial_id}" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 mr-2">View</a>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching testimonials:', error);
                alert('Failed to load testimonials.');
            }
        }

        // Load testimonials on page load
        document.addEventListener('DOMContentLoaded', fetchTestimonials);
    </script>
</body>
</html>