<!-- ```php -->
<?php
session_start();

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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
    <title>Settings - Pixel IT Solution</title>
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
                <a href="domain_checks.php" class="flex items-center space-x-2 hover:text-gray-300">
                    <span>🌐</span><span>Domain Checks</span>
                </a>
                <a href="setting.php" class="flex items-center text-pink-400 space-x-2">
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

            <div id="error-message" class="hidden text-red-500 mt-4 mx-6"></div>

            <!-- Main Section -->
            <div class="p-6 flex-1 overflow-auto">
                <!-- Company Info -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Company Information</h2>
                    
                    <form id="company-info-form" class="grid grid-cols-2 gap-4">
                        <input type="hidden" name="info_id" value="0">

                        <div>
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>Phone</label>
                            <input type="text" name="phone" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>Email</label>
                            <input type="email" name="email" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>Address</label>
                            <input type="text" name="address" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>Facebook URL</label>
                            <input type="url" name="facebook_url" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>Instagram URL</label>
                            <input type="url" name="instagram_url" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>Twitter URL</label>
                            <input type="url" name="twitter_url" class="w-full border rounded px-3 py-2">
                        </div>

                        <div>
                            <label>WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" class="w-full border rounded px-3 py-2">
                        </div>
                    </form>

                    <button id="save-company-info" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mt-4">Save</button>
                </div>

                <!-- About Content -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">About Content</h2>
                    <form id="about-form" class="grid grid-cols-1 gap-4" enctype="multipart/form-data">
                        <input type="hidden" name="about_id" id="about_id" value="">
                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="5" class="w-full border rounded px-3 py-2"></textarea>
                        </div>
                        <div>
                            <label for="image">Upload Image</label>
                            <input type="file" id="image" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <img id="preview-image" src="" alt="Preview" class="w-32 mt-2 rounded" style="display:none;">
                        </div>
                        <button type="button" id="save-about" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 mt-4">
                            Save
                        </button>
                    </form>
                </div>

                <!-- Pricing Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Pricing</h2>
                        <button id="add-pricing" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Pricing</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4">Item Name</th>
                                    <th class="py-3 px-4">Price</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Active</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pricing-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Pricing Modal -->
                <div id="add-pricing-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Add Pricing</h3>
                        <form id="add-pricing-form" class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="add-category">Category</label>
                                <select id="add-category" name="category" class="w-full border rounded px-3 py-2">
                                    <option value="">Select Category</option>
                                    <option value="development">Development</option>
                                    <option value="hosting">Hosting</option>
                                    <option value="domain">Domain</option>
                                </select>
                            </div>
                            <div>
                                <label for="add-item_name">Item Name</label>
                                <input type="text" id="add-item_name" name="item_name" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="add-price">Price</label>
                                <input type="number" step="0.01" id="add-price" name="price" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="add-description">Description</label>
                                <textarea id="add-description" name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label for="add-is_active">Active</label>
                                <select id="add-is_active" name="is_active" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="cancel-add-pricing" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                                <button type="submit" id="save-add-pricing" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Pricing Modal -->
                <div id="update-pricing-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Update Pricing</h3>
                        <form id="update-pricing-form" class="grid grid-cols-1 gap-4">
                            <input type="hidden" id="update-pricing_id" name="pricing_id">
                            <div>
                                <label for="update-category">Category</label>
                                <select id="update-category" name="category" class="w-full border rounded px-3 py-2">
                                    <option value="">Select Category</option>
                                    <option value="development">Development</option>
                                    <option value="hosting">Hosting</option>
                                    <option value="domain">Domain</option>
                                </select>
                            </div>
                            <div>
                                <label for="update-item_name">Item Name</label>
                                <input type="text" id="update-item_name" name="item_name" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="update-price">Price</label>
                                <input type="number" step="0.01" id="update-price" name="price" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="update-description">Description</label>
                                <textarea id="update-description" name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label for="update-is_active">Active</label>
                                <select id="update-is_active" name="is_active" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="cancel-update-pricing" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                                <button type="submit" id="save-update-pricing" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Process Steps Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Process Steps</h2>
                        <button id="add-step" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Step</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">Title</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Step Number</th>
                                    <th class="py-3 px-4">Visible</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="process-steps-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Step Modal -->
                <div id="add-step-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Add Process Step</h3>
                        <form id="add-step-form" class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="add-title">Title</label>
                                <input type="text" id="add-title" name="title" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="add-description">Description</label>
                                <textarea id="add-description" name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label for="add-step_number">Step Number</label>
                                <input type="number" min="1" id="add-step_number" name="step_number" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="add-is_visible">Visible</label>
                                <select id="add-is_visible" name="is_visible" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="cancel-add-step" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                                <button type="submit" id="save-add-step" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Step Modal -->
                <div id="update-step-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Update Process Step</h3>
                        <form id="update-step-form" class="grid grid-cols-1 gap-4">
                            <input type="hidden" id="update-step_id" name="step_id">
                            <div>
                                <label for="update-title">Title</label>
                                <input type="text" id="update-title" name="title" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="update-description">Description</label>
                                <textarea id="update-description" name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label for="update-step_number">Step Number</label>
                                <input type="number" min="1" id="update-step_number" name="step_number" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="update-is_visible">Visible</label>
                                <select id="update-is_visible" name="is_visible" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="cancel-update-step" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                                <button type="submit" id="save-update-step" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Testimonials Table -->
                <!-- <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Testimonials</h2>
                        <button id="add-testimonial" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Testimonial</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">Client Name</th>
                                    <th class="py-3 px-4">Client Title</th>
                                    <th class="py-3 px-4">Content</th>
                                    <th class="py-3 px-4">Rating</th>
                                    <th class="py-3 px-4">Image</th>
                                    <th class="py-3 px-4">Visible</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="testimonials-table-body"></tbody>
                        </table>
                    </div>
                </div> -->

                <!-- Why Choose Us Table -->
                <div class="bg-white rounded-lg p-6 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Why Choose Us</h2>
                        <button id="add-reason" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Reason</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">Title</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Icon Class</th>
                                    <th class="py-3 px-4">Visible</th>
                                    <th class="py-3 px-4">Display Order</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="why-choose-us-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Reason Modal -->
                <div id="add-reason-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Add Why Choose Us Reason</h3>
                        <form id="add-reason-form" class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="add-title">Title</label>
                                <input type="text" id="add-title" name="title" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="add-description">Description</label>
                                <textarea id="add-description" name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label for="add-icon_class">Icon Class</label>
                                <input type="text" id="add-icon_class" name="icon_class" class="w-full border rounded px-3 py-2" placeholder="e.g., fas fa-star">
                            </div>
                            <div>
                                <label for="add-is_visible">Visible</label>
                                <select id="add-is_visible" name="is_visible" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label for="add-display_order">Display Order</label>
                                <input type="number" min="1" id="add-display_order" name="display_order" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="cancel-add-reason" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                                <button type="submit" id="save-add-reason" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Reason Modal -->
                <div id="update-reason-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Update Why Choose Us Reason</h3>
                        <form id="update-reason-form" class="grid grid-cols-1 gap-4">
                            <input type="hidden" id="update-reason_id" name="reason_id">
                            <div>
                                <label for="update-title">Title</label>
                                <input type="text" id="update-title" name="title" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label for="update-description">Description</label>
                                <textarea id="update-description" name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                            </div>
                            <div>
                                <label for="update-icon_class">Icon Class</label>
                                <input type="text" id="update-icon_class" name="icon_class" class="w-full border rounded px-3 py-2" placeholder="e.g., fas fa-star">
                            </div>
                            <div>
                                <label for="update-is_visible">Visible</label>
                                <select id="update-is_visible" name="is_visible" class="w-full border rounded px-3 py-2">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label for="update-display_order">Display Order</label>
                                <input type="number" min="1" id="update-display_order" name="display_order" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" id="cancel-update-reason" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                                <button type="submit" id="save-update-reason" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const apiBaseUrl = 'api/';

        // function to truncate text for display
        function truncateText(text, maxLength) {
            return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
        }

        document.addEventListener("DOMContentLoaded", () => {
            fetchCompanyInfo();

            document.getElementById("save-company-info").addEventListener("click", async () => {
                await saveCompanyInfo();
            });
        });

        async function fetchCompanyInfo() {
            try {
                const response = await fetch(`${apiBaseUrl}company_info.php`);
                const data = await response.json();

                for (let key in data) {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) input.value = data[key];
                }
            } catch (error) {
                console.error("Error fetching company info:", error);
            }
        }

        async function saveCompanyInfo() {
            const form = document.querySelector("#company-info-form");
            const formData = new FormData(form);
            const jsonData = {};

            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            try {
                const response = await fetch(`${apiBaseUrl}company_info.php`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(jsonData)
                });

                const result = await response.json();

                if (result.success) {
                    alert("Company info updated successfully!");
                } else {
                    alert("Failed to update company info.");
                }
            } catch (error) {
                console.error("Error saving company info:", error);
                alert("An error occurred while saving.");
            }
        }

        // About Content (from previous implementation)
        document.addEventListener("DOMContentLoaded", () => {
            fetchAboutContent();
            fetchPricing();
            fetchStep();
            fetchWhyChooseUs();

            document.getElementById("save-about").addEventListener("click", async () => {
                await saveAboutContent();
            });

            document.getElementById("add-pricing").addEventListener("click", () => {
                document.getElementById("add-pricing-modal").classList.remove("hidden");
            });

            document.getElementById("cancel-add-pricing").addEventListener("click", () => {
                document.getElementById("add-pricing-modal").classList.add("hidden");
                document.getElementById("add-pricing-form").reset();
            });

            document.getElementById("cancel-update-pricing").addEventListener("click", () => {
                document.getElementById("update-pricing-modal").classList.add("hidden");
                document.getElementById("update-pricing-form").reset();
            });

            document.getElementById("add-pricing-form").addEventListener("submit", async (e) => {
                e.preventDefault();
                await savePricing();
            });

            document.getElementById("update-pricing-form").addEventListener("submit", async (e) => {
                e.preventDefault();
                await updatePricing();
            });

            document.getElementById("add-step").addEventListener("click", () => {
                document.getElementById("add-step-modal").classList.remove("hidden");
            });

            document.getElementById("cancel-add-step").addEventListener("click", () => {
                document.getElementById("add-step-modal").classList.add("hidden");
                document.getElementById("add-step-form").reset();
            });

            document.getElementById("cancel-update-step").addEventListener("click", () => {
                document.getElementById("update-step-modal").classList.add("hidden");
                document.getElementById("update-step-form").reset();
            });

            document.getElementById("add-step-form").addEventListener("submit", async (e) => {
                e.preventDefault();
                await saveStep();
            });

            document.getElementById("update-step-form").addEventListener("submit", async (e) => {
                e.preventDefault();
                await updateStep();
            });

            document.getElementById("add-reason").addEventListener("click", () => {
                document.getElementById("add-reason-modal").classList.remove("hidden");
            });

            document.getElementById("cancel-add-reason").addEventListener("click", () => {
                document.getElementById("add-reason-modal").classList.add("hidden");
                document.getElementById("add-reason-form").reset();
            });

            document.getElementById("cancel-update-reason").addEventListener("click", () => {
                document.getElementById("update-reason-modal").classList.add("hidden");
                document.getElementById("update-reason-form").reset();
            });

            document.getElementById("add-reason-form").addEventListener("submit", async (e) => {
                e.preventDefault();
                await saveReason();
            });

            document.getElementById("update-reason-form").addEventListener("submit", async (e) => {
                e.preventDefault();
                await updateReason();
            });
        });

        // About Content Functions
        async function fetchAboutContent() {
            try {
                const response = await fetch(`${apiBaseUrl}about_content.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                if (data.error) {
                    alert(`Failed to load content: ${data.error}`);
                    return;
                }

                const form = document.getElementById("about-form");
                for (let key in data) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && key !== 'image_url') {
                        input.value = data[key];
                    }
                }

                if (data.image_url) {
                    const img = document.getElementById("preview-image");
                    img.src = data.image_url.startsWith('http') ? data.image_url : `${data.image_url}`;
                    img.style.display = "block";
                }
            } catch (error) {
                console.error("Error loading about content:", error);
                alert("Failed to load about content.");
            }
        }

        async function saveAboutContent() {
            const form = document.getElementById("about-form");
            const description = form.querySelector("[name='description']").value.trim();
            const button = document.getElementById("save-about");

            if (!description) {
                alert("Description is required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                const response = await fetch(`${apiBaseUrl}about_content.php`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("About content updated successfully!");
                    await fetchAboutContent();
                } else {
                    alert(`Failed to update content: ${result.error || "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error saving about content:", error);
                alert("An error occurred while saving.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        // Pricing Functions
        async function fetchPricing() {
            try {
                const response = await fetch(`${apiBaseUrl}pricing.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                const tbody = document.getElementById("pricing-table-body");
                tbody.innerHTML = "";

                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="py-3 px-4">${item.pricing_id}</td>
                        <td class="py-3 px-4">${item.category.charAt(0).toUpperCase() + item.category.slice(1)}</td>
                        <td class="py-3 px-4">${item.item_name}</td>
                        <td class="py-3 px-4">$${parseFloat(item.price).toFixed(2)}</td>
                        <td class="py-3 px-4">${item.description || ''}</td>
                        <td class="py-3 px-4">${item.is_active == 1 ? 'Yes' : 'No'}</td>
                        <td class="py-3 px-4">
                            <button class="edit-pricing bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" data-id="${item.pricing_id}">Edit</button>
                            <button class="delete-pricing bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" data-id="${item.pricing_id}">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                document.querySelectorAll(".edit-pricing").forEach(button => {
                    button.addEventListener("click", async () => {
                        const pricingId = button.dataset.id;
                        await loadPricingForEdit(pricingId);
                    });
                });

                document.querySelectorAll(".delete-pricing").forEach(button => {
                    button.addEventListener("click", async () => {
                        if (confirm("Are you sure you want to delete this pricing?")) {
                            await deletePricing(button.dataset.id);
                        }
                    });
                });
            } catch (error) {
                console.error("Error loading pricing:", error);
                alert("Failed to load pricing data.");
            }
        }

        async function savePricing() {
            const form = document.getElementById("add-pricing-form");
            const button = document.getElementById("save-add-pricing");
            const category = form.querySelector("[name='category']").value;
            const itemName = form.querySelector("[name='item_name']").value.trim();
            const price = form.querySelector("[name='price']").value.trim();

            if (!category || !itemName || !price) {
                alert("Category, Item Name, and Price are required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                const response = await fetch(`${apiBaseUrl}pricing.php`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Pricing added successfully!");
                    form.reset();
                    document.getElementById("add-pricing-modal").classList.add("hidden");
                    await fetchPricing();
                } else {
                    alert(`Failed to add pricing: ${result.errors ? result.errors.join(", ") : "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error saving pricing:", error);
                alert("An error occurred while saving.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        async function loadPricingForEdit(pricingId) {
            try {
                const response = await fetch(`${apiBaseUrl}pricing.php?id=${pricingId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                if (data.error) {
                    alert(`Failed to load pricing: ${data.error}`);
                    return;
                }

                const form = document.getElementById("update-pricing-form");
                form.querySelector("[name='pricing_id']").value = data.pricing_id;
                form.querySelector("[name='category']").value = data.category;
                form.querySelector("[name='item_name']").value = data.item_name;
                form.querySelector("[name='price']").value = data.price;
                form.querySelector("[name='description']").value = data.description || '';
                form.querySelector("[name='is_active']").value = data.is_active;

                document.getElementById("update-pricing-modal").classList.remove("hidden");
            } catch (error) {
                console.error("Error loading pricing for edit:", error);
                alert("Failed to load pricing data.");
            }
        }

        async function updatePricing() {
            const form = document.getElementById("update-pricing-form");
            const button = document.getElementById("save-update-pricing");
            const pricingId = form.querySelector("[name='pricing_id']").value;
            const category = form.querySelector("[name='category']").value;
            const itemName = form.querySelector("[name='item_name']").value.trim();
            const price = form.querySelector("[name='price']").value.trim();

            if (!category || !itemName || !price) {
                alert("Category, Item Name, and Price are required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                const response = await fetch(`${apiBaseUrl}pricing.php?id=${pricingId}`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Pricing updated successfully!");
                    form.reset();
                    document.getElementById("update-pricing-modal").classList.add("hidden");
                    await fetchPricing();
                } else {
                    alert(`Failed to update pricing: ${result.errors ? result.errors.join(", ") : "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error updating pricing:", error);
                alert("An error occurred while updating.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        async function deletePricing(pricingId) {
            try {
                const response = await fetch(`${apiBaseUrl}pricing.php?id=${pricingId}`, {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_method=DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    alert("Pricing deleted successfully!");
                    await fetchPricing();
                } else {
                    alert(`Failed to delete pricing: ${result.error || "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error deleting pricing:", error);
                alert("An error occurred while deleting.");
            }
        }

        // Process Steps Functions
        async function fetchStep() {
            try {
                const response = await fetch(`${apiBaseUrl}step.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                const tbody = document.getElementById("process-steps-table-body");
                tbody.innerHTML = "";

                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="py-3 px-4">${item.step_id}</td>
                        <td class="py-3 px-4">${item.title.charAt(0).toUpperCase() + item.title.slice(1)}</td>
                        <td class="py-3 px-4">${item.description || ''}</td>
                        <td class="py-3 px-4">${item.step_number}</td>
                        <td class="py-3 px-4">${item.is_visible == 1 ? 'Yes' : 'No'}</td>
                        <td class="py-3 px-4">
                            <button class="edit-step bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" data-id="${item.step_id}">Edit</button>
                            <button class="delete-step bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" data-id="${item.step_id}">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                document.querySelectorAll(".edit-step").forEach(button => {
                    button.addEventListener("click", async () => {
                        const stepId = button.dataset.id;
                        await loadStepForEdit(stepId);
                    });
                });

                document.querySelectorAll(".delete-step").forEach(button => {
                    button.addEventListener("click", async () => {
                        if (confirm("Are you sure you want to delete this process step?")) {
                            await deleteStep(button.dataset.id);
                        }
                    });
                });
            } catch (error) {
                console.error("Error loading process steps:", error);
                alert("Failed to load process steps data.");
            }
        }

        async function saveStep() {
            const form = document.getElementById("add-step-form");
            const button = document.getElementById("save-add-step");
            const title = form.querySelector("[name='title']").value.trim();
            const stepNumber = form.querySelector("[name='step_number']").value.trim();

            if (!title || !stepNumber) {
                alert("Title and Step Number are required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                const response = await fetch(`${apiBaseUrl}step.php`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Process step added successfully!");
                    form.reset();
                    document.getElementById("add-step-modal").classList.add("hidden");
                    await fetchStep();
                } else {
                    alert(`Failed to add process step: ${result.errors ? result.errors.join(", ") : "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error saving process step:", error);
                alert("An error occurred while saving.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        async function loadStepForEdit(stepId) {
            try {
                const response = await fetch(`${apiBaseUrl}step.php?id=${stepId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                if (data.error) {
                    alert(`Failed to load process step: ${data.error}`);
                    return;
                }

                const form = document.getElementById("update-step-form");
                form.querySelector("[name='step_id']").value = data.step_id;
                form.querySelector("[name='title']").value = data.title;
                form.querySelector("[name='description']").value = data.description || '';
                form.querySelector("[name='step_number']").value = data.step_number;
                form.querySelector("[name='is_visible']").value = data.is_visible;

                document.getElementById("update-step-modal").classList.remove("hidden");
            } catch (error) {
                console.error("Error loading process step for edit:", error);
                alert("Failed to load process step data.");
            }
        }

        async function updateStep() {
            const form = document.getElementById("update-step-form");
            const button = document.getElementById("save-update-step");
            const stepId = form.querySelector("[name='step_id']").value;
            const title = form.querySelector("[name='title']").value.trim();
            const stepNumber = form.querySelector("[name='step_number']").value.trim();

            if (!title || !stepNumber) {
                alert("Title and Step Number are required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                const response = await fetch(`${apiBaseUrl}step.php?id=${stepId}`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Process step updated successfully!");
                    form.reset();
                    document.getElementById("update-step-modal").classList.add("hidden");
                    await fetchStep();
                } else {
                    alert(`Failed to update process step: ${result.errors ? result.errors.join(", ") : "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error updating process step:", error);
                alert("An error occurred while updating.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        async function deleteStep(stepId) {
            try {
                const response = await fetch(`${apiBaseUrl}step.php?id=${stepId}`, {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_method=DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    alert("Process step deleted successfully!");
                    await fetchStep();
                } else {
                    alert(`Failed to delete process step: ${result.error || "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error deleting process step:", error);
                alert("An error occurred while deleting.");
            }
        }

        // Why Choose Us Functions
        async function fetchWhyChooseUs() {
            try {
                const response = await fetch(`${apiBaseUrl}why_choose_us.php`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                const tbody = document.getElementById("why-choose-us-table-body");
                tbody.innerHTML = "";

                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="py-3 px-4">${item.reason_id}</td>
                        <td class="py-3 px-4">${item.title.charAt(0).toUpperCase() + item.title.slice(1)}</td>
                        <td class="py-3 px-4">${item.description || ''}</td>
                        <td class="py-3 px-4">${item.icon_class}</td>
                        <td class="py-3 px-4">${item.is_visible == 1 ? 'Yes' : 'No'}</td>
                        <td class="py-3 px-4">${item.display_order}</td>
                        <td class="py-3 px-4">
                            <button class="edit-reason bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" data-id="${item.reason_id}">Edit</button>
                            <button class="delete-reason bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" data-id="${item.reason_id}">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                document.querySelectorAll(".edit-reason").forEach(button => {
                    button.addEventListener("click", async () => {
                        const reasonId = button.dataset.id;
                        await loadReasonForEdit(reasonId);
                    });
                });

                document.querySelectorAll(".delete-reason").forEach(button => {
                    button.addEventListener("click", async () => {
                        if (confirm("Are you sure you want to delete this reason?")) {
                            await deleteReason(button.dataset.id);
                        }
                    });
                });
            } catch (error) {
                console.error("Error loading why choose us reasons:", error);
                alert("Failed to load why choose us data.");
            }
        }

        async function saveReason() {
            const form = document.getElementById("add-reason-form");
            const button = document.getElementById("save-add-reason");
            const title = form.querySelector("[name='title']").value.trim();
            const iconClass = form.querySelector("[name='icon_class']").value.trim();
            const displayOrder = form.querySelector("[name='display_order']").value.trim();

            if (!title || !iconClass || !displayOrder) {
                alert("Title, Icon Class, and Display Order are required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                const response = await fetch(`${apiBaseUrl}why_choose_us.php`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Reason added successfully!");
                    form.reset();
                    document.getElementById("add-reason-modal").classList.add("hidden");
                    await fetchWhyChooseUs();
                } else {
                    alert(`Failed to add reason: ${result.errors ? result.errors.join(", ") : "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error saving reason:", error);
                alert("An error occurred while saving.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        async function loadReasonForEdit(reasonId) {
            try {
                const response = await fetch(`${apiBaseUrl}why_choose_us.php?id=${reasonId}`);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();

                if (data.error) {
                    alert(`Failed to load reason: ${data.error}`);
                    return;
                }

                const form = document.getElementById("update-reason-form");
                form.querySelector("[name='reason_id']").value = data.reason_id;
                form.querySelector("[name='title']").value = data.title;
                form.querySelector("[name='description']").value = data.description || '';
                form.querySelector("[name='icon_class']").value = data.icon_class;
                form.querySelector("[name='is_visible']").value = data.is_visible;
                form.querySelector("[name='display_order']").value = data.display_order;

                document.getElementById("update-reason-modal").classList.remove("hidden");
            } catch (error) {
                console.error("Error loading reason for edit:", error);
                alert("Failed to load reason data.");
            }
        }

        async function updateReason() {
            const form = document.getElementById("update-reason-form");
            const button = document.getElementById("save-update-reason");
            const reasonId = form.querySelector("[name='reason_id']").value;
            const title = form.querySelector("[name='title']").value.trim();
            const iconClass = form.querySelector("[name='icon_class']").value.trim();
            const displayOrder = form.querySelector("[name='display_order']").value.trim();

            if (!title || !iconClass || !displayOrder) {
                alert("Title, Icon Class, and Display Order are required.");
                return;
            }

            button.disabled = true;
            button.textContent = "Saving...";

            try {
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                const response = await fetch(`${apiBaseUrl}why_choose_us.php?id=${reasonId}`, {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert("Reason updated successfully!");
                    form.reset();
                    document.getElementById("update-reason-modal").classList.add("hidden");
                    await fetchWhyChooseUs();
                } else {
                    alert(`Failed to update reason: ${result.errors ? result.errors.join(", ") : "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error updating reason:", error);
                alert("An error occurred while updating.");
            } finally {
                button.disabled = false;
                button.textContent = "Save";
            }
        }

        async function deleteReason(reasonId) {
            try {
                const response = await fetch(`${apiBaseUrl}why_choose_us.php?id=${reasonId}`, {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_method=DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    alert("Reason deleted successfully!");
                    await fetchWhyChooseUs();
                } else {
                    alert(`Failed to delete reason: ${result.error || "Unknown error"}`);
                }
            } catch (error) {
                console.error("Error deleting reason:", error);
                alert("An error occurred while deleting.");
            }
        }
    </script>
</body>
</html>