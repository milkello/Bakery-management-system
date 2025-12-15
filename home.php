<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Website</title>
    <!-- link to font awesome for inserting icons -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <!-- lets link google font for inserting font families -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair:ital,opsz,wght@0,5..1200,300..900;1,5..1200,300..900&display=swap" rel="stylesheet">
    <!-- link to css file -->
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --coffee-dark: #3E2723;
            --coffee-medium: #6F4E37;
            --coffee-light: #C4A484;
            --coffee-accent: #D2B48C;
            --coffee-cream: #F5F5DC;
            --coffee-gold: #D4AF37;
            --text-dark: #2C2416;
            --text-light: #f3f3f3;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--coffee-cream);
            color: var(--text-dark);
            line-height: 1.7;
            overflow-x: hidden;
        }
        
      
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header & Navigation */
        header {
            background: #161515;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 10px 0;
            transition: all 0.4s ease;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo-icon {
            color: var(--coffee-gold);
            font-size: 28px;
        }
        
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 900;
            color: var(--coffee-medium);
            letter-spacing: 1px;
        }
        
        .logo-text span {
            color: var(--coffee-gold);
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 35px;
        }
        
        nav a {
            text-decoration: none;
            color: var(--coffee-medium);
            font-weight: 600;
            font-size: 16px;
            position: relative;
            padding: 5px 0;
            transition: all 0.3s ease;
        }
        
        nav a:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--coffee-gold);
            transition: width 0.3s ease;
        }
        
        nav a:hover:after {
            width: 100%;
        }
        
        .nav-buttons {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        
        .btn-primary {
            background: var(--coffee-medium);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--coffee-dark);
            transform: translateY(-3px);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--coffee-medium);
            border: 2px solid var(--coffee-medium);
        }
        
        .btn-secondary:hover {
            background: rgba(111, 78, 55, 0.05);
            transform: translateY(-3px);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0B0705 0%, #010000 50%);
            z-index: -1;
        }
        
        .hero-content {
            width: 750px;
            color: var(--text-light);
            animation: fadeInUp 1s ease-out;
        }
        
        .hero .container{
          display: flex;
        }
       
        .hero-subtitle {
            font-size: 20px;
            color: var(--coffee-accent);
            margin-top: 20px;
            margin-bottom: 60px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .hero-subtitle::before {
            content: '';
            width: 50px;
            height: 2px;
            background: var(--coffee-gold);
        }
        
        .hero-title {
            font-size: 58px;
            line-height: 1.1;
            margin-bottom: 25px;
            letter-spacing: 0.5px;
            color: var(--text-light);
        }
        
        .hero-title span {
            color: var(--coffee-gold);
            position: relative;
        }
        
        .hero-text {
            font-size: 16px;
            margin-bottom: 120px;
            max-width: 700px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }
             /* Products Section */
        .products {
            padding: 120px 0;
            position: relative; 
            background-color: #161515;
            border-top: 4px solid  var(--coffee-gold);
        }
        
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 70px;
        }
        
        .section-subtitle {
            font-size: 20px;
            color: var(--coffee-gold);
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 42px;
            color: var(--coffee-gold);
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--coffee-gold);
        }
        
        .section-desc {
            font-size: 18px;
            color: var(--text-light);
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .product-card {
            background: var(--coffee-dark);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            top: 0;
        }
        
        .product-card:hover {
            transform: translateY(-15px);
            border: 2px solid var(--coffee-gold);
        }
        
        .product-img {
            height: 280px;
            overflow: hidden;
        }
        
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-img img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 25px;
            position: relative;
        }
        
        .product-tag {
            position: absolute;
            top: -20px;
            right: 25px;
            background: var(--coffee-gold);
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .product-title {
            font-size: 24px;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        
        .product-desc {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--coffee-gold);
        }
        
        .rating {
            color: var(--coffee-gold);
            font-size: 16px;
        }

       h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

          /* Footer */
        footer {
            background: var(--coffee-dark);
            color: var(--text-light);
            padding: 70px 0 30px;
            position: relative;
            border-top: 4px solid  var(--coffee-gold);
            border
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .footer-column h3 {
            font-size: 22px;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--coffee-gold);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 15px;
        }
        
        .footer-links a {
            color: var(--coffee-accent);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--coffee-gold);
            padding-left: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: var(--coffee-cream);
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--coffee-gold);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--coffee-accent);
            font-size: 15px;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
      <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="#" class="logo">
                <!-- now insert coffee cup icons here -->
                 <i class="fa-solid fa-mug-hot logo-icon"></i>
                <div class="logo-text">Brew<span>Haven</span></div>
            </a>
            
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#">Locations</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
            
            <div class="nav-buttons">
                <a href="#" class="btn btn-secondary">Sign In</a>
                <a href="#" class="btn btn-primary">Order Online</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-subtitle">
                    <i class="fas fa-seedling"></i>
                    Ethically Sourced Beans
                </div>
                <h1 class="hero-title">Artisan Coffee <br><span>Crafted with Passion</span></h1>
                <p class="hero-text">Discover single-origin coffees roasted to perfection in small batches for exceptional flavor. Experience the journey from bean to cup with our master roasters.</p>
                <div class="hero-buttons">
                    <a href="#products" class="btn btn-primary">Explore Our Blends</a>
                    <a href="#" class="btn btn-secondary">Visit Our Cafe</a>
                </div>
            </div>
            <img src="1.jpg" alt="" width="370px">
        </div>
    </section>

    <!-- Products Section -->
    <section class="products" id="products">
        <div class="container">
            <div class="section-header">
                <div class="section-subtitle">Our Selection</div>
                <h2 class="section-title">Signature Coffee Blends</h2>
                <p class="section-desc">Each batch is carefully roasted to highlight the unique characteristics of the beans.</p>
            </div>
            
            <div class="product-grid">
                <div class="product-card">
                    <div class="product-img">
                        <img src="2.jpg" alt="Ethiopian Yirgacheffe">
                    </div>
                    <div class="product-info">
                        <div class="product-tag">BESTSELLER</div>
                        <h3 class="product-title">Ethiopian Yirgacheffe</h3>
                        <p class="product-desc">Floral notes with bright citrus acidity and a tea-like body. Light roast.</p>
                        <div class="product-footer">
                            <div class="product-price">$18.99</div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-img">
                        <img src="3.jpg" alt="Colombian Supremo">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Colombian Supremo</h3>
                        <p class="product-desc">Rich caramel sweetness with nutty undertones and balanced acidity. Medium roast.</p>
                        <div class="product-footer">
                            <div class="product-price">$16.99</div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-img">
                        <img src="4.jpg" alt="Dark Roast Espresso">
                    </div>
                    <div class="product-info">
                        <div class="product-tag">NEW</div>
                        <h3 class="product-title">Dark Roast Espresso</h3>
                        <p class="product-desc">Bold and intense with dark chocolate notes and a creamy mouthfeel. Dark roast.</p>
                        <div class="product-footer">
                            <div class="product-price">$17.99</div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

       <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>About BrewHaven</h3>
                    <p style="color: var(--coffee-accent); margin-bottom: 20px;">Artisan coffee roasters dedicated to quality, sustainability, and the perfect cup.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Coffee Blends</a></li>
                        <li><a href="#">Brewing Guides</a></li>
                        <li><a href="#">Sustainability</a></li>
                        <li><a href="#">Wholesale</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Coffee Lane, Portland, OR</li>
                        <li><i class="fas fa-phone"></i> (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> hello@brewhaven.com</li>
                        <li><i class="fas fa-clock"></i> Mon-Sat: 7am - 8pm</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                © 2023 BrewHaven Coffee Roasters. All rights reserved.
            </div>
        </div>
    </footer>


    <!-- link to JavaScript file -->
    <script src="script.js"></script>
    <script>
                // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Product hover animations
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-15px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>