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
    <title>Inquiry Details - Pixel IT Solution</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
                <!-- Inquiry Details -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Inquiry Details</h2>
                        <div class="space-x-2" id="action-buttons">
                            <!-- Buttons populated via JavaScript -->
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="inquiry-details">
                        <!-- Details populated via JavaScript -->
                    </div>
                </div>

                <!-- Reply History -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Reply History</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="reply-history">
                                <!-- Replies populated via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reply Inquiry Modal -->
                <div id="reply-inquiry-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                        <h3 class="text-lg font-semibold mb-4">Reply to Inquiry</h3>
                        <form id="reply-form" enctype="multipart/form-data">
                            <input type="hidden" name="inquiry_id" id="reply-inquiry-id">
                            <input type="hidden" name="recipient_email" id="reply-recipient-email">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" name="subject" id="reply-subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea name="message" id="reply-message" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" rows="5"></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Attach Quote (PDF)</label>
                                <input type="file" name="quote_pdf" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeModal('reply-inquiry-modal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Send Reply</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';
        const urlParams = new URLSearchParams(window.location.search);
        const inquiryId = urlParams.get('id');

        // Show toast notification with Toastify.js
        function showToast(message) {
            Toastify({
                text: message,
                duration: 5000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#16a34a",
                className: "rounded-lg shadow-lg"
            }).showToast();
        }

        // Open/close modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Fetch inquiry details
        async function fetchInquiry() {
            try {
                const response = await fetch(`${apiBaseUrl}inquiries.php?id=${inquiryId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const inquiry = await response.json();

                if (inquiry.error) {
                    alert(`Failed to load inquiry: ${inquiry.error}`);
                    return;
                }

                // Populate details
                const detailsContainer = document.getElementById('inquiry-details');
                detailsContainer.innerHTML = `
                    <div>
                        <p class="text-sm font-medium text-gray-700">Inquiry ID</p>
                        <p class="mt-1 text-gray-900">${inquiry.inquiry_id}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Name</p>
                        <p class="mt-1 text-gray-900">${inquiry.name}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Email</p>
                        <p class="mt-1 text-gray-900">${inquiry.email}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Service ID</p>
                        <p class="mt-1 text-gray-900">${inquiry.service_id}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Project Type</p>
                        <p class="mt-1 text-gray-900">${inquiry.project_type}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Status</p>
                        <p class="mt-1 text-gray-900">${inquiry.status}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Created At</p>
                        <p class="mt-1 text-gray-900">${inquiry.created_at}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-700">Message</p>
                        <p class="mt-1 text-gray-900">${inquiry.message}</p>
                    </div>
                `;

                // Populate reply form
                document.getElementById('reply-inquiry-id').value = inquiry.inquiry_id;
                document.getElementById('reply-recipient-email').value = inquiry.email;
                document.getElementById('reply-subject').value = `Re: Inquiry about ${inquiry.project_type}`;
                document.getElementById('reply-message').value = `Dear ${inquiry.name},\n\nThank you for your inquiry about our ${inquiry.project_type} services. We are excited to discuss your project in detail. Please find the attached quote for your reference.\n\nBest regards,\nSEAVPEAV PECH\nPixel IT Solution`;

                // Populate action buttons
                const actionButtons = document.getElementById('action-buttons');
                actionButtons.innerHTML = `
                    <button onclick="openModal('reply-inquiry-modal')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Reply</button>
                    ${inquiry.status !== 'responded' && inquiry.status !== 'closed' ? `
                        <button onclick="updateStatus(${inquiry.inquiry_id}, 'responded')" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">Mark as Responded</button>
                    ` : ''}
                    ${inquiry.status !== 'closed' ? `
                        <button onclick="updateStatus(${inquiry.inquiry_id}, 'closed')" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Mark/Hide as Closed</button>
                    ` : ''}
                `;

                // Fetch reply history
                fetchReplyHistory();
            } catch (error) {
                console.error('Error fetching inquiry:', error);
                alert('Failed to load inquiry details.');
            }
        }

        // Fetch reply history
        async function fetchReplyHistory() {
            try {
                const response = await fetch(`${apiBaseUrl}inquiry_replies.php?inquiry_id=${inquiryId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const replies = await response.json();

                if (replies.error) {
                    console.error('Error fetching replies:', replies.error);
                    return;
                }

                const replyHistory = document.getElementById('reply-history');
                replyHistory.innerHTML = replies.length === 0 ? `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No replies yet.</td>
                    </tr>
                ` : replies.map(reply => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${reply.subject}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${reply.message.substring(0, 100)}${reply.message.length > 100 ? '...' : ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${reply.pdf_attachment ? `<a href="${reply.pdf_attachment}" target="_blank" class="text-blue-600 hover:underline">View PDF</a>` : 'None'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${reply.sent_at}</td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error fetching reply history:', error);
                showToast('Failed to load reply history.');
            }
        }

        // Update inquiry status
        async function updateStatus(inquiryId, status) {
            try {
                const response = await fetch(`${apiBaseUrl}inquiries.php?id=${inquiryId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });

                const result = await response.json();
                if (result.success) {
                    showToast(`Inquiry marked as ${status}.`);
                    fetchInquiry();
                } else {
                    alert(`Failed to update status: ${result.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('An error occurred while updating status.');
            }
        }

        // Handle reply form submission
        document.getElementById('reply-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Sending...';

            try {
                const formData = new FormData(form);

                // Save reply to database
                const saveResponse = await fetch(`${apiBaseUrl}save_inquiry_reply.php`, {
                    method: 'POST',
                    body: formData
                });
                const saveResult = await saveResponse.json();

                if (!saveResult.success) {
                    throw new Error(`Failed to save reply: ${saveResult.error || 'Unknown error'}`);
                }

                // Send email
                const emailResponse = await fetch(`${apiBaseUrl}inquiry_reply.php`, {
                    method: 'POST',
                    body: formData
                });
                const emailResult = await emailResponse.json();

                if (emailResult.success) {
                    showToast('Reply sent and saved successfully!');
                    closeModal('reply-inquiry-modal');
                    form.reset();
                    fetchInquiry();
                } else {
                    throw new Error(`Failed to send email: ${emailResult.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error processing reply:', error);
                alert(`An error occurred: ${error.message}`);
            } finally {
                button.disabled = false;
                button.textContent = 'Send Reply';
            }
        });

        // Load inquiry on page load
        document.addEventListener('DOMContentLoaded', fetchInquiry);
    </script>
</body>
</html>