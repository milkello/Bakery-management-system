<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if already logged in
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
    <title>DoughLight Delights - Bakery Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        .floating-delayed {
            animation: floating 4s ease-in-out infinite;
            animation-delay: 1s;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        .gradient-text {
            background: linear-gradient(135deg, #84cc16 0%, #d946ef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="gradient-bg text-white overflow-x-hidden">
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="floating absolute top-10 left-10 w-72 h-72 bg-lime-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="floating-delayed absolute top-1/3 right-20 w-96 h-96 bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="floating absolute bottom-20 left-1/4 w-80 h-80 bg-lime-400 rounded-full mix-blend-multiply filter blur-3xl opacity-15"></div>
        <div class="floating-delayed absolute bottom-1/3 right-1/4 w-64 h-64 bg-fuchsia-400 rounded-full mix-blend-multiply filter blur-3xl opacity-15"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-10">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-lime-500 rounded-xl flex items-center justify-center">
                        <i data-feather="star" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-2xl font-bold gradient-text">DoughLight 🍪</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="#features" class="text-gray-300 hover:text-lime-400 transition-colors">Features</a>
                    <a href="#about" class="text-gray-300 hover:text-lime-400 transition-colors">About</a>
                    <a href="?page=login" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative z-10 py-20">
        <div class="container mx-auto px-6 text-center">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-6xl md:text-7xl font-bold mb-6">
                    Bake Your Business
                    <span class="gradient-text block">To Perfection</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 leading-relaxed">
                    Streamline your bakery operations with our all-in-one management system. 
                    From recipes to sales, we've got your dough covered.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="?page=login" 
                       class="bg-gradient-to-r from-lime-500 to-lime-600 hover:from-lime-600 hover:to-lime-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center space-x-3">
                        <i data-feather="log-in" class="w-6 h-6"></i>
                        <span>Start Baking Smarter</span>
                    </a>
                    <a href="#features" 
                       class="glass-card border border-lime-500/30 text-lime-400 hover:bg-lime-500/10 px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-3">
                        <i data-feather="play-circle" class="w-6 h-6"></i>
                        <span>See How It Works</span>
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
                    <div class="glass-card p-4 rounded-2xl text-center">
                        <div class="text-2xl font-bold text-lime-400">50+</div>
                        <div class="text-gray-400 text-sm">Bakeries Powered</div>
                    </div>
                    <div class="glass-card p-4 rounded-2xl text-center">
                        <div class="text-2xl font-bold text-fuchsia-400">99.9%</div>
                        <div class="text-gray-400 text-sm">Uptime</div>
                    </div>
                    <div class="glass-card p-4 rounded-2xl text-center">
                        <div class="text-2xl font-bold text-lime-400">24/7</div>
                        <div class="text-gray-400 text-sm">Support</div>
                    </div>
                    <div class="glass-card p-4 rounded-2xl text-center">
                        <div class="text-2xl font-bold text-fuchsia-400">5★</div>
                        <div class="text-gray-400 text-sm">Rated</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative z-10 py-20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Everything You Need to</h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">Manage your bakery efficiently with our comprehensive suite of tools</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-card p-6 rounded-2xl hover-lift group">
                    <div class="w-16 h-16 bg-lime-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="users" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Employee Management</h3>
                    <p class="text-gray-400">Track staff, schedules, and performance with intuitive employee management tools.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card p-6 rounded-2xl hover-lift group">
                    <div class="w-16 h-16 bg-fuchsia-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="package" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Inventory Control</h3>
                    <p class="text-gray-400">Manage raw materials, track stock levels, and automate reordering processes.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card p-6 rounded-2xl hover-lift group">
                    <div class="w-16 h-16 bg-lime-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="book-open" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Recipe Management</h3>
                    <p class="text-gray-400">Create, store, and scale recipes with precise ingredient measurements and costs.</p>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card p-6 rounded-2xl hover-lift group">
                    <div class="w-16 h-16 bg-fuchsia-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="trending-up" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Production Tracking</h3>
                    <p class="text-gray-400">Monitor daily production, track efficiency, and optimize your baking processes.</p>
                </div>

                <!-- Feature 5 -->
                <div class="glass-card p-6 rounded-2xl hover-lift group">
                    <div class="w-16 h-16 bg-lime-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="shopping-cart" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Sales Analytics</h3>
                    <p class="text-gray-400">Gain insights into sales patterns, customer preferences, and revenue trends.</p>
                </div>

                <!-- Feature 6 -->
                <div class="glass-card p-6 rounded-2xl hover-lift group">
                    <div class="w-16 h-16 bg-fuchsia-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="bell" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Smart Notifications</h3>
                    <p class="text-gray-400">Get alerted for low stock, production milestones, and important updates.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative z-10 py-20">
        <div class="container mx-auto px-6">
            <div class="glass-card rounded-3xl p-12 text-center max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Ready to Transform Your Bakery?</h2>
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    Join hundreds of successful bakeries that use DoughLight to streamline their operations and boost profitability.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="?page=login" 
                       class="bg-gradient-to-r from-lime-500 to-lime-600 hover:from-lime-600 hover:to-lime-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center justify-center space-x-3">
                        <i data-feather="zap" class="w-6 h-6"></i>
                        <span>Get Started Free</span>
                    </a>
                    <a href="#features" 
                       class="border border-lime-500 text-lime-400 hover:bg-lime-500/10 px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-3">
                        <i data-feather="book-open" class="w-6 h-6"></i>
                        <span>View Documentation</span>
                    </a>
                </div>
                <p class="text-gray-400 text-sm mt-6">No credit card required • Setup in minutes</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-12 border-t border-gray-800">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-lime-500 rounded-lg flex items-center justify-center">
                        <i data-feather="star" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="text-xl font-bold gradient-text">DoughLight 🍪</span>
                </div>
                <div class="flex space-x-6 text-gray-400">
                    <a href="#" class="hover:text-lime-400 transition-colors">Privacy</a>
                    <a href="#" class="hover:text-lime-400 transition-colors">Terms</a>
                    <a href="#" class="hover:text-lime-400 transition-colors">Support</a>
                </div>
                <div class="text-gray-500 text-sm mt-4 md:mt-0">
                    &copy; 2024 DoughLight Delights. Baked with ❤️ for bakers worldwide.
                </div>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll animation for features
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe feature cards
        document.querySelectorAll('.glass-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease ' + (index * 0.1) + 's';
            observer.observe(card);
        });

        // Initial feather icons render
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
    </script>
</body>
</html>