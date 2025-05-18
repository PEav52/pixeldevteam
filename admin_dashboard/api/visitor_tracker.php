<?php
require_once 'config.php';

class VisitorTracker {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Generate unique session ID
    private function generateSessionId() {
        return bin2hex(random_bytes(16));
    }
    
    // Get client IP address
    private function getClientIp() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
    }
    
    // Get device and browser information
    private function getDeviceInfo() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Simple device detection
        $deviceType = 'Desktop';
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $deviceType = 'Tablet';
        }
        
        // Simple browser detection
        $browser = 'Unknown';
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'user_agent' => $userAgent
        ];
    }
    
    // Check if tracking should occur (bot filtering)
    public function shouldTrack() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/bot|crawl|spider|slurp|googlebot|bingbot|yahoo/i', $userAgent)) {
            return false;
        }
        return true;
    }
    
    // Track visitor
    public function trackVisitor($clientPageUrl = null) {
        try {
            $ip = $this->getClientIp();
            $sessionId = $_COOKIE['tracker_session_id'] ?? $this->generateSessionId();
            
            // Determine page URL: prioritize client-provided URL, then HTTP_REFERER, then fall back to REQUEST_URI
            $pageUrl = $clientPageUrl ?? ($_SERVER['HTTP_REFERER'] ?? ($_SERVER['REQUEST_URI'] ?? '/'));
            
            // Clean page URL to remove API endpoint if present
            if (strpos($pageUrl, '/admin_dashboard/api/visitor_tracker.php') !== false) {
                $pageUrl = '/';
            }
            
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            $deviceInfo = $this->getDeviceInfo();
            
            // Set session cookie if new
            if (!isset($_COOKIE['tracker_session_id'])) {
                setcookie('tracker_session_id', $sessionId, time() + 3600, '/');
            }
            
            // UTM parameters
            $utmCampaign = $_GET['utm_campaign'] ?? null;
            $utmSource = $_GET['utm_source'] ?? null;
            $utmMedium = $_GET['utm_medium'] ?? null;
            
            // Simple country code detection (default, replace with GeoIP if needed)
            $countryCode = 'US';
            
            // Traffic source detection
            $trafficSource = 'direct';
            if ($referrer) {
                if (strpos($referrer, 'google') !== false) {
                    $trafficSource = 'google';
                } elseif (strpos($referrer, 'facebook') !== false) {
                    $trafficSource = 'facebook';
                }
            }
            
            // Check if this is a returning visitor
            $isReturning = 0;
            if (isset($_COOKIE['tracker_session_id'])) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM sessions WHERE session_id = ?");
                $stmt->execute([$sessionId]);
                $isReturning = $stmt->fetchColumn() > 0 ? 1 : 0;
            }
            
            // Insert visitor record (visit_duration initially 0)
            $visitorStmt = $this->pdo->prepare("
                INSERT INTO webpagevisitors (
                    session_id, ip_address, page_url, visit_duration, visit_timestamp, user_agent,
                    referrer_url, traffic_source, country_code, device_type, browser,
                    is_active, utm_campaign, utm_source, utm_medium
                ) VALUES (?, ?, ?, 0, NOW(), ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)
            ");
            
            $visitorStmt->execute([
                $sessionId,
                $ip,
                $pageUrl,
                $deviceInfo['user_agent'],
                $referrer,
                $trafficSource,
                $countryCode,
                $deviceInfo['device_type'],
                $deviceInfo['browser'],
                $utmCampaign,
                $utmSource,
                $utmMedium
            ]);
            
            // Get the visitor_id for this record
            $visitorId = $this->pdo->lastInsertId();
            
            // Insert or update session record
            $sessionStmt = $this->pdo->prepare("
                INSERT INTO sessions (
                    session_id, visitor_id, start_timestamp, ip_address, user_agent, is_returning
                ) VALUES (?, ?, NOW(), ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    start_timestamp = NOW(),
                    ip_address = ?,
                    user_agent = ?,
                    is_returning = ?
            ");
            
            $sessionStmt->execute([
                $sessionId,
                $visitorId,
                $ip,
                $deviceInfo['user_agent'],
                $isReturning,
                $ip,
                $deviceInfo['user_agent'],
                $isReturning
            ]);
            
            return ['status' => 'success', 'session_id' => $sessionId, 'visitor_id' => $visitorId];
            
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    // Update session and visit duration
    public function updateSessionAndDuration($sessionId, $visitorId, $visitDuration) {
        try {
            // Update session duration
            $sessionStmt = $this->pdo->prepare("
                UPDATE sessions 
                SET end_timestamp = NOW(),
                    total_duration = TIMESTAMPDIFF(SECOND, start_timestamp, NOW())
                WHERE session_id = ?
            ");
            $sessionStmt->execute([$sessionId]);
            
            // Update visit duration in webpagevisitors
            $visitorStmt = $this->pdo->prepare("
                UPDATE webpagevisitors
                SET visit_duration = ?
                WHERE visitor_id = ? AND session_id = ?
            ");
            $visitorStmt->execute([$visitDuration, $visitorId, $sessionId]);
            
            return ['status' => 'success'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    // Get visitor analytics
    public function getAnalytics($dateRange = 'today') {
        try {
            $whereClause = '';
            switch ($dateRange) {
                case 'today':
                    $whereClause = "WHERE DATE(visit_timestamp) = CURDATE()";
                    break;
                case 'week':
                    $whereClause = "WHERE visit_timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $whereClause = "WHERE visit_timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                    break;
            }
            
            // Basic analytics
            $stats = $this->pdo->query("
                SELECT 
                    COUNT(DISTINCT visitor_id) as total_visitors,
                    COUNT(DISTINCT session_id) as total_sessions,
                    AVG(visit_duration) as avg_duration,
                    COUNT(DISTINCT ip_address) as unique_ips
                FROM webpagevisitors
                $whereClause
            ")->fetch(PDO::FETCH_ASSOC);
            
            // Top pages
            $topPages = $this->pdo->query("
                SELECT page_url, COUNT(*) as visits
                FROM webpagevisitors
                $whereClause
                GROUP BY page_url
                ORDER BY visits DESC
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Traffic sources
            $trafficSources = $this->pdo->query("
                SELECT traffic_source, COUNT(*) as count
                FROM webpagevisitors
                $whereClause
                GROUP BY traffic_source
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'stats' => $stats,
                'top_pages' => $topPages,
                'traffic_sources' => $trafficSources
            ];
            
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

// Instantiate tracker
$tracker = new VisitorTracker($pdo);

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Expect client to send page_url in JSON body
    $input = json_decode(file_get_contents('php://input'), true);
    $pageUrl = $input['page_url'] ?? null;
    $result = $tracker->trackVisitor($pageUrl);
    echo json_encode($result);
}

// Update session and visit duration
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['session_id']) && isset($_GET['visitor_id']) && isset($_GET['visit_duration'])) {
    $result = $tracker->updateSessionAndDuration(
        $_GET['session_id'],
        $_GET['visitor_id'],
        (int)$_GET['visit_duration']
    );
    echo json_encode($result);
}

// Get analytics
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['analytics'])) {
    $dateRange = $_GET['range'] ?? 'today';
    $result = $tracker->getAnalytics($dateRange);
    echo json_encode($result);
}
?>