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
    <title>My Projects - Pixel IT Solution</title>
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
                <!-- Projects Table -->
                <div class="bg-white rounded-lg p-6 shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">My Projects</h2>
                        <a href="project-insert.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Project</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">Project ID</th>
                                    <th class="py-3 px-4">Title</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Service</th>
                                    <th class="py-3 px-4">Image URL</th>
                                    <th class="py-3 px-4">Is Visible</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="projects-table-body">
                                <!-- Projects will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="error-message" class="hidden text-red-500 mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // Fetch and store services for mapping service_id to service title
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
                // Create a map of service_id to service title
                const serviceMap = {};
                services.forEach(service => {
                    serviceMap[service.service_id] = service.title;
                });
                return serviceMap;
            } catch (error) {
                console.error('Error fetching services:', error);
                document.getElementById('error-message').textContent = `Error fetching services: ${error.message}`;
                document.getElementById('error-message').classList.remove('hidden');
                return {};
            }
        }

        // Delete a project
        async function deleteProject(projectId) {
            if (!confirm(`Are you sure you want to delete project ID ${projectId}?`)) {
                return;
            }

            try {
                const response = await fetch(`${apiBaseUrl}projects.php?id=${projectId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `Failed to delete project (Status: ${response.status})`);
                }

                // Refresh the projects table
                await fetchProjects();
                document.getElementById('error-message').textContent = 'Project deleted successfully';
                document.getElementById('error-message').classList.remove('hidden');
                document.getElementById('error-message').classList.remove('text-red-500');
                document.getElementById('error-message').classList.add('text-green-500');
                // Reset message after 3 seconds
                setTimeout(() => {
                    document.getElementById('error-message').classList.add('hidden');
                    document.getElementById('error-message').classList.remove('text-green-500');
                    document.getElementById('error-message').classList.add('text-red-500');
                }, 3000);
            } catch (error) {
                console.error('Error deleting project:', error);
                document.getElementById('error-message').textContent = `Error: ${error.message}`;
                document.getElementById('error-message').classList.remove('hidden');
            }
        }

        // Fetch and populate projects
        async function fetchProjects() {
            const tableBody = document.getElementById('projects-table-body');
            const errorMessage = document.getElementById('error-message');

            try {
                // Fetch services first to get service titles
                const serviceMap = await fetchServices();

                const response = await fetch(`${apiBaseUrl}projects.php`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error(`Failed to fetch projects (Status: ${response.status})`);
                }

                const projects = await response.json();

                // Clear existing rows
                tableBody.innerHTML = '';

                // Populate table
                projects.forEach(project => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="py-3 px-4">${project.project_id}</td>
                        <td class="py-3 px-4">${project.title.substring(0, 30)}${project.title.length > 30 ? '...' : ''}</td>
                        <td class="py-3 px-4">${project.description.substring(0, 30)}${project.description.length > 30 ? '...' : ''}</td>
                        <td class="py-3 px-4">${serviceMap[project.service_id] || 'Unknown Service'}</td>
                        <td class="py-3 px-4">
                            ${project.image_url ? `<a href="${project.image_url}" target="_blank">View Image</a>` : 'No Image'}
                        </td>
                        <td class="py-3 px-4 ${project.is_visible ? 'text-green-500' : 'text-red-500'}">
                            ${project.is_visible ? 'True' : 'False'}
                        </td>
                        <td class="py-3 px-4 space-x-2">
                            <a href="project-details.php?id=${project.project_id}" class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600">Edit</a>
                            <button onclick="deleteProject(${project.project_id})" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                // If no projects, show a message
                if (projects.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="py-3 px-4 text-center text-gray-500">No projects found.</td>
                        </tr>
                    `;
                }

            } catch (error) {
                console.error('Error fetching projects:', error);
                errorMessage.textContent = `Error: ${error.message}`;
                errorMessage.classList.remove('hidden');
            }
        }

        // Fetch projects when the page loads
        document.addEventListener('DOMContentLoaded', fetchProjects);
    </script>
</body>
</html>