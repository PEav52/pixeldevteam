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
    <title>Add New Project - Pixel IT Solution</title>
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
                    <input type="text" placeholder="Search" class="bg-gray-700 text-white rounded-full px-4 py-2 border border-gray-900 focus:outline-none">
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
                <!-- Project Form -->
                <div class="bg-white rounded-lg p-6 shadow mb-6 border border-gray-900">
                    <h2 class="text-lg font-semibold mb-4">Add New Project</h2>
                    <form id="project-form" method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service ID</label>
                                <select name="service_id" id="project-service" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">Select a service</option>
                                    <!-- Options will be populated dynamically -->
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Personal Website">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Image URL</label>
                                <input type="file" name="image_url" id="imageInput" accept="image/*" class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <div class="mt-4">
                                    <img id="imagePreview" class="max-w-xs h-20 rounded-lg hidden" alt="Image Preview">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Is Visible</label>
                                <input type="checkbox" name="is_visible" id="is_visible" value="1" class="block mt-1 h-4 w-4 ounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" checked>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Describe the project..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Overview</label>
                            <textarea name="overview" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Project overview..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Development Process</label>
                            <textarea name="development_process" class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Describe the development process..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Client Quote</label>
                            <textarea name="client_quote" class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Enter client quote..."></textarea>
                        </div>
                    </form>
                </div>

                <script>
                    const apiBaseUrl = 'api/';

                    // Fetch and populate services
                    async function fetchServices() {
                        try {
                            const response = await fetch(`${apiBaseUrl}services.php?method=GET`, {
                                method: 'GET',
                                headers: { 'Content-Type': 'application/json' }
                            });

                            if (!response.ok) {
                                throw new Error(`Failed to fetch services (Status: ${response.status})`);
                            }

                            const services = await response.json();
                            console.log('Fetched services:', services);

                            const serviceSelect = document.getElementById('project-service');
                            serviceSelect.innerHTML = '<option value="">Select a service</option>';
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

                    // Initialize
                    document.addEventListener('DOMContentLoaded', () => {
                        fetchServices(); // Fetch services on page load
                    });
                </script>

                <!-- Project Features Form -->
                <div class="bg-white rounded-lg p-6 shadow mb-6 border border-gray-900">
                    <h2 class="text-lg font-semibold mb-4">Add Project Features</h2>
                    <div id="features-container">
                        <div class="feature-form mb-4">
                            <form method="POST">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Feature Name</label>
                                        <input type="text" name="p_feature_name" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Responsive Design">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <input type="text" name="p_feature_description" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Looks great on all devices">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <button type="button" onclick="addFeatureForm()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Add Another Feature</button>
                </div>

                <!-- Technologies Form -->
                <div class="bg-white rounded-lg p-6 shadow mb-6 border border-gray-900">
                    <h2 class="text-lg font-semibold mb-4">Add Technologies</h2>
                    <div id="technologies-container">
                        <div class="technology-form mb-4">
                            <form method="POST">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Technology Name</label>
                                    <input type="text" name="technology_name" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., HTML5, CSS3, JavaScript">
                                </div>
                            </form>
                        </div>
                    </div>
                    <button type="button" onclick="addTechnologyForm()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Add Another Technology</button>
                </div>

                <!-- Visual Assets Form -->
                <div class="bg-white rounded-lg p-6 shadow mb-6 border border-gray-900">
                    <h2 class="text-lg font-semibold mb-4">Add Visual Assets</h2>
                    <div id="assets-container">
                        <div class="asset-form mb-4">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Image URL</label>
                                        <input type="file" name="image_url" class="visual_imageInput mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" accept="image/*" required>
                                        <div class="mt-4">
                                            <img class="visual_imagePreview max-w-xs h-20 rounded-lg hidden" alt="Image Preview">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Alt Text</label>
                                        <input type="text" name="alt_text" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Project Homepage">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <button type="button" onclick="addAssetForm()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Add Another Asset</button>
                </div>

                <!-- Results Form -->
                <div class="bg-white rounded-lg p-6 shadow mb-6 border border-gray-900">
                    <h2 class="text-lg font-semibold mb-4">Add Results</h2>
                    <div id="results-container">
                        <div class="result-form mb-4">
                            <form method="POST">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Metric Value</label>
                                        <input type="text" name="metric_value" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., 40%">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Metric Description</label>
                                        <input type="text" name="metric_description" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Increase in user engagement">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <button type="button" onclick="addResultForm()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Add Another Result</button>
                </div>

                <!-- Save All Button -->
                <div class="bg-white rounded-lg p-6 shadow border border-gray-900">
                    <button type="button" onclick="submitAllForms()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save All</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Image Preview for Project Form
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

        // Add Forms Dynamically
        function addFeatureForm() {
            const container = document.getElementById('features-container');
            const newForm = document.createElement('div');
            newForm.className = 'feature-form mb-4';
            newForm.innerHTML = `
                <form method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Feature Name</label>
                            <input type="text" name="p_feature_name" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Responsive Design">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="p_feature_description" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Looks great on all devices">
                        </div>
                    </div>
                </form>
            `;
            container.appendChild(newForm);
        }

        function addTechnologyForm() {
            const container = document.getElementById('technologies-container');
            const newForm = document.createElement('div');
            newForm.className = 'technology-form mb-4';
            newForm.innerHTML = `
                <form method="POST">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Technology Name</label>
                        <input type="text" name="technology_name" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., HTML5, CSS3, JavaScript">
                    </div>
                </form>
            `;
            container.appendChild(newForm);
        }

        function addAssetForm() {
            const container = document.getElementById('assets-container');
            const newForm = document.createElement('div');
            newForm.className = 'asset-form mb-4';
            newForm.innerHTML = `
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Image URL</label>
                            <input type="file" name="image_url" class="visual_imageInput mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" accept="image/*" required>
                            <div class="mt-4">
                                <img class="visual_imagePreview max-w-xs h-20 rounded-lg hidden" alt="Image Preview">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alt Text</label>
                            <input type="text" name="alt_text" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Project Homepage">
                        </div>
                    </div>
                </form>
            `;
            container.appendChild(newForm);
            setupImagePreview(newForm);
        }

        function addResultForm() {
            const container = document.getElementById('results-container');
            const newForm = document.createElement('div');
            newForm.className = 'result-form mb-4';
            newForm.innerHTML = `
                <form method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Metric Value</label>
                            <input type="text" name="metric_value" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., 40%">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Metric Description</label>
                            <input type="text" name="metric_description" required class="mt-1 block w-full rounded-md border-gray-900 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="e.g., Increase in user engagement">
                        </div>
                    </div>
                </form>
            `;
            container.appendChild(newForm);
        }

        function setupImagePreview(container) {
            const input = container.querySelector('.visual_imageInput');
            const preview = container.querySelector('.visual_imagePreview');
            
            input.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            });
        }

        // Setup initial visual asset preview
        document.querySelectorAll('.asset-form').forEach(setupImagePreview);

        async function submitAllForms() {
            const apiBaseUrl = 'api/';
            let projectId = null;

            try {
                // Step 1: Submit Project Form
                const projectForm = document.getElementById('project-form');
                if (!projectForm.checkValidity()) {
                    projectForm.reportValidity();
                    throw new Error('Project form validation failed');
                }

                const projectFormData = new FormData(projectForm);
                const projectResponse = await fetch(`${apiBaseUrl}projects.php`, {
                    method: 'POST',
                    body: projectFormData
                });

                const projectResult = await projectResponse.json();
                if (!projectResponse.ok) {
                    throw new Error(projectResult.error || `Failed to create project (Status: ${projectResponse.status})`);
                }

                projectId = projectResult.project_id;
                alert('Project created successfully!');

                // Step 2: Submit Features
                const featureForms = document.querySelectorAll('#features-container form');
                for (const form of featureForms) {
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        throw new Error('Feature form validation failed');
                    }

                    const formData = new FormData(form);
                    const featureData = {
                        p_feature_name: formData.get('p_feature_name'),
                        p_feature_description: formData.get('p_feature_description')
                    };

                    const response = await fetch(`${apiBaseUrl}features.php?project_id=${projectId}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(featureData)
                    });

                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to add feature');
                    }
                }

                // Step 3: Submit Technologies
                const technologyForms = document.querySelectorAll('#technologies-container form');
                for (const form of technologyForms) {
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        throw new Error('Technology form validation failed');
                    }

                    const formData = new FormData(form);
                    const technologyData = {
                        technology_name: formData.get('technology_name')
                    };

                    const response = await fetch(`${apiBaseUrl}technologies.php?project_id=${projectId}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(technologyData)
                    });

                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to add technology');
                    }
                }

                // Step 4: Submit Visual Assets
                const assetForms = document.querySelectorAll('#assets-container form');
                for (const form of assetForms) {
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        throw new Error('Asset form validation failed');
                    }

                    const formData = new FormData(form);
                    const response = await fetch(`${apiBaseUrl}assets.php?project_id=${projectId}`, {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to add asset');
                    }
                }

                // Step 5: Submit Results
                const resultForms = document.querySelectorAll('#results-container form');
                for (const form of resultForms) {
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        throw new Error('Result form validation failed');
                    }

                    const formData = new FormData(form);
                    const resultData = {
                        metric_value: formData.get('metric_value'),
                        metric_description: formData.get('metric_description')
                    };

                    const response = await fetch(`${apiBaseUrl}results.php?project_id=${projectId}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(resultData)
                    });

                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to add result');
                    }
                }

                alert('All data submitted successfully!');
                window.location.href = 'projects.php'; // Redirect to projects page

            } catch (error) {
                console.error('Error submitting forms:', error);
                alert(`Error: ${error.message}`);
            }
        }
    </script>
</body>
</html>