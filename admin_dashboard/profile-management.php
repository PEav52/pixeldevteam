<?php
// profile-management.php
// Admin page for managing team members
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
    <title>Profile Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="domain_checks.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🌐</span><span>Domain Checks</span>
                </a>
                <a href="setting.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>⚙️</span><span>Settings</span>
                </a>
                <a href="profile-management.php" class="flex items-center space-x-2 text-pink-400">
                    <span>👤</span><span>Profile Management</span>
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
                <h1 class="text-2xl font-bold text-blue-700 mb-8">Team Profile Management</h1>
                <!-- Add/Edit Team Member Form -->
                <div class="bg-white p-6 rounded-lg shadow mb-10">
                    <h2 class="text-xl font-semibold mb-4">Add / Edit Team Member</h2>
                    <form id="team-member-form" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-1 font-medium">Full Name</label>
                            <input type="text" name="full_name" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">Role</label>
                            <input type="text" name="role" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 font-medium">Description</label>
                            <textarea name="description" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">LinkedIn URL</label>
                            <input type="url" name="linkedin" class="w-full border rounded px-3 py-2" pattern="https?://(www\.)?linkedin\.com/.*">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">GitHub URL</label>
                            <input type="url" name="github" class="w-full border rounded px-3 py-2" pattern="https?://(www\.)?github\.com/.*">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium">Profile Image</label>
                            <input type="file" name="profile_image" accept="image/*" class="w-full">
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" id="submit-btn" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800 disabled:bg-gray-500" disabled="false">Save Member</button>
                        </div>
                    </form>
                </div>
                <!-- Team Members List -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Current Team Members</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 border">Image</th>
                                    <th class="px-4 py-2 border">Full Name</th>
                                    <th class="px-4 py-2 border">Role</th>
                                    <th class="px-4 py-2 border">Description</th>
                                    <th class="px-4 py-2 border">LinkedIn</th>
                                    <th class="px-4 py-2 border">GitHub</th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="team-members-list">
                                <!-- Team members will be listed here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Team Member</h3>
                <form id="edit-form" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block mb-1 font-medium">Full Name</label>
                        <input type="text" name="full_name" id="edit-full-name" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Role</label>
                        <input type="text" name="role" id="edit-role" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Description</label>
                        <textarea name="description" id="edit-description" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">LinkedIn URL</label>
                        <input type="url" name="linkedin" id="edit-linkedin" class="w-full border rounded px-3 py-2" pattern="https?://(www\.)?linkedin\.com/.*">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">GitHub URL</label>
                        <input type="url" name="github" id="edit-github" class="w-full border rounded px-3 py-2" pattern="https?://(www\.)?github\.com/.*">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Profile Image</label>
                        <input type="file" name="profile_image" accept="image/*" class="w-full">
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        <button type="submit" id="edit-submit-btn" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 disabled:bg-gray-500" disabled="false">Update Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        const form = document.getElementById('team-member-form');
        const membersList = document.getElementById('team-members-list');
        const editModal = document.getElementById('edit-modal');
        const editForm = document.getElementById('edit-form');
        const submitBtn = document.getElementById('submit-btn');
        const editSubmitBtn = document.getElementById('edit-submit-btn');

        // Validate URL format
        function isValidUrl(url, domain) {
            if (!url) return true; // Allow empty URLs
            try {
                const pattern = new RegExp(`^https?://(www\\.)?${domain}\\.com/.*$`, 'i');
                return pattern.test(url);
            } catch (e) {
                return false;
            }
        }

        // Load team members on page load
        document.addEventListener('DOMContentLoaded', loadTeamMembers);

        // Load all team members
        async function loadTeamMembers() {
            try {
                const response = await fetch('api/team_members.php');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                displayTeamMembers(data);
            } catch (error) {
                console.error('Error loading team members:', error);
                alert('Failed to load team members: ' + error.message);
            }
        }

        // Display team members in the table
        function displayTeamMembers(members) {
            membersList.innerHTML = members.map(member => `
                <tr>
                    <td class="px-4 py-2 border text-center">
                        <img src="${member.image_url || '../images/default-profile.jpg'}" 
                             class="w-12 h-12 rounded-full mx-auto object-cover">
                    </td>
                    <td class="px-4 py-2 border">${member.full_name}</td>
                    <td class="px-4 py-2 border">${member.role}</td>
                    <td class="px-4 py-2 border">${member.description}</td>
                    <td class="px-4 py-2 border text-center">
                        ${member.linkedin ? `<a href="${member.linkedin}" target="_blank" class="text-blue-700">
                            <i class="fab fa-linkedin"></i>
                        </a>` : '-'}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        ${member.github ? `<a href="${member.github}" target="_blank" class="text-gray-700">
                            <i class="fab fa-github"></i>
                        </a>` : '-'}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        <button onclick="openEditModal(${member.id})" class="text-yellow-500 hover:underline mr-2">
                            Edit
                        </button>
                        <button onclick="deleteMember(${member.id})" class="text-red-500 hover:underline">
                            Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Handle new member form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            const formData = new FormData(form);
            const linkedin = formData.get('linkedin');
            const github = formData.get('github');

            // Validate URLs
            if (linkedin && !isValidUrl(linkedin, 'linkedin')) {
                alert('Please enter a valid LinkedIn URL');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Member';
                return;
            }
            if (github && !isValidUrl(github, 'github')) {
                alert('Please enter a valid GitHub URL');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Member';
                return;
            }

            // Client-side validation
            if (!formData.get('full_name') || !formData.get('role') || !formData.get('description')) {
                alert('Full name, role, and description are required');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Member';
                return;
            }

            try {
                const response = await fetch('api/team_members.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();

                if (data.success) {
                    alert('Member added successfully!');
                    form.reset();
                    loadTeamMembers();
                } else {
                    throw new Error(data.error || 'Operation failed');
                }
            } catch (error) {
                console.error('Error saving team member:', error);
                alert('Failed to save team member: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Member';
            }
        });

        // Handle edit form submission
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            editSubmitBtn.disabled = true;
            editSubmitBtn.textContent = 'Updating...';

            const formData = new FormData(editForm);
            const id = formData.get('id');
            const linkedin = formData.get('linkedin');
            const github = formData.get('github');

            // Validate URLs
            if (linkedin && !isValidUrl(linkedin, 'linkedin')) {
                alert('Please enter a valid LinkedIn URL');
                editSubmitBtn.disabled = false;
                editSubmitBtn.textContent = 'Update Member';
                return;
            }
            if (github && !isValidUrl(github, 'github')) {
                alert('Please enter a valid GitHub URL');
                editSubmitBtn.disabled = false;
                editSubmitBtn.textContent = 'Update Member';
                return;
            }

            // Client-side validation
            if (!formData.get('full_name') || !formData.get('role') || !formData.get('description')) {
                alert('Full name, role, and description are required');
                editSubmitBtn.disabled = false;
                editSubmitBtn.textContent = 'Update Member';
                return;
            }

            try {
                const response = await fetch(`api/team_members.php?id=${id}`, {
                    method: 'POST', // Use POST with _method override
                    body: formData
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();

                if (data.success) {
                    alert('Member updated successfully!');
                    closeEditModal();
                    loadTeamMembers();
                } else {
                    throw new Error(data.error || 'Operation failed');
                }
            } catch (error) {
                console.error('Error updating team member:', error);
                alert('Failed to update team member: ' + error.message);
            } finally {
                editSubmitBtn.disabled = false;
                editSubmitBtn.textContent = 'Update Member';
            }
        });

        // Open edit modal
        async function openEditModal(id) {
            try {
                const response = await fetch(`api/team_members.php?id=${id}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const member = await response.json();

                if (member && !member.error) {
                    document.getElementById('edit-id').value = member.id || '';
                    document.getElementById('edit-full-name').value = member.full_name || '';
                    document.getElementById('edit-role').value = member.role || '';
                    document.getElementById('edit-description').value = member.description || '';
                    document.getElementById('edit-linkedin').value = member.linkedin || '';
                    document.getElementById('edit-github').value = member.github || '';
                    editModal.classList.remove('hidden');
                } else {
                    throw new Error(member.error || 'Failed to load member details');
                }
            } catch (error) {
                console.error('Error loading member details:', error);
                alert('Failed to load member details: ' + error.message);
            }
        }

        // Close edit modal
        function closeEditModal() {
            editModal.classList.add('hidden');
            editForm.reset();
        }

        // Delete team member
        async function deleteMember(id) {
            if (!confirm('Are you sure you want to delete this team member?')) {
                return;
            }

            try {
                const response = await fetch(`api/team_members.php?id=${id}`, {
                    method: 'DELETE'
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();

                if (data.success) {
                    alert('Member deleted successfully!');
                    loadTeamMembers();
                } else {
                    throw new Error(data.error || 'Delete failed');
                }
            } catch (error) {
                console.error('Error deleting team member:', error);
                alert('Failed to delete team member: ' + error.message);
            }
        }
    </script>
</body>
</html>