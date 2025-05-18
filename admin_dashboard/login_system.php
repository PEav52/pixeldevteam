<?php
session_start();

// Database configuration and connection (unchanged)
define('DB_HOST', 'sql.freedb.tech');
define('DB_NAME', 'freedb_pixel_dev_team_989164061090');
define('DB_USER', 'freedb_pixel_db_owner');
define('DB_PASS', '*XE76bKXBGNT&SB');

// define('DB_HOST', 'mysql-2d380048-pixedevteam.k.aivencloud.com');
// define('DB_NAME', 'defaultdb');
// define('DB_USER', 'avnadmin');
// define('DB_PASS', 'AVNS_GZ5VYO2kj0ZPvtsirYM');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Generate and store CSRF token
if (empty($_SESSION['csrf_token']) && empty($_COOKIE['csrf_token'])) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
    
    // Set CSRF token as a cookie
    $cookie_expiry = time() + 3600; // 1 hour expiry
    setcookie('csrf_token', $csrf_token, [
        'expires' => $cookie_expiry,
        'path' => '/',
        'secure' => true, // Only send over HTTPS
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // Prevent CSRF
    ]);
} else {
    // Use existing token from session or cookie
    $csrf_token = $_SESSION['csrf_token'] ?? $_COOKIE['csrf_token'] ?? '';
    $_SESSION['csrf_token'] = $csrf_token; // Sync session with cookie
}

// Rate limiting function (unchanged)
function checkRateLimit() {
    $maxAttempts = 5;
    $timeFrame = 15 * 60; // 15 minutes
    
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    $currentTime = time();
    $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($timestamp) use ($currentTime, $timeFrame) {
        return ($currentTime - $timestamp) < $timeFrame;
    });
    
    if (count($_SESSION['login_attempts']) >= $maxAttempts) {
        return false;
    }
    
    $_SESSION['login_attempts'][] = $currentTime;
    return true;
}

// Log login attempts (unchanged)
function logLoginAttempt($pdo, $userId, $success) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO login_attempts (user_id, ip_address, success, attempt_time) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $ip, $success ? 1 : 0]);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Verify CSRF token
    $submitted_token = $_POST['csrf_token'] ?? '';
    $stored_token = $_COOKIE['csrf_token'] ?? $_SESSION['csrf_token'] ?? '';
    
    if (!hash_equals($stored_token, $submitted_token)) {
        $error = "Invalid CSRF token";
    } else {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        // Rate limiting
        if (!checkRateLimit()) {
            $error = "Too many login attempts. Please try again later.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Store user data in session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['last_activity'] = time();
                    
                    // Log successful login
                    logLoginAttempt($pdo, $user['user_id'], true);
                    
                    // Regenerate CSRF token after successful login
                    $new_csrf_token = bin2hex(random_bytes(32));
                    $_SESSION['csrf_token'] = $new_csrf_token;
                    setcookie('csrf_token', $new_csrf_token, [
                        'expires' => time() + 3600,
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                    
                    header("Location: index.php");
                    exit;
                } else {
                    // Log failed login
                    logLoginAttempt($pdo, null, false);
                    $error = "Invalid username or password";
                }
            } catch (PDOException $e) {
                $error = "An error occurred. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta: true
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login</h2>
        <?php if (isset($error)): ?>
            <div class="mb-4 text-red-500 text-center"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" required class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">Login</button>
        </form>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register here</a></p>
        </div>
    </div>
</body>
</html>