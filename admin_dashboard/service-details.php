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
    <title>Service Details - Pixel Admin</title>
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
                    <h2 class="text-lg font-semibold mb-6">Service Details</h2>
                    <div class="space-y-8">
                        <!-- Service Details Table -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Service Information</h3>
                                <button onclick="openServiceModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Edit</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="py-3 px-4">Title</th>
                                            <th class="py-3 px-4">Description</th>
                                            <th class="py-3 px-4">Overview</th>
                                            <th class="py-3 px-4">Min Price ($)</th>
                                            <th class="py-3 px-4">Max Price ($)</th>
                                            <th class="py-3 px-4">Image</th>
                                            <th class="py-3 px-4">Visibility</th>
                                            <th class="py-3 px-4">Display Order</th>
                                        </tr>
                                    </thead>
                                    <tbody id="serviceTableBody">
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Features Table -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Features</h3>
                                <button onclick="openNewFeatureModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Add New Feature</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="py-3 px-4">Name</th>
                                            <th class="py-3 px-4">Description</th>
                                            <th class="py-3 px-4">Active</th>
                                            <th class="py-3 px-4">Created At</th>
                                            <th class="py-3 px-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="featuresTableBody">
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pricing Packages Table -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-md font-semibold">Pricing Packages</h3>
                                <button onclick="openNewPricingModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Add New Package</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="py-3 px-4">Title</th>
                                            <th class="py-3 px-4">Price ($)</th>
                                            <th class="py-3 px-4">Created At</th>
                                            <th class="py-3 px-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pricingTableBody">
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Edit Modal -->
    <div id="serviceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Edit Service</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="serviceTitle" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="serviceDescription" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Overview</label>
                    <textarea id="serviceOverview" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Price ($)</label>
                    <input type="number" id="serviceMinPrice" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Price ($)</label>
                    <input type="number" id="serviceMaxPrice" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Image</label>
                    <input type="file" id="serviceImage" required accept="image/*" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="mt-4">
                        <img id="serviceImagePreview" class="max-w-xs h-auto rounded-lg" alt="Image Preview">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                    <select id="serviceVisibility" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Visible</option>
                        <option value="0">Hidden</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" id="serviceDisplayOrder" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeServiceModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                    <button onclick="saveService()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Edit Modal -->
    <div id="featureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Edit Feature</h3>
            <div class="space-y-4">
                <input type="hidden" id="featureId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name</label>
                    <input type="text" id="featureName" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="featureDescription" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Active</label>
                    <select id="featureIsActive" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeFeatureModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                    <button onclick="saveFeature()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Feature Modal -->
    <div id="newFeatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Add New Feature</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name</label>
                    <input type="text" id="newFeatureName" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="newFeatureDescription" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Active</label>
                    <select id="newFeatureIsActive" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeNewFeatureModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                    <button onclick="addNewFeature()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Add Feature</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Package Edit Modal -->
    <div id="pricingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Edit Pricing Package</h3>
            <div class="space-y-4">
                <input type="hidden" id="pricingId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="pricingTitle" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                    <input type="number" step="0.01" id="pricingPrice" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-end space-x-4">
                    <button onclick="closePricingModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                    <button onclick="savePricing()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Pricing Package Modal -->
    <div id="newPricingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Add New Pricing Package</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="newPricingTitle" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                    <input type="number" step="0.01" id="newPricingPrice" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeNewPricingModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                    <button onclick="addNewPricing()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Add Package</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';
        const urlParams = new URLSearchParams(window.location.search);
        const serviceId = urlParams.get('id');

        function showError(message) {
            alert(message);
        }

        async function fetchServiceDetails() {
            try {
                const response = await fetch(`${apiBaseUrl}services.php?id=${serviceId}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) {
                    throw new Error('Failed to fetch service details');
                }
                const data = await response.json();
                populateServiceTable(data);
                populateFeaturesTable(data.features);
                populatePricingTable(data.pricing_packages);
            } catch (error) {
                console.error('Error fetching service details:', error);
                showError('Error fetching service details: ' + error.message);
            }
        }

        function populateServiceTable(service) {
            const tableBody = document.getElementById('serviceTableBody');
            tableBody.innerHTML = `
                <tr class="border-b">
                    <td class="py-3 px-4">${service.title}</td>
                    <td class="py-3 px-4">${service.description}</td>
                    <td class="py-3 px-4">${service.overview || ''}</td>
                    <td class="py-3 px-4">$${parseFloat(service.min_price).toFixed(2)}</td>
                    <td class="py-3 px-4">$${parseFloat(service.max_price).toFixed(2)}</td>
                    <td class="py-3 px-4">
                        <img src="${service.image_url || 'https://via.placeholder.com/50'}" alt="Service Image" class="w-12 h-12 rounded">
                    </td>
                    <td class="py-3 px-4 ${service.is_visible ? 'text-green-500' : 'text-red-500'}">${service.is_visible ? 'Visible' : 'Hidden'}</td>
                    <td class="py-3 px-4">${service.display_order || ''}</td>
                </tr>
            `;
        }

        function populateFeaturesTable(features) {
            const tableBody = document.getElementById('featuresTableBody');
            tableBody.innerHTML = '';
            features.forEach(feature => {
                const row = document.createElement('tr');
                row.className = 'border-b';
                row.innerHTML = `
                    <td class="py-3 px-4">${feature.name}</td>
                    <td class="py-3 px-4">${feature.description}</td>
                    <td class="py-3 px-4 ${feature.is_active ? 'text-green-500' : 'text-red-500'}">${feature.is_active ? 'Yes' : 'No'}</td>
                    <td class="py-3 px-4">${feature.created_at}</td>
                    <td class="py-3 px-4">
                        <button onclick="openFeatureModal(${feature.id}, '${feature.name}', '${feature.description}', ${feature.is_active})" class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">Edit</button>
                        <button onclick="deleteFeature(${feature.id})" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 ml-2">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function populatePricingTable(pricingPackages) {
            const tableBody = document.getElementById('pricingTableBody');
            tableBody.innerHTML = '';
            pricingPackages.forEach(package => {
                const row = document.createElement('tr');
                row.className = 'border-b';
                row.innerHTML = `
                    <td class="py-3 px-4">${package.title}</td>
                    <td class="py-3 px-4">$${parseFloat(package.price).toFixed(2)}</td>
                    <td class="py-3 px-4">${package.created_at}</td>
                    <td class="py-3 px-4">
                        <button onclick="openPricingModal(${package.pricing_package_id}, '${package.title}', ${package.price})" class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">Edit</button>
                        <button onclick="deletePricing(${package.pricing_package_id})" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 ml-2">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Service Modal
        function openServiceModal() {
            fetch(`${apiBaseUrl}services.php?id=${serviceId}`)
                .then(response => response.json())
                .then(service => {
                    document.getElementById('serviceTitle').value = service.title;
                    document.getElementById('serviceDescription').value = service.description;
                    document.getElementById('serviceOverview').value = service.overview || '';
                    document.getElementById('serviceMinPrice').value = service.min_price;
                    document.getElementById('serviceMaxPrice').value = service.max_price;
                    document.getElementById('serviceVisibility').value = service.is_visible;
                    document.getElementById('serviceDisplayOrder').value = service.display_order || '';
                    document.getElementById('serviceImagePreview').src = service.image_url || 'https://via.placeholder.com/50';
                    document.getElementById('serviceModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching service for edit:', error);
                    showError('Error loading service data: ' + error.message);
                });
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.add('hidden');
            document.getElementById('serviceImage').value = ''; // Reset file input
        }

        async function saveService() {
            const formData = new FormData();
            formData.append('title', document.getElementById('serviceTitle').value);
            formData.append('description', document.getElementById('serviceDescription').value);
            formData.append('overview', document.getElementById('serviceOverview').value);
            formData.append('min_price', document.getElementById('serviceMinPrice').value);
            formData.append('max_price', document.getElementById('serviceMaxPrice').value);
            formData.append('is_visible', document.getElementById('serviceVisibility').value);
            formData.append('display_order', document.getElementById('serviceDisplayOrder').value);
            formData.append('_method', 'PUT'); // Add _method=PUT
            const imageFile = document.getElementById('serviceImage').files[0];
            if (imageFile) {
                formData.append('image_url', imageFile);
            }

            try {
                const response = await fetch(`${apiBaseUrl}services.php?id=${serviceId}`, {
                    method: 'POST', // Change to POST
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || `Failed to update service (Status: ${response.status})`);
                }
                alert('Service updated successfully!');
                fetchServiceDetails();
                closeServiceModal();
            } catch (error) {
                console.error('Error updating service:', error);
                showError('Error updating service: ' + error.message);
            }
        }

        // Feature Modals
        function openFeatureModal(id, name, description, isActive) {
            document.getElementById('featureId').value = id;
            document.getElementById('featureName').value = name;
            document.getElementById('featureDescription').value = description;
            document.getElementById('featureIsActive').value = isActive;
            document.getElementById('featureModal').classList.remove('hidden');
        }

        function openNewFeatureModal() {
            document.getElementById('newFeatureName').value = '';
            document.getElementById('newFeatureDescription').value = '';
            document.getElementById('newFeatureIsActive').value = '1';
            document.getElementById('newFeatureModal').classList.remove('hidden');
        }

        function closeFeatureModal() {
            document.getElementById('featureModal').classList.add('hidden');
        }

        function closeNewFeatureModal() {
            document.getElementById('newFeatureModal').classList.add('hidden');
        }

        async function saveFeature() {
            const id = document.getElementById('featureId').value;
            const name = document.getElementById('featureName').value.trim();
            const description = document.getElementById('featureDescription').value.trim();
            const isActive = document.getElementById('featureIsActive').value;

            // Validate inputs
            if (!name) {
                showError('Feature name is required');
                return;
            }
            if (!['0', '1'].includes(isActive)) {
                showError('Invalid active status');
                return;
            }

            // Build FormData
            const formData = new FormData();
            formData.append('name', name);
            if (description) {
                formData.append('description', description);
            }
            formData.append('is_active', isActive);
            formData.append('_method', 'PUT'); // Explicitly include _method

            // Log FormData for debugging
            const formDataEntries = [...formData.entries()];
            console.log('FormData being sent:', formDataEntries);

            try {
                const response = await fetch(`${apiBaseUrl}service_features.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('API response:', result); // Log full response
                if (!response.ok) {
                    throw new Error(result.errors ? result.errors.join(', ') : (result.error || 'Failed to update feature'));
                }
                alert('Feature updated successfully!');
                fetchServiceDetails();
                closeFeatureModal();
            } catch (error) {
                console.error('Error updating feature:', error);
                showError('Error updating feature: ' + error.message);
            }
        }

        async function addNewFeature() {
            const formData = new FormData();
            formData.append('name', document.getElementById('newFeatureName').value);
            formData.append('description', document.getElementById('newFeatureDescription').value);
            formData.append('is_active', document.getElementById('newFeatureIsActive').value);
            formData.append('service_id', serviceId);

            try {
                const response = await fetch(`${apiBaseUrl}service_features.php`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to add feature');
                }
                alert('Feature added successfully!');
                fetchServiceDetails();
                closeNewFeatureModal();
            } catch (error) {
                console.error('Error adding feature:', error);
                showError('Error adding feature: ' + error.message);
            }
        }

        async function deleteFeature(id) {
            if (confirm('Are you sure you want to delete this feature?')) {
                try {
                    const response = await fetch(`${apiBaseUrl}service_features.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to delete feature');
                    }
                    alert('Feature deleted successfully!');
                    fetchServiceDetails();
                } catch (error) {
                    console.error('Error deleting feature:', error);
                    showError('Error deleting feature: ' + error.message);
                }
            }
        }

        // Pricing Package Modals
        function openPricingModal(id, title, price) {
            document.getElementById('pricingId').value = id;
            document.getElementById('pricingTitle').value = title;
            document.getElementById('pricingPrice').value = price;
            document.getElementById('pricingModal').classList.remove('hidden');
        }

        function openNewPricingModal() {
            document.getElementById('newPricingTitle').value = '';
            document.getElementById('newPricingPrice').value = '';
            document.getElementById('newPricingModal').classList.remove('hidden');
        }

        function closePricingModal() {
            document.getElementById('pricingModal').classList.add('hidden');
        }

        function closeNewPricingModal() {
            document.getElementById('newPricingModal').classList.add('hidden');
        }

        async function savePricing() {
            const id = document.getElementById('pricingId').value;
            const title = document.getElementById('pricingTitle').value.trim();
            const price = document.getElementById('pricingPrice').value;

            // Validate inputs
            if (!title) {
                showError('Pricing package tile is required');
                return;
            }

            // Build FormData
            const formData = new FormData();
            formData.append('title', title);
            if (price) {
                formData.append('price', price);
            }
            formData.append('_method', 'PUT'); // Explicitly include _method

            // Log FormData for debugging
            const formDataEntries = [...formData.entries()];
            console.log('FormData being sent:', formDataEntries);

            try {
                const response = await fetch(`${apiBaseUrl}pricing_packages.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('API response:', result); // Log full response
                if (!response.ok) {
                    throw new Error(result.errors ? result.errors.join(', ') : (result.error || 'Failed to update Pricing Package'));
                }
                alert('Pricing Package updated successfully!');
                fetchServiceDetails();
                closePricingModal();
            } catch (error) {
                console.error('Error updating Pricing Package:', error);
                showError('Error updating Pricing Package: ' + error.message);
            }
        }

        async function addNewPricing() {
            const formData = new FormData();
            formData.append('title', document.getElementById('newPricingTitle').value);
            formData.append('price', document.getElementById('newPricingPrice').value);
            formData.append('service_id', serviceId);

            try {
                const response = await fetch(`${apiBaseUrl}pricing_packages.php`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to add pricing package');
                }
                alert('Pricing package added successfully!');
                fetchServiceDetails();
                closeNewPricingModal();
            } catch (error) {
                console.error('Error adding pricing package:', error);
                showError('Error adding pricing package: ' + error.message);
            }
        }

        async function deletePricing(id) {
            if (confirm('Are you sure you want to delete this pricing package?')) {
                try {
                    const response = await fetch(`${apiBaseUrl}pricing_packages.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to delete pricing package');
                    }
                    alert('Pricing package deleted successfully!');
                    fetchServiceDetails();
                } catch (error) {
                    console.error('Error deleting pricing package:', error);
                    showError('Error deleting pricing package: ' + error.message);
                }
            }
        }

        // Image Preview for Service Edit
        const serviceImageInput = document.getElementById('serviceImage');
        const serviceImagePreview = document.getElementById('serviceImagePreview');

        serviceImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    serviceImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Fetch service details on page load
        document.addEventListener('DOMContentLoaded', fetchServiceDetails);
    </script>
</body>
</html>