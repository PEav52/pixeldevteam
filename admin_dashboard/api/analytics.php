<?php
require_once 'functions.php';

// Start output buffering
ob_start();

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log');

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$type = isset($_GET['type']) ? $_GET['type'] : null;

try {
    if ($method !== 'GET') {
        ob_end_clean();
        sendResponse(['error' => 'Method not allowed'], 405);
    }

    if (!$type) {
        ob_end_clean();
        sendResponse(['error' => 'Analytics type is required'], 400);
    }

    switch ($type) {
        case 'metrics':
            // Fetch key visitor metrics
            $daily = $pdo->query("SELECT COUNT(*) as count FROM webpagevisitors WHERE DATE(visit_timestamp) = CURDATE()")->fetch(PDO::FETCH_ASSOC)['count'];
            $weekly = $pdo->query("SELECT COUNT(*) as count FROM webpagevisitors WHERE visit_timestamp >= CURDATE() - INTERVAL 7 DAY")->fetch(PDO::FETCH_ASSOC)['count'];
            $monthly = $pdo->query("SELECT COUNT(*) as count FROM webpagevisitors WHERE visit_timestamp >= CURDATE() - INTERVAL 30 DAY")->fetch(PDO::FETCH_ASSOC)['count'];
            $avg_session = $pdo->query("SELECT AVG(visit_duration) as avg FROM webpagevisitors WHERE visit_timestamp >= CURDATE() - INTERVAL 30 DAY")->fetch(PDO::FETCH_ASSOC)['avg'];

            $data = [
                'daily_visitors' => (int)$daily,
                'weekly_visitors' => (int)$weekly,
                'monthly_visitors' => (int)$monthly,
                'avg_session_duration' => round($avg_session / 60, 2) // Convert seconds to minutes
            ];
            ob_end_clean();
            sendResponse($data);
            break;

        case 'trends':
            // Fetch visitor trends data
            $period = isset($_GET['period']) ? $_GET['period'] : 'daily';
            $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

            $query = "";
            $params = [];
            if ($period === 'daily') {
                $query = "SELECT DATE_FORMAT(visit_timestamp, '%Y-%m-%d') as label, COUNT(*) as count 
                          FROM webpagevisitors 
                          WHERE DATE(visit_timestamp) = ? 
                          GROUP BY label";
                $params = [$date];
            } elseif ($period === 'weekly') {
                $query = "SELECT DATE(visit_timestamp) as label, COUNT(*) as count 
                          FROM webpagevisitors 
                          WHERE visit_timestamp >= ? - INTERVAL 7 DAY 
                          GROUP BY label";
                $params = [$date];
            } else { // monthly
                $query = "SELECT DATE(visit_timestamp) as label, COUNT(*) as count 
                          FROM webpagevisitors 
                          WHERE visit_timestamp >= ? - INTERVAL 30 DAY 
                          GROUP BY label";
                $params = [$date];
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ob_end_clean();
            sendResponse($data);
            break;

        case 'traffic_sources':
            // Fetch traffic sources data
            $stmt = $pdo->query("SELECT traffic_source as label, COUNT(*) as count 
                                 FROM webpagevisitors 
                                 WHERE visit_timestamp >= CURDATE() - INTERVAL 30 DAY 
                                 GROUP BY traffic_source");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ob_end_clean();
            sendResponse($data);
            break;

        case 'demographics':
            // Fetch demographics data (by region)
            $by = isset($_GET['by']) ? $_GET['by'] : 'region';
            if ($by === 'region') {
                $stmt = $pdo->query("SELECT country_code as label, COUNT(*) as count 
                                     FROM webpagevisitors 
                                     WHERE visit_timestamp >= CURDATE() - INTERVAL 30 DAY 
                                     GROUP BY country_code");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else { // Placeholder for age group (requires external data)
                $data = [
                    ['label' => '18-24', 'count' => 5000],
                    ['label' => '25-34', 'count' => 8000],
                    ['label' => '35-44', 'count' => 6000],
                    ['label' => '45+', 'count' => 4000]
                ];
            }
            ob_end_clean();
            sendResponse($data);
            break;

        case 'realtime':
            // Fetch real-time visitors
            $active = $pdo->query("SELECT COUNT(*) as count 
                                   FROM webpagevisitors 
                                   WHERE is_active = TRUE")->fetch(PDO::FETCH_ASSOC)['count'];
            $sparkline = $pdo->query("SELECT MINUTE(visit_timestamp) as minute, COUNT(*) as count 
                                      FROM webpagevisitors 
                                      WHERE visit_timestamp >= NOW() - INTERVAL 10 MINUTE 
                                      GROUP BY minute 
                                      ORDER BY minute DESC 
                                      LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
            $data = [
                'active_visitors' => (int)$active,
                'sparkline' => array_reverse($sparkline) // Reverse for chronological order
            ];
            ob_end_clean();
            sendResponse($data);
            break;

        case 'top_pages':
            // Fetch top pages data
            $stmt = $pdo->query("SELECT page_url, 
                                        COUNT(*) as page_views, 
                                        COUNT(DISTINCT session_id) as unique_visitors, 
                                        AVG(visit_duration) as avg_time_spent 
                                 FROM webpagevisitors 
                                 WHERE visit_timestamp >= CURDATE() - INTERVAL 30 DAY 
                                 GROUP BY page_url 
                                 ORDER BY page_views DESC 
                                 LIMIT 5");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as &$row) {
                $row['avg_time_spent'] = round($row['avg_time_spent'] / 60, 2); // Convert to minutes
            }
            ob_end_clean();
            sendResponse($data);
            break;

        case 'comparison':
            // Fetch historical comparison data
            $period1 = isset($_GET['period1']) ? $_GET['period1'] : date('Y-m');
            $period2 = isset($_GET['period2']) ? $_GET['period2'] : date('Y-m', strtotime('-1 month'));
            
            $stmt1 = $pdo->prepare("SELECT COUNT(*) as count 
                                    FROM webpagevisitors 
                                    WHERE DATE_FORMAT(visit_timestamp, '%Y-%m') = ?");
            $stmt1->execute([$period1]);
            $count1 = $stmt1->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt2 = $pdo->prepare("SELECT COUNT(*) as count 
                                    FROM webpagevisitors 
                                    WHERE DATE_FORMAT(visit_timestamp, '%Y-%m') = ?");
            $stmt2->execute([$period2]);
            $count2 = $stmt2->fetch(PDO::FETCH_ASSOC)['count'];

            $percentage_change = $count2 ? round(($count1 - $count2) / $count2 * 100, 2) : 0;

            $data = [
                'period1' => ['label' => $period1, 'visitors' => (int)$count1],
                'period2' => ['label' => $period2, 'visitors' => (int)$count2],
                'percentage_change' => $percentage_change
            ];
            ob_end_clean();
            sendResponse($data);
            break;

        default:
            ob_end_clean();
            sendResponse(['error' => 'Invalid analytics type'], 400);
            break;
    }
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    ob_end_clean();
    sendResponse(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
}
?>