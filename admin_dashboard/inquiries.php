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
    <title>Inquiries - Pixel IT Solution</title>
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
                <a href="inquiries.php" class="flex items-center text-pink-400 space-x-2">
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
                <!-- Inquiries Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Inquiries</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Inquiry ID</th>
                                    <th class="py-3 px-4">Name</th>
                                    <th class="py-3 px-4">Email</th>
                                    <th class="py-3 px-4">Project Type</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Created At</th>
                                    <th class="py-3 px-4">Responded At</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inquiries-table-body">
                                <!-- Inquiries will be populated via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // Fetch and display inquiries
        async function fetchInquiries() {
            try {
                const response = await fetch(`${apiBaseUrl}inquiries.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const inquiries = await response.json();

                const tableBody = document.getElementById('inquiries-table-body');
                tableBody.innerHTML = '';

                inquiries.forEach(inquiry => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${inquiry.inquiry_id}</td>
                        <td class="py-3 px-4">${inquiry.name}</td>
                        <td class="py-3 px-4">${inquiry.email}</td>
                        <td class="py-3 px-4">${inquiry.project_type}</td>
                        <td class="py-3 px-4">
                            ${inquiry.status === 'pending' ? '<span class="text-yellow-500">Pending</span>' :
                            inquiry.status === 'read' ? '<span class="text-green-500">Read</span>' :
                            inquiry.status === 'responded' ? '<span class="text-blue-500">Responded</span>' :
                            inquiry.status === 'closed' ? '<span class="text-gray-500">Closed</span>' :
                            '<span class="text-red-500">Unknown</span>'}
                        </td>           
                        <td class="py-3 px-4">${inquiry.created_at}</td>
                        <td class="py-3 px-4">${inquiry.responded_at}</td>
                        <td class="py-3 px-4">
                            <a href="inquiry-details.php?id=${inquiry.inquiry_id}" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 mr-2">View</a>
                            ${inquiry.status === 'pending' ? `
                                <button onclick="markAsRead(${inquiry.inquiry_id})" class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600">Mark as Read</button>
                            ` : ''}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching inquiries:', error);
                alert('Failed to load inquiries.');
            }
        }

        // Mark inquiry as read
        async function markAsRead(inquiryId) {
            try {
                const response = await fetch(`${apiBaseUrl}inquiries.php?id=${inquiryId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: 'read' })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Inquiry marked as read.');
                    fetchInquiries();
                } else {
                    alert(`Failed to update status: ${result.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error marking as read:', error);
                alert('An error occurred while updating status.');
            }
        }

        // Load inquiries on page load
        document.addEventListener('DOMContentLoaded', fetchInquiries);
    </script>
</body>
</html>