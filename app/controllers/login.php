<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Rate limiting - check if too many attempts
    $attempts_key = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
    $attempts = $_SESSION[$attempts_key] ?? 0;
    $last_attempt = $_SESSION['last_attempt'] ?? 0;
    
    // Reset attempts after 15 minutes
    if (time() - $last_attempt > 900) {
        $attempts = 0;
    }
    
    if ($attempts >= 5) {
        $error = 'Too many login attempts. Please try again in 15 minutes.';
    } else {
        // Use prepared statement to find user
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Debug: Check what we're getting from database
        error_log("Login attempt - Username: $username, User found: " . ($user ? 'Yes' : 'No'));
        
        if ($user) {
            // Check if password column uses hashing or plain text
            $password_column = $user['password'];
            
            // Determine if password is hashed or plain text
            if (password_verify($password, $password_column)) {
                // Password is hashed and matches
                $password_valid = true;
            } elseif ($password === $password_column) {
                // Password is plain text and matches (for migration purposes)
                $password_valid = true;
                
                // Optional: Upgrade to hashed password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $upgrade_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upgrade_stmt->execute([$hashed_password, $user['id']]);
            } else {
                $password_valid = false;
            }

            if ($password_valid) {
                // Check if user is active
                if ($user['is_active'] ?? 1) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    
                    // Reset attempts on successful login
                    $_SESSION[$attempts_key] = 0;
                    
                    // Log login activity
                    try {
                        $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
                        $log_stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    } catch (Exception $e) {
                        // Log table might not exist, ignore error
                        error_log("Login log error: " . $e->getMessage());
                    }
                    
                    header('Location: ?page=dashboard');
                    exit;
                } else {
                    $error = 'Account is deactivated. Please contact administrator.';
                }
            } else {
                $error = 'Invalid username or password';
                $_SESSION[$attempts_key] = $attempts + 1;
                $_SESSION['last_attempt'] = time();
            }
        } else {
            $error = 'Invalid username or password';
            $_SESSION[$attempts_key] = $attempts + 1;
            $_SESSION['last_attempt'] = time();
        }
    }
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ?page=dashboard');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DoughLight Delights</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            min-height: 100vh;
        }
        .login-card {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(132, 204, 22, 0.3);
            border-color: #84cc16;
        }
        .floating-blob {
            animation: float 6s ease-in-out infinite;
        }
        .floating-blob-2 {
            animation: float 8s ease-in-out infinite;
            animation-delay: 1s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Enhanced Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="floating-blob absolute top-1/4 left-1/4 w-96 h-96 bg-lime-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="floating-blob-2 absolute bottom-1/4 right-1/4 w-96 h-96 bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-lime-400 rounded-full mix-blend-multiply filter blur-2xl opacity-10"></div>
    </div>

    <!-- Main Login Container -->
    <div class="w-full max-w-7xl mx-auto flex items-center justify-center min-h-screen">
        <div class="login-card rounded-3xl p-8 w-full max-w-md mx-auto relative z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-24 h-24 bg-gradient-to-br from-lime-500 to-lime-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <i data-feather="star" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-3">DoughLight 🍪</h1>
                <p class="text-gray-400 text-lg">Welcome back to your bakery dashboard</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if(!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg mb-6 flex items-center space-x-2 animate-pulse">
                <i data-feather="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                <span class="text-sm"><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-gray-400 text-sm font-medium">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="user" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <input 
                            name="username" 
                            placeholder="Enter your username" 
                            class="w-full bg-gray-700 text-white pl-10 pr-4 py-3 rounded-lg border border-gray-600 focus:outline-none input-glow transition-all duration-200"
                            required
                            autocomplete="username"
                            autofocus
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-gray-400 text-sm font-medium">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="lock" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <input 
                            name="password" 
                            type="password" 
                            placeholder="Enter your password" 
                            class="w-full bg-gray-700 text-white pl-10 pr-4 py-3 rounded-lg border border-gray-600 focus:outline-none input-glow transition-all duration-200"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center space-x-2 text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-lime-500 text-lime-500">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="text-lime-400 hover:text-lime-300 transition-colors text-sm">
                        Forgot password?
                    </a>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-lime-500 to-lime-600 hover:from-lime-600 hover:to-lime-700 text-white font-semibold py-4 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl flex items-center justify-center space-x-3 group"
                >
                    <i data-feather="log-in" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span class="text-base">Sign In to Dashboard</span>
                </button>
            </form>

            <!-- Security Features Indicator -->
            <!-- <div class="mt-8 pt-6 border-t border-gray-700">
                <div class="flex items-center justify-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                        <i data-feather="shield" class="w-4 h-4 text-lime-400"></i>
                        <span>SSL Secured</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-feather="clock" class="w-4 h-4 text-lime-400"></i>
                        <span>24/7 Monitoring</span>
                    </div>
                </div>
            </div> -->

            <!-- Demo Credentials (Remove in production) -->
            <!-- <div class="mt-6 p-4 bg-gray-900/50 rounded-lg border border-gray-700">
                <h4 class="text-sm font-semibold text-gray-400 mb-3 flex items-center space-x-2">
                    <i data-feather="info" class="w-4 h-4"></i>
                    <span>Demo Credentials</span>
                </h4>
                <div class="text-sm text-gray-500 space-y-2">
                    <div class="flex justify-between">
                        <span>Username:</span>
                        <span class="text-lime-400 font-mono">admin</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Password:</span>
                        <span class="text-lime-400 font-mono">admin123</span>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-600 text-center">
                    Change these in your database for production
                </div>
            </div> -->
        </div>
    </div>

    <!-- Footer -->
    <!-- <div class="fixed bottom-6 left-0 right-0 text-center text-gray-500 text-sm z-10">
        &copy; 2024 DoughLight Delights. All rights reserved.
    </div> -->

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Add form submission loading state
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function() {
            const originalText = submitButton.innerHTML;
            
            submitButton.innerHTML = `
                <i data-feather="loader" class="w-5 h-5 animate-spin"></i>
                <span>Signing in...</span>
            `;
            submitButton.disabled = true;
            
            // Re-render feather icons
            feather.replace();
        });

        // Auto-focus on username field
        const usernameInput = document.querySelector('input[name="username"]');
        usernameInput.focus();
        
        // Add input validation styling
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.add('border-lime-500');
                } else {
                    this.classList.remove('border-lime-500');
                }
            });
            
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.remove('border-lime-500');
                }
            });
        });

        // Initial feather icons render
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    // Add some interactive effects
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-lime-500/30', 'rounded-lg');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-lime-500/30', 'rounded-lg');
        });
    });
    </script>
</body>
</html>