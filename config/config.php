<?php
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db   = $_ENV['DB_NAME'] ?? 'bakery_db';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
session_start();
// CSRF helper
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
function check_csrf($token) {
  return hash_equals($_SESSION['csrf'] ?? '', $token ?? '');
}

// Load translation helper
require_once __DIR__ . '/../app/helpers/i18n.php';

try {
    $conn = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Load user theme and language preferences into session if logged in
if (isset($_SESSION['user_id'])) {
    try {
        $prefStmt = $conn->prepare("SELECT theme, language FROM user_preferences WHERE user_id = ? LIMIT 1");
        $prefStmt->execute([$_SESSION['user_id']]);
        $prefs = $prefStmt->fetch(PDO::FETCH_ASSOC);

        if ($prefs) {
            $_SESSION['theme'] = $prefs['theme'] ?? 'dark';
            $_SESSION['language'] = $prefs['language'] ?? 'rw';
        } else {
            $_SESSION['theme'] = $_SESSION['theme'] ?? 'dark';
            $_SESSION['language'] = $_SESSION['language'] ?? 'rw';
        }
    } catch (Exception $e) {
        if (!isset($_SESSION['theme'])) {
            $_SESSION['theme'] = 'dark';
        }
        if (!isset($_SESSION['language'])) {
            $_SESSION['language'] = 'rw';
        }
    }
} else {
    $_SESSION['theme'] = $_SESSION['theme'] ?? 'dark';
    $_SESSION['language'] = $_SESSION['language'] ?? 'rw';
}

