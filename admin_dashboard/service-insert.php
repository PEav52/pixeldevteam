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
    <title>Add Service - Pixel Admin</title>
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
                    <h2 class="text-lg font-semibold mb-6">Add New Service</h2>
                    <form id="serviceForm">
                        <div class="space-y-8">
                            <!-- Service Details Section -->
                            <div>
                                <h3 class="text-md font-semibold mb-4">Service Details</h3>
                                <div class="space-y-6">
                                    <!-- Title -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                        <input type="text" name="title" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter service title" required>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Enter service description" required></textarea>
                                    </div>

                                    <!-- Overview -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Overview</label>
                                        <textarea name="overview" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Enter service overview"></textarea>
                                    </div>

                                    <!-- Min Price -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Price ($)</label>
                                        <input type="number" name="min_price" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter minimum price" required>
                                    </div>

                                    <!-- Max Price -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Price ($)</label>
                                        <input type="number" name="max_price" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter maximum price" required>
                                    </div>

                                    <!-- Image File Input -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Service Image</label>
                                        <input type="file" name="image_url" id="imageInput" accept="image/*" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <div class="mt-4">
                                            <img id="imagePreview" class="max-w-xs h-auto rounded-lg hidden" alt="Image Preview">
                                        </div>
                                    </div>

                                    <!-- Is Visible -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                                        <select name="is_visible" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="1">Visible</option>
                                            <option value="0">Hidden</option>
                                        </select>
                                    </div>

                                    <!-- Display Order -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                                        <input type="number" name="display_order" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter display order">
                                    </div>
                                </div>
                            </div>

                            <!-- Features Section -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-md font-semibold">Features</h3>
                                    <button type="button" onclick="addFeature()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Feature</button>
                                </div>
                                <div id="featuresContainer" class="space-y-6">
                                    <!-- Feature entries will be added here dynamically -->
                                </div>
                            </div>

                            <!-- Pricing Package Section -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-md font-semibold">Pricing Packages</h3>
                                    <button type="button" onclick="addPricing()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Pricing Package</button>
                                </div>
                                <div id="pricingContainer" class="space-y-6">
                                    <!-- Pricing package entries will be added here dynamically -->
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-4">
                                <a href="services.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</a>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // Image Preview
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
            }
        });

        // Feature Counter
        let featureCount = 0;

        function addFeature() {
            featureCount++;
            const featureDiv = document.createElement('div');
            featureDiv.className = 'feature-entry p-4 border rounded-lg space-y-4 relative';
            featureDiv.id = `feature-${featureCount}`;
            featureDiv.innerHTML = `
                <button type="button" onclick="removeFeature(${featureCount})" class="absolute top-2 right-2 text-red-500 hover:text-red-700">✖</button>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name</label>
                    <input type="text" name="features[${featureCount}][name]" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter feature name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="features[${featureCount}][description]" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Enter feature description"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Active</label>
                    <select name="features[${featureCount}][is_active]" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            `;
            document.getElementById('featuresContainer').appendChild(featureDiv);
        }

        function removeFeature(id) {
            const featureDiv = document.getElementById(`feature-${id}`);
            if (featureDiv) {
                featureDiv.remove();
            }
        }

        // Pricing Package Counter
        let pricingCount = 0;

        function addPricing() {
            pricingCount++;
            const pricingDiv = document.createElement('div');
            pricingDiv.className = 'pricing-entry p-4 border rounded-lg space-y-4 relative';
            pricingDiv.id = `pricing-${pricingCount}`;
            pricingDiv.innerHTML = `
                <button type="button" onclick="removePricing(${pricingCount})" class="absolute top-2 right-2 text-red-500 hover:text-red-700">✖</button>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="pricing_packages[${pricingCount}][title]" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter package title">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                    <input type="number" step="0.01" name="pricing_packages[${pricingCount}][price]" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter package price">
                </div>
            `;
            document.getElementById('pricingContainer').appendChild(pricingDiv);
        }

        function removePricing(id) {
            const pricingDiv = document.getElementById(`pricing-${id}`);
            if (pricingDiv) {
                pricingDiv.remove();
            }
        }

        // Form Submission
        document.getElementById('serviceForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = this;
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);

            try {
                const response = await fetch(`${apiBaseUrl}services.php`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || `Failed to create service (Status: ${response.status})`);
                }
                alert('Service created successfully!');
                window.location.href = 'services.php';
            } catch (error) {
                console.error('Error creating service:', error);
                alert('Error creating service: ' + error.message);
            }
        });
    </script>
</body>
</html>