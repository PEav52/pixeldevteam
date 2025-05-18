<?php
// Set timezone to Phnom Penh, Cambodia
date_default_timezone_set('Asia/Phnom_Penh');
// echo date('Y-m-d H:i:s'); // current PHP datetime

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
    <title>Project Details - Pixel IT Solution</title>
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
                <a href="projects.php" class="flex items-center text-pink-400 space-x-2">
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
                <!-- Error Message -->
                <div id="error-message" class="hidden text-red-500 mb-4"></div>

                <!-- Project Details Form -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Edit Project</h2>
                    <form id="project-form" enctype="multipart/form-data">
                        <input type="hidden" name="project_id" id="project-id">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" id="project-title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Image</label>
                                <input type="file" name="image_url" id="imageInput" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <div class="mt-4">
                                    <img id="imagePreview" class="max-w-xs h-20 rounded-lg hidden" alt="Image Preview">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="project-description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Overview</label>
                            <textarea name="overview" id="project-overview" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Service ID</label>
                            <select name="service_id" id="project-service" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="">Select a service</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        <br>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Development Process</label>
                            <textarea name="development_process" id="project-process" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Client Quote</label>
                            <textarea name="client_quote" id="project-quote" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Is Visible</label>
                            <input type="checkbox" name="is_visible" id="project-visible" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <hr>
                        <br>
                        <button type="button" onclick="updateProject()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                    </form>
                </div>

                <!-- Project Features Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Project Features</h2>
                        <button onclick="openModal('add-feature-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Feature</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Feature ID</th>
                                    <th class="py-3 px-4">Feature Name</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="features-table-body">
                                <!-- Features will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Technologies Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Technologies</h2>
                        <button onclick="openModal('add-technology-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Technology</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Technology ID</th>
                                    <th class="py-3 px-4">Technology Name</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="technologies-table-body">
                                <!-- Technologies will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Visual Assets Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Visual Assets</h2>
                        <button onclick="openModal('add-asset-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Asset</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Asset ID</th>
                                    <th class="py-3 px-4">Image Preview</th>
                                    <th class="py-3 px-4">Alt Text</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="assets-table-body">
                                <!-- Assets will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="bg-white rounded-lg p-6 shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Results</h2>
                        <button onclick="openModal('add-result-modal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Result</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Result ID</th>
                                    <th class="py-3 px-4">Metric Value</th>
                                    <th class="py-3 px-4">Metric Description</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="results-table-body">
                                <!-- Results will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Feature Modal -->
                <div id="add-feature-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Add Feature</h3>
                        <form id="add-feature-form">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Feature Name</label>
                                <input type="text" name="p_feature_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="p_feature_description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('add-feature-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="addFeature()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Feature Modal -->
                <div id="edit-feature-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Edit Feature</h3>
                        <form id="edit-feature-form">
                            <input type="hidden" name="p_feature_id" id="edit-feature-id">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Feature Name</label>
                                <input type="text" name="p_feature_name" id="edit-feature-name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="p_feature_description" id="edit-feature-description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('edit-feature-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="updateFeature()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add Technology Modal -->
                <div id="add-technology-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Add Technology</h3>
                        <form id="add-technology-form">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Technology Name</label>
                                <input type="text" name="technology_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('add-technology-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="addTechnology()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Technology Modal -->
                <div id="edit-technology-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Edit Technology</h3>
                        <form id="edit-technology-form">
                            <input type="hidden" name="technology_id" id="edit-technology-id">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Technology Name</label>
                                <input type="text" name="technology_name" id="edit-technology-name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('edit-technology-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="updateTechnology()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add Visual Asset Modal -->
                <div id="add-asset-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Add Visual Asset</h3>
                        <form id="add-asset-form" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Image</label>
                                <input type="file" name="image_url" id="add-asset-image-input" accept="image/*" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <div class="mt-4">
                                    <img id="add-asset-image-preview" class="max-w-xs h-20 rounded-lg hidden" alt="Image Preview">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Alt Text</label>
                                <input type="text" name="alt_text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('add-asset-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="addAsset()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Visual Asset Modal -->
                <div id="edit-asset-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Edit Visual Asset</h3>
                        <form id="edit-asset-form" enctype="multipart/form-data">
                            <input type="hidden" name="asset_id" id="edit-asset-id">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Image</label>
                                <input type="file" name="image_url" id="edit-asset-image-input" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <div class="mt-4">
                                    <img id="edit-asset-image-preview" class="max-w-xs h-20 rounded-lg" alt="Image Preview">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Alt Text</label>
                                <input type="text" name="alt_text" id="edit-asset-alt" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('edit-asset-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="updateAsset()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add Result Modal -->
                <div id="add-result-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Add Result</h3>
                        <form id="add-result-form">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Metric Value</label>
                                <input type="text" name="metric_value" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Metric Description</label>
                                <textarea name="metric_description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('add-result-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="addResult()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Result Modal -->
                <div id="edit-result-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Edit Result</h3>
                        <form id="edit-result-form">
                            <input type="hidden" name="result_id" id="edit-result-id">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Metric Value</label>
                                <input type="text" name="metric_value" id="edit-result-value" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Metric Description</label>
                                <textarea name="metric_description" id="edit-result-description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('edit-result-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="button" onclick="updateResult()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';
        let projectId;

        // Get project ID from URL
        function getProjectIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        // Fetch and populate services
        async function fetchServices() {
            try {
                const response = await fetch(`${apiBaseUrl}services.php`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch services (Status: ${response.status})`);
                }

                const services = await response.json();
                console.log('Fetched services:', services); // Log services data for debugging

                const serviceSelect = document.getElementById('project-service');
                serviceSelect.innerHTML = '<option value="">Select a service</option>'; // Reset options
                services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.service_id;
                    option.textContent = service.title;
                    serviceSelect.appendChild(option);
                });
            } catch (error) {
                showError(`Error fetching services: ${error.message}`);
            }
        }

        // Fetch and populate project details
        async function fetchProjectDetails() {
            projectId = getProjectIdFromUrl();
            if (!projectId) {
                showError('Project ID is missing from URL');
                return;
            }

            try {
                const response = await fetch(`${apiBaseUrl}projects.php?id=${projectId}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch project (Status: ${response.status})`);
                }

                const project = await response.json();
                console.log('Fetched project:', project); // Log project data for debugging

                // Populate project form
                document.getElementById('project-id').value = project.project_id || '';
                document.getElementById('project-title').value = project.title || '';
                document.getElementById('project-description').value = project.description || '';
                document.getElementById('project-overview').value = project.overview || '';
                document.getElementById('project-service').value = project.service_id || '';
                document.getElementById('project-process').value = project.development_process || '';
                document.getElementById('project-quote').value = project.client_quote || '';
                document.getElementById('project-visible').checked = project.is_visible === 1;
                if (project.image_url) {
                    document.getElementById('imagePreview').src = project.image_url;
                    document.getElementById('imagePreview').classList.remove('hidden');
                }

                // Populate features table
                const featuresTableBody = document.getElementById('features-table-body');
                featuresTableBody.innerHTML = '';
                project.features.forEach(feature => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${feature.p_feature_id}</td>
                        <td class="py-3 px-4">${feature.p_feature_name}</td>
                        <td class="py-3 px-4">${feature.p_feature_description}</td>
                        <td class="py-3 px-4">
                            <button onclick="openEditFeatureModal(${feature.p_feature_id}, '${feature.p_feature_name}', '${feature.p_feature_description}')" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 mr-2">Edit</button>
                            <button onclick="deleteFeature(${feature.p_feature_id})" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">Delete</button>
                        </td>
                    `;
                    featuresTableBody.appendChild(row);
                });

                // Populate technologies table
                const technologiesTableBody = document.getElementById('technologies-table-body');
                technologiesTableBody.innerHTML = '';
                project.technologies.forEach(tech => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${tech.technology_id}</td>
                        <td class="py-3 px-4">${tech.technology_name}</td>
                        <td class="py-3 px-4">
                            <button onclick="openEditTechnologyModal(${tech.technology_id}, '${tech.technology_name}')" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 mr-2">Edit</button>
                            <button onclick="deleteTechnology(${tech.technology_id})" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">Delete</button>
                        </td>
                    `;
                    technologiesTableBody.appendChild(row);
                });

                // Populate assets table
                const assetsTableBody = document.getElementById('assets-table-body');
                assetsTableBody.innerHTML = '';
                project.assets.forEach(asset => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${asset.asset_id}</td>
                        <td class="py-3 px-4"><img src="${asset.image_url}" class="max-w-xs h-20 rounded-lg" alt="${asset.alt_text}"></td>
                        <td class="py-3 px-4">${asset.alt_text}</td>
                        <td class="py-3 px-4">
                            <button onclick="openEditAssetModal(${asset.asset_id}, '${asset.image_url}', '${asset.alt_text}')" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 mr-2">Edit</button>
                            <button onclick="deleteAsset(${asset.asset_id})" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">Delete</button>
                        </td>
                    `;
                    assetsTableBody.appendChild(row);
                });

                // Populate results table
                const resultsTableBody = document.getElementById('results-table-body');
                resultsTableBody.innerHTML = '';
                project.results.forEach(result => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${result.result_id}</td>
                        <td class="py-3 px-4">${result.metric_value}</td>
                        <td class="py-3 px-4">${result.metric_description}</td>
                        <td class="py-3 px-4">
                            <button onclick="openEditResultModal(${result.result_id}, '${result.metric_value}', '${result.metric_description}')" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 mr-2">Edit</button>
                            <button onclick="deleteResult(${result.result_id})" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">Delete</button>
                        </td>
                    `;
                    resultsTableBody.appendChild(row);
                });

            } catch (error) {
                showError(`Error fetching project details: ${error.message}`);
            }
        }

        // project update function
        async function updateProject() {
            const form = document.getElementById('project-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            formData.append('_method', 'PUT');
            
            // Properly handle the checkbox value
            const isVisible = document.getElementById('project-visible').checked ? 1 : 0;
            formData.set('is_visible', isVisible);

            try {
                const response = await fetch(`${apiBaseUrl}projects.php?id=${projectId}`, {
                    method: 'POST', // Using POST with _method=PUT
                    body: formData
                });

                const result = await response.json();
                
                if (!response.ok) {
                    let errorMessage = result.error || 'Failed to update project';
                    if (result.errors && Array.isArray(result.errors)) {
                        errorMessage = result.errors.join(', ');
                    }
                    throw new Error(errorMessage);
                }

                alert('Project updated successfully!');
                fetchProjectDetails(); // Refresh data
            } catch (error) {
                showError(`Error updating project: ${error.message}`);
            }
        }

        // Add feature
        async function addFeature() {
            const form = document.getElementById('add-feature-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const featureData = {
                p_feature_name: formData.get('p_feature_name'),
                p_feature_description: formData.get('p_feature_description')
            };

            try {
                const response = await fetch(`${apiBaseUrl}features.php?project_id=${projectId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(featureData)
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to add feature');
                }

                closeModal('add-feature-modal');
                form.reset();
                fetchProjectDetails();
                alert('Feature added successfully!');
            } catch (error) {
                showError(`Error adding feature: ${error.message}`);
            }
        }

        // Update feature
        async function updateFeature() {
            const form = document.getElementById('edit-feature-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const featureData = {
                p_feature_name: formData.get('p_feature_name'),
                p_feature_description: formData.get('p_feature_description')
            };

            try {
                const response = await fetch(`${apiBaseUrl}features.php?id=${formData.get('p_feature_id')}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(featureData)
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to update feature');
                }

                closeModal('edit-feature-modal');
                fetchProjectDetails();
                alert('Feature updated successfully!');
            } catch (error) {
                showError(`Error updating feature: ${error.message}`);
            }
        }

        // Delete feature
        async function deleteFeature(id) {
            if (!confirm('Are you sure you want to delete this feature?')) return;

            try {
                const response = await fetch(`${apiBaseUrl}features.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to delete feature');
                }

                fetchProjectDetails();
                alert('Feature deleted successfully!');
            } catch (error) {
                showError(`Error deleting feature: ${error.message}`);
            }
        }

        // Add technology
        async function addTechnology() {
            const form = document.getElementById('add-technology-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const techData = {
                technology_name: formData.get('technology_name')
            };

            try {
                const response = await fetch(`${apiBaseUrl}technologies.php?project_id=${projectId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(techData)
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to add technology');
                }

                closeModal('add-technology-modal');
                form.reset();
                fetchProjectDetails();
                alert('Technology added successfully!');
            } catch (error) {
                showError(`Error adding technology: ${error.message}`);
            }
        }

        // Update technology
        async function updateTechnology() {
            const form = document.getElementById('edit-technology-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const techData = {
                technology_name: formData.get('technology_name')
            };

            try {
                const response = await fetch(`${apiBaseUrl}technologies.php?id=${formData.get('technology_id')}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(techData)
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to update technology');
                }

                closeModal('edit-technology-modal');
                fetchProjectDetails();
                alert('Technology updated successfully!');
            } catch (error) {
                showError(`Error updating technology: ${error.message}`);
            }
        }

        // Delete technology
        async function deleteTechnology(id) {
            if (!confirm('Are you sure you want to delete this technology?')) return;

            try {
                const response = await fetch(`${apiBaseUrl}technologies.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to delete technology');
                }

                fetchProjectDetails();
                alert('Technology deleted successfully!');
            } catch (error) {
                showError(`Error deleting technology: ${error.message}`);
            }
        }

        // Add visual asset
        async function addAsset() {
            const form = document.getElementById('add-asset-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);

            try {
                const response = await fetch(`${apiBaseUrl}assets.php?project_id=${projectId}`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to add asset');
                }

                closeModal('add-asset-modal');
                form.reset();
                document.getElementById('add-asset-image-preview').classList.add('hidden');
                fetchProjectDetails();
                alert('Asset added successfully!');
            } catch (error) {
                showError(`Error adding asset: ${error.message}`);
            }
        }

        // Update visual asset
        async function updateAsset() {
            const form = document.getElementById('edit-asset-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const assetId = formData.get('asset_id');

            // Ensure alt_text is included
            if (!formData.get('alt_text')) {
                showError('Alt text is required');
                return;
            }

            try {
                formData.append('_method', 'PUT');

                const response = await fetch(`${apiBaseUrl}assets.php?id=${assetId}`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to update asset');
                }

                closeModal('edit-asset-modal');
                fetchProjectDetails();
                alert('Asset updated successfully!');
            } catch (error) {
                showError(`Error updating asset: ${error.message}`);
            }
        }

        // Delete visual asset
        async function deleteAsset(id) {
            if (!confirm('Are you sure you want to delete this asset?')) return;

            try {
                const response = await fetch(`${apiBaseUrl}assets.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to delete asset');
                }

                fetchProjectDetails();
                alert('Asset deleted successfully!');
            } catch (error) {
                showError(`Error deleting asset: ${error.message}`);
            }
        }

        // Add result
        async function addResult() {
            const form = document.getElementById('add-result-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const resultData = {
                metric_value: formData.get('metric_value'),
                metric_description: formData.get('metric_description')
            };

            try {
                const response = await fetch(`${apiBaseUrl}results.php?project_id=${projectId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(resultData)
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to add result');
                }

                closeModal('add-result-modal');
                form.reset();
                fetchProjectDetails();
                alert('Result added successfully!');
            } catch (error) {
                showError(`Error adding result: ${error.message}`);
            }
        }

        // Update result
        async function updateResult() {
            const form = document.getElementById('edit-result-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const resultData = {
                metric_value: formData.get('metric_value'),
                metric_description: formData.get('metric_description')
            };

            try {
                const response = await fetch(`${apiBaseUrl}results.php?id=${formData.get('result_id')}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(resultData)
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to update result');
                }

                closeModal('edit-result-modal');
                fetchProjectDetails();
                alert('Result updated successfully!');
            } catch (error) {
                showError(`Error updating result: ${error.message}`);
            }
        }

        // Delete result
        async function deleteResult(id) {
            if (!confirm('Are you sure you want to delete this result?')) return;

            try {
                const response = await fetch(`${apiBaseUrl}results.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Failed to delete result');
                }

                fetchProjectDetails();
                alert('Result deleted successfully!');
            } catch (error) {
                showError(`Error deleting result: ${error.message}`);
            }
        }

        // Image preview handlers
        function setupImagePreviews() {
            // Project form image preview
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
                    imagePreview.classList.add('hidden');
                }
            });

            // Add asset image preview
            const addAssetImageInput = document.getElementById('add-asset-image-input');
            const addAssetImagePreview = document.getElementById('add-asset-image-preview');
            addAssetImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        addAssetImagePreview.src = e.target.result;
                        addAssetImagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    addAssetImagePreview.classList.add('hidden');
                }
            });

            // Edit asset image preview
            const editAssetImageInput = document.getElementById('edit-asset-image-input');
            const editAssetImagePreview = document.getElementById('edit-asset-image-preview');
            editAssetImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        editAssetImagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Show error message
        function showError(message) {
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
        }

        // Open modal by ID
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        // Close modal by ID
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Open edit feature modal and pre-fill fields
        function openEditFeatureModal(id, name, description) {
            document.getElementById('edit-feature-id').value = id;
            document.getElementById('edit-feature-name').value = name;
            document.getElementById('edit-feature-description').value = description;
            openModal('edit-feature-modal');
        }

        // Open edit technology modal and pre-fill fields
        function openEditTechnologyModal(id, name) {
            document.getElementById('edit-technology-id').value = id;
            document.getElementById('edit-technology-name').value = name;
            openModal('edit-technology-modal');
        }

        // Open edit asset modal and pre-fill fields
        function openEditAssetModal(id, image, alt) {
            document.getElementById('edit-asset-id').value = id;
            document.getElementById('edit-asset-image-preview').src = image;
            document.getElementById('edit-asset-alt').value = alt;
            document.getElementById('edit-asset-image-input').value = ''; // Clear file input
            openModal('edit-asset-modal');
        }

        // Open edit result modal and pre-fill fields
        function openEditResultModal(id, value, description) {
            document.getElementById('edit-result-id').value = id;
            document.getElementById('edit-result-value').value = value;
            document.getElementById('edit-result-description').value = description;
            openModal('edit-result-modal');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            setupImagePreviews();
            fetchServices(); // Fetch services on page load
            fetchProjectDetails();
        });
    </script>
</body>
</html>