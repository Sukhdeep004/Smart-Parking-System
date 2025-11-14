<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking - Home</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #20c997;
            --dark-color: #212529;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-hero-bg {
            position: relative;
            background: url('./assets/img/img7.jpg') center center/cover no-repeat;
            width: 100%;
            min-height: 720px;
            background-attachment: fixed; /* Optional: cool parallax */
            overflow: hidden;
        }
        .hero-section, .about-section {
            background: none !important;
        }
        .hero-section {
            padding: 120px 0 60px 0;
            color: #fff;
            text-align: center;
            position: relative;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.20);
        }
        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.20);
        }
        .btn-hero { padding: 15px 40px; font-size: 1.1rem; border-radius: 50px; transition: all 0.3s; position: relative;}
        .btn-hero:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2);}
        .main-hero-bg::after {
            /* Optional: soft overlay for readability (remove if you don't want it) */
            content: '';
            position: absolute; left:0; right:0; top:0; bottom:0;
            background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%,rgba(13,110,253,0.08) 100%);
            z-index: 1;
        }
        .features-section {padding: 80px 0; background: #f8f9fa;}
        .feature-card {padding: 40px 30px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s; height: 100%;}
        .feature-card:hover {transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.15);}
        .feature-icon {font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;}
        .about-section {
            padding: 60px 0 80px 0;
            color:#fff;
            position: relative;
            z-index: 2;
        }
        .about-section .container {
            position: relative;
            z-index: 3;
        }
        .about-section h2,
        .about-section p {
            text-shadow: 0 1px 5px rgba(0,0,0,0.16);
        }
        .stats-section {background: var(--dark-color); color: white; padding: 60px 0;}
        .stat-card {text-align: center;}
        .stat-number {font-size: 3rem; font-weight: 700; color: var(--secondary-color);}
        footer {background: var(--dark-color); color: white; padding: 40px 0;}
        .main-hero-bg::after {
    content: '';
    position: absolute;
    left:0; right:0; top:0; bottom:0;
    background: linear-gradient(180deg,rgba(0,0,0,0.15) 0%,rgba(13,110,253,0.08) 100%);
    z-index: 1;
}
.hero-section, .about-section {
    position: relative;
    z-index: 2;
    background: none !important;
}

    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-parking"></i> Smart Parking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO + ABOUT shared background -->
<div class="main-hero-bg">
    <!-- Hero Section -->
    <section class="hero-section" data-aos="fade-up">
        <div class="container">
            <h1>Smart Parking, Simplified.</h1>
            <p>Automate parking slot management and revenue tracking with real-time insights</p>
            <a href="login.php" class="btn btn-light btn-hero">
                <i class="fas fa-rocket"></i> Get Started
            </a>
        </div>
    </section>
    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12" data-aos="fade-up">
                    <h2 class="mb-4">What is Smart Parking System?</h2>
                    <p class="lead">A comprehensive web-based platform designed to revolutionize parking management. Our system helps administrators efficiently manage parking slots, monitor vehicle entries and exits, track payments, and generate insightful analytics.</p>
                    <p>Built with modern technologies and best practices, the Smart Parking System provides a seamless experience for parking administrators, ensuring optimal utilization of parking spaces and accurate revenue tracking.</p>
                </div>
            </div>
        </div>
    </section>
</div><!-- /main-hero-bg -->

<!-- Features Section -->
<section class="features-section" id="features">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-4 mb-3">Key Features</h2>
            <p class="lead text-muted">Everything you need to manage your parking facility</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h4>Real-time Slot Tracking</h4>
                    <p>Monitor parking slot availability in real-time with color-coded visual indicators</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-calculator"></i></div>
                    <h4>Automatic Fee Calculation</h4>
                    <p>Smart fee calculation based on parking duration and vehicle type</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                    <h4>Revenue Insights</h4>
                    <p>Comprehensive analytics with interactive charts and detailed reports</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h4>Secure Admin Access</h4>
                    <p>Protected authentication with encrypted passwords and session management</p>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-3">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-car"></i></div>
                    <h4>Vehicle Management</h4>
                    <p>Easy vehicle entry and exit processing with automatic slot assignment</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-file-export"></i></div>
                    <h4>Export Reports</h4>
                    <p>Download detailed reports in CSV format for record keeping</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="700">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h4>Mobile Responsive</h4>
                    <p>Fully responsive design that works seamlessly on all devices</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="800">
                <div class="feature-card text-center">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <h4>Live Activity Feed</h4>
                    <p>Real-time updates on all parking activities and transactions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section" data-aos="fade-up">
    <div class="container">
        <div class="row">
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">50+</div><p>Parking Slots</p></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">24/7</div><p>System Availability</p></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">100%</div><p>Secure</p></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">Fast</div><p>Processing Time</p></div></div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="mb-4">Ready to Transform Your Parking Management?</h2>
        <p class="lead mb-4">Join us today and experience the future of parking management</p>
        <a href="register.php" class="btn btn-primary btn-lg me-3"><i class="fas fa-user-plus"></i> Register Now</a>
        <a href="login.php" class="btn btn-outline-primary btn-lg"><i class="fas fa-sign-in-alt"></i> Login</a>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6"><h5><i class="fas fa-parking"></i> Smart Parking Solutions</h5>
                <p class="mt-3">Revolutionizing parking management with smart technology and innovative solutions.</p></div>
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled mt-3">
                    <li><a href="#about" class="text-white text-decoration-none">About</a></li>
                    <li><a href="#features" class="text-white text-decoration-none">Features</a></li>
                    <li><a href="login.php" class="text-white text-decoration-none">Login</a></li>
                    <li><a href="register.php" class="text-white text-decoration-none">Register</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact</h5>
                <ul class="list-unstyled mt-3">
                    <li><i class="fas fa-envelope"></i> support@parking.com</li>
                    <li><i class="fas fa-phone"></i> +91-9876543210</li>
                    <li><i class="fas fa-map-marker-alt"></i> Punjab, India</li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center"><p class="mb-0">&copy; 2025 Smart Parking Solutions. All rights reserved.</p></div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
</body>
</html>
