<?php
/**
 * Main Application Entry Point / Router
 * Handles all HTTP requests and routes them to appropriate endpoints
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Load configuration
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';

// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Compatibility for shared hosting where putenv might be disabled
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Check configuration
$db_name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? null);
if (!$db_name) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'System not configured']);
    die();
}

// Start session
@session_start();

// Initialize database
try {
    $db = new Database();
    $db->connect();
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'System error']);
    die();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];

// Handle both rewritten and direct requests
$path = isset($_GET['path']) ? $_GET['path'] : parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize the path: remove "api/" prefix and strip leading/trailing slashes
$path = trim($path, '/');
$path = preg_replace('/^api\//i', '', $path);
$path = strtok($path, '?');

// Route requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse the route
$parts = explode('/', trim($path, '/'));
$endpoint = isset($parts[0]) ? $parts[0] : 'home';
$action = isset($parts[1]) ? $parts[1] : null;

// Route to appropriate handler
switch ($endpoint) {
    case 'auth':
        require_once __DIR__ . '/api/auth.php';
        handleAuthRequest($method, $action);
        break;
    
    case 'flights':
        require_once __DIR__ . '/api/flights.php';
        handleFlightsRequest($method, $action);
        break;
    
    case 'bookings':
        require_once __DIR__ . '/api/bookings.php';
        handleBookingsRequest($method, $action);
        break;
    
    case 'admin':
        require_once __DIR__ . '/api/admin.php';
        handleAdminRequest($method, $action);
        break;
    
    case 'users':
        require_once __DIR__ . '/api/users.php';
        handleUsersRequest($method, $action);
        break;
    
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
        break;
}

/**
 * Send JSON response
 */
function sendResponse($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get JSON POST data
 */
function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user
 */
function getCurrentUser() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN;
}

/**
 * Generate unique booking reference
 */
function generateBookingReference() {
    return 'BK' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(stripslashes($data), ENT_QUOTES, 'UTF-8');
}
?>
