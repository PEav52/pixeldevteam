<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login_system.php");
    exit;
}

$check_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$check_id) {
    header("Location: domain_checks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Check Details - Pixel IT Solution</title>
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
                    <a href="domain_checks.php" class="text-white hover:text-gray-300">
                        ← Back to Domain Checks
                    </a>
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
                <div class="max-w-4xl mx-auto">
                    <!-- Domain Check Details -->
                    <div class="bg-white rounded-lg p-6 shadow mb-6">
                        <h2 class="text-2xl font-semibold mb-6">Domain Check Details</h2>
                        <div id="domainCheckDetails" class="space-y-4">
                            <!-- Details will be populated via JavaScript -->
                            <div class="animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-xl font-semibold mb-4">Additional Information</h3>
                        <div id="additionalInfo" class="space-y-4">
                            <!-- Additional info will be populated via JavaScript -->
                            <div class="animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';
        const checkId = <?php echo $check_id; ?>;

        async function fetchDomainCheckDetails() {
            try {
                const response = await fetch(`${apiBaseUrl}domain_checks.php?id=${checkId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                updateDetails(data);
            } catch (error) {
                console.error('Error fetching domain check details:', error);
                alert('Failed to load domain check details.');
            }
        }

        function updateDetails(data) {
            const detailsContainer = document.getElementById('domainCheckDetails');
            const additionalInfoContainer = document.getElementById('additionalInfo');

            // Format the checked_at date
            const checkedAt = new Date(data.checked_at).toLocaleString();

            // Determine status display
            let statusHtml;
            if (data.availability_status === 'Available') {
                statusHtml = '<span class="text-green-500">Available</span>';
            } else if (data.availability_status === 'Unavailable') {
                statusHtml = '<span class="text-red-500">Unavailable</span>';
            } else {
                statusHtml = '<span class="text-gray-500">Null</span>';
            }

            // Update main details
            detailsContainer.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Domain</p>
                        <p class="font-semibold">${data.domain}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p class="font-semibold">${statusHtml}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Checked At</p>
                        <p class="font-semibold">${checkedAt}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Check ID</p>
                        <p class="font-semibold">${data.check_id}</p>
                    </div>
                </div>
            `;

            // Update additional information
            additionalInfoContainer.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600">Error Message</p>
                        <p class="font-semibold">${data.error_message || 'No errors reported'}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Domain Analysis</p>
                        <div class="mt-2">
                            <p class="text-sm">
                                <span class="font-semibold">TLD:</span> 
                                ${data.domain ? data.domain.split('.').pop() : 'N/A'}
                            </p>
                            <p class="text-sm">
                                <span class="font-semibold">Length:</span> 
                                ${data.domain ? data.domain.length : 0} characters
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', fetchDomainCheckDetails);
    </script>
</body>
</html>