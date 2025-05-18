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
    <title>Testimonial Details - Pixel IT Solution</title>
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
                <!-- Testimonial Details -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Testimonial Details</h2>
                        <a href="testimonials.php" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600">Back to Testimonials</a>
                    </div>
                    <div id="testimonial-details" class="space-y-4">
                        <!-- Testimonial details will be populated via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // Get testimonial ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const testimonialId = urlParams.get('id');

        // Fetch and display testimonial details
        async function fetchTestimonialDetails() {
            if (!testimonialId) {
                alert('Testimonial ID is missing.');
                window.location.href = 'testimonials.php';
                return;
            }

            try {
                const response = await fetch(`${apiBaseUrl}testimonials.php?id=${testimonialId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const testimonial = await response.json();

                if (testimonial.error) {
                    alert(testimonial.error);
                    window.location.href = 'testimonials.php';
                    return;
                }

                const detailsContainer = document.getElementById('testimonial-details');
                detailsContainer.innerHTML = `
                    <div class="flex flex-col space-y-2">
                        <div><strong>Testimonial ID:</strong> ${testimonial.testimonial_id}</div>
                        <div><strong>Client Name:</strong> ${testimonial.client_name}</div>
                        <div><strong>Client Title:</strong> ${testimonial.client_title}</div>
                        <div><strong>Content:</strong> ${testimonial.content}</div>
                        <div><strong>Rating:</strong> ${testimonial.rating}</div>
                        <div><strong>Image URL:</strong> ${testimonial.image_url ? `<a href="${testimonial.image_url}" target="_blank">View Image</a>` : 'None'}</div>
                        <div><strong>Project ID:</strong> ${testimonial.project_id || 'None'}</div>
                        <div><strong>Visibility:</strong> 
                            <span class="${testimonial.is_visible === 1 ? 'text-green-500' : 'text-red-500'}">
                                ${testimonial.is_visible === 1 ? 'Visible' : 'Invisible'}
                            </span>
                        </div>
                        <div><strong>Display Order:</strong> ${testimonial.display_order}</div>
                        <div class="flex space-x-2">
                            <button onclick="setVisible(${testimonial.testimonial_id})" 
                                    class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600">
                                Set Visible
                            </button>
                            <button onclick="setInvisible(${testimonial.testimonial_id})" 
                                    class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">
                                Set Invisible
                            </button>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error fetching testimonial details:', error);
                alert('Failed to load testimonial details.');
                window.location.href = 'testimonials.php';
            }
        }

        // Set testimonial to visible (is_visible = 1)
        async function setVisible(testimonialId) {
            try {
                const response = await fetch(`${apiBaseUrl}testimonials.php?id=${testimonialId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ is_visible: '1' })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Testimonial set to visible.');
                    fetchTestimonialDetails();
                } else {
                    alert(`Failed to update visibility: ${result.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error setting visible:', error);
                alert('An error occurred while updating visibility.');
            }
        }

        // Set testimonial to invisible (is_visible = 0)
        async function setInvisible(testimonialId) {
            try {
                const response = await fetch(`${apiBaseUrl}testimonials.php?id=${testimonialId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ is_visible: '0' })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Testimonial set to invisible.');
                    fetchTestimonialDetails();
                } else {
                    alert(`Failed to update visibility: ${result.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error setting invisible:', error);
                alert('An error occurred while updating visibility.');
            }
        }

        // Load testimonial details on page load
        document.addEventListener('DOMContentLoaded', fetchTestimonialDetails);
    </script>
</body>
</html>