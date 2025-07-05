<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MC-LEGENDE - Plateforme Éducative Innovante</title>
  <!-- Optimisation des ressources -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #4e73df;
      --primary-dark: #224abe;
      --secondary-color: #2c3e50;
      --accent-color: #f39c12;
      --light-color: #f8f9fa;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --dark-color: #5a5c69;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-color);
      line-height: 1.6;
      color: var(--dark-color);
      overflow-x: hidden;
    }
    
    /* Navbar améliorée */
    .navbar {
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
      background-color: rgba(255, 255, 255, 0.98) !important;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    .navbar.scrolled {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 10px 0;
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: var(--primary-color) !important;
      display: flex;
      align-items: center;
    }
    
    .logo-navbar {
      height: 40px;
      transition: transform 0.3s;
      margin-right: 10px;
    }
    
    .nav-link {
      font-weight: 500;
      padding: 8px 15px !important;
      border-radius: 20px;
      transition: all 0.3s;
      color: var(--secondary-color) !important;
    }
    
    .nav-link:hover, .nav-link.active {
      background-color: rgba(78, 115, 223, 0.1);
      color: var(--primary-color) !important;
    }
    
    /* Hero section premium */
    .hero-section {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 180px 0 140px;
      text-align: center;
      position: relative;
      overflow: hidden;
      clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
      margin-bottom: 50px;
    }
    
    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3QgZmlsbD0idXJsKCNwYXR0ZXJuKSIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIvPjwvc3ZnPg==');
      opacity: 0.3;
      pointer-events: none;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 20px;
      line-height: 1.2;
      text-shadow: 0 2px 10px rgba(0,0,0,0.1);
      animation: fadeInUp 1s ease;
    }
    
    .hero-subtitle {
      font-size: 1.5rem;
      font-weight: 300;
      margin-bottom: 10px;
      opacity: 0.9;
    }
    
    .hero-description {
      font-size: 1.2rem;
      max-width: 700px;
      margin: 0 auto 40px;
      opacity: 0.9;
    }
    
    /* Boutons premium */
    .btn-custom {
      border-radius: 50px;
      padding: 14px 32px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
      border-width: 2px;
      text-transform: uppercase;
      font-size: 0.9rem;
      position: relative;
      overflow: hidden;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
      background-color: var(--primary-dark);
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
    }
    
    .btn-outline-light:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }
    
    /* Badges */
    .badge-pill {
      border-radius: 50px;
      padding: 8px 15px;
      font-weight: 500;
      font-size: 0.8rem;
    }
    
    /* Section features premium */
    .features-section {
      padding: 100px 0;
      background-color: white;
      position: relative;
    }
    
    .section-header {
      margin-bottom: 70px;
      text-align: center;
    }
    
    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--secondary-color);
      position: relative;
      display: inline-block;
      margin-bottom: 15px;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: var(--primary-color);
      border-radius: 2px;
    }
    
    .section-subtitle {
      color: var(--dark-color);
      font-size: 1.1rem;
      max-width: 700px;
      margin: 0 auto;
      opacity: 0.8;
    }
    
    /* Feature cards */
    .feature-card {
      background-color: white;
      border-radius: 15px;
      padding: 40px 30px;
      text-align: center;
      transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
      border: none;
      height: 100%;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-bottom: 25px;
      display: inline-flex;
      width: 80px;
      height: 80px;
      align-items: center;
      justify-content: center;
      background-color: rgba(78, 115, 223, 0.1);
      border-radius: 50%;
      transition: all 0.3s;
    }
    
    .feature-card:hover .feature-icon {
      background-color: var(--primary-color);
      color: white;
      transform: scale(1.1);
    }
    
    .feature-title {
      font-weight: 600;
      margin-bottom: 15px;
      color: var(--secondary-color);
      font-size: 1.3rem;
    }
    
    .feature-text {
      color: var(--dark-color);
      opacity: 0.8;
    }
    
    /* Testimonials */
    .testimonials-section {
      background-color: #f8f9fa;
      padding: 100px 0;
      position: relative;
    }
    
    .testimonial-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
      height: 100%;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .testimonial-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .testimonial-header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .testimonial-img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
      border: 3px solid var(--primary-color);
    }
    
    .testimonial-author {
      font-weight: 600;
      margin-bottom: 5px;
      color: var(--secondary-color);
    }
    
    .testimonial-position {
      color: var(--primary-color);
      font-size: 0.9rem;
      font-weight: 500;
    }
    
    .testimonial-text {
      font-style: italic;
      color: var(--dark-color);
      position: relative;
      padding-left: 20px;
    }
    
    .testimonial-text::before {
      content: '"';
      position: absolute;
      left: 0;
      top: -10px;
      font-size: 3rem;
      color: rgba(78, 115, 223, 0.2);
      font-family: serif;
      line-height: 1;
    }
    
    /* CTA Section */
    .cta-section {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 80px 0;
      text-align: center;
      position: relative;
      clip-path: polygon(0 10%, 100% 0, 100% 100%, 0 100%);
      margin-top: 50px;
    }
    
    .cta-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    
    .cta-description {
      font-size: 1.2rem;
      max-width: 700px;
      margin: 0 auto 40px;
      opacity: 0.9;
    }
    
    /* Footer premium */
    .footer {
      background: var(--secondary-color);
      color: rgba(255, 255, 255, 0.8);
      padding: 80px 0 30px;
    }
    
    .footer-logo {
      width: 60px;
      margin-bottom: 20px;
    }
    
    .footer-title {
      color: white;
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 20px;
    }
    
    .footer-links {
      list-style: none;
      padding: 0;
    }
    
    .footer-links li {
      margin-bottom: 10px;
    }
    
    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .footer-links a:hover {
      color: white;
      padding-left: 5px;
    }
    
    .social-icons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
    }
    
    .social-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s;
      color: white;
    }
    
    .social-icon:hover {
      background-color: var(--primary-color);
      transform: translateY(-3px);
    }
    
    .copyright {
      margin-top: 50px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      font-size: 0.9rem;
      opacity: 0.6;
    }
    
    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
      100% { transform: translateY(0px); }
    }
    
    .floating {
      animation: float 6s ease-in-out infinite;
    }
    
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    
    /* Responsive */
    @media (max-width: 992px) {
      .hero-section {
        padding: 150px 0 100px;
      }
      
      .hero-title {
        font-size: 2.8rem;
      }
      
      .hero-subtitle {
        font-size: 1.3rem;
      }
    }
    
    @media (max-width: 768px) {
      .hero-section {
        padding: 120px 0 80px;
        clip-path: polygon(0 0, 100% 0, 100% 95%, 0 100%);
      }
      
      .hero-title {
        font-size: 2.2rem;
      }
      
      .hero-subtitle {
        font-size: 1.1rem;
      }
      
      .hero-description {
        font-size: 1rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .cta-section {
        clip-path: polygon(0 5%, 100% 0, 100% 100%, 0 100%);
        padding: 60px 0;
      }
      
      .cta-title {
        font-size: 2rem;
      }
    }
    
    @media (max-width: 576px) {
      .navbar-brand {
        font-size: 1.2rem;
      }
      
      .logo-navbar {
        height: 30px;
      }
      
      .btn-custom {
        padding: 10px 20px;
        font-size: 0.8rem;
      }
      
      .feature-card {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<!-- Navbar Premium -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index1.php">
      <img src="images/back.jpeg" alt="MC-LEGENDE" class="logo-navbar rounded-circle shadow-sm">
      <span>MC-LEGENDE</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index1.php">
            <i class="fas fa-home me-1"></i>Accueil
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="connexion.php">
            <i class="fas fa-sign-in-alt me-1"></i>Connexion
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="inscription.php">
            <i class="fas fa-user-plus me-1"></i>Inscription
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section Premium -->
<section class="hero-section">
  <div class="container position-relative">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <h1 class="hero-title animate__animated animate__fadeInDown">MC-LEGENDE</h1>
        <h2 class="hero-subtitle">L'éducation réinventée</h2>
        <p class="hero-description">
          La plateforme d'évaluation numérique qui garantit l'équité, la sécurité et l'engagement pour chaque élève.
          Conçue pour les établissements exigeants, les enseignants innovants et les investisseurs visionnaires.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
          <a href="connexion.php" class="btn btn-light btn-custom">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
          </a>
          <a href="inscription.php" class="btn btn-outline-light btn-custom">
            <i class="fas fa-user-plus me-2"></i>S'inscrire
          </a>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2">
          <span class="badge bg-success badge-pill">
            <i class="fas fa-lock me-1"></i> Sécurité avancée
          </span>
          <span class="badge bg-info badge-pill">
            <i class="fas fa-chart-line me-1"></i> Analytics temps réel
          </span>
          <span class="badge bg-warning text-dark badge-pill">
            <i class="fas fa-users me-1"></i> +10000 élèves
          </span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Key Metrics -->
<section class="container mb-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card delay-1">
        <div class="feature-icon">
          <i class="fas fa-user-graduate"></i>
        </div>
        <h3 class="feature-title">10,000+</h3>
        <p class="feature-text">Élèves inscrits</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card delay-2">
        <div class="feature-icon">
          <i class="fas fa-book-open"></i>
        </div>
        <h3 class="feature-title">350+</h3>
        <p class="feature-text">Quiz réalisés</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card delay-3">
        <div class="feature-icon">
          <i class="fas fa-smile"></i>
        </div>
        <h3 class="feature-title">98%</h3>
        <p class="feature-text">Taux de satisfaction</p>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="features-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Pourquoi choisir MC-LEGENDE ?</h2>
      <p class="section-subtitle">
        Une solution complète et sécurisée pour l'évaluation moderne, pensée pour la réussite de chaque élève 
        et la sérénité des établissements.
      </p>
    </div>
    
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-user-shield"></i>
          </div>
          <h3 class="feature-title">Sécurité & Anti-triche</h3>
          <p class="feature-text">
            Navigation verrouillée, détection de triche, gestion stricte du temps : vos évaluations sont protégées à chaque instant.
          </p>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-chart-bar"></i>
          </div>
          <h3 class="feature-title">Analytics en temps réel</h3>
          <p class="feature-text">
            Suivi instantané des résultats, tableaux de bord dynamiques, export des données : pilotez la réussite de vos élèves.
          </p>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-laptop-code"></i>
          </div>
          <h3 class="feature-title">Expérience intuitive</h3>
          <p class="feature-text">
            Interface moderne, navigation fluide, responsive : chaque utilisateur profite d'une expérience optimale sur tous supports.
          </p>
        </div>
      </div>
      
      
      
           
      
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Ils nous font confiance</h2>
      <p class="section-subtitle">
        Établissements, enseignants et élèves témoignent de l'impact de MC-LEGENDE sur leur quotidien.
      </p>
    </div>
    
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="testimonial-card">
          <div class="testimonial-header">
            <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="Prof. Sarah Dubois" class="testimonial-img">
            <div>
              <h4 class="testimonial-author">Prof. Sarah Dubois</h4>
              <p class="testimonial-position">Lycée Descartes, Paris</p>
            </div>
          </div>
          <p class="testimonial-text">
            MC-LEGENDE a révolutionné l'évaluation dans mon établissement. Sécurité, simplicité, résultats immédiats : un vrai plus !
          </p>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6">
        <div class="testimonial-card">
          <div class="testimonial-header">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Thomas Leroy" class="testimonial-img">
            <div>
              <h4 class="testimonial-author">Thomas Leroy</h4>
              <p class="testimonial-position">Étudiant en Licence</p>
            </div>
          </div>
          <p class="testimonial-text">
            J'apprécie la transparence et l'équité du système. Les feedbacks immédiats m'aident à progresser efficacement.
          </p>
        </div>
      </div>
      
      <div class="col-lg-4 col-md-6">
        <div class="testimonial-card">
          <div class="testimonial-header">
            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Émilie Martin" class="testimonial-img">
            <div>
              <h4 class="testimonial-author">Émilie Martin</h4>
              <p class="testimonial-position">Directrice, École Internationale</p>
            </div>
          </div>
          <p class="testimonial-text">
            La gestion des classes est simplifiée et les élèves adorent l'interface moderne. Un vrai gain de temps pour l'équipe pédagogique.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h2 class="cta-title">Investissez dans l'avenir de l'éducation</h2>
        <p class="cta-description">
          Rejoignez l'aventure MC-LEGENDE et participez à la transformation digitale de l'évaluation scolaire.
          Notre équipe est prête à vous présenter la plateforme et à répondre à toutes vos questions.
        </p>
        <a href="mailto:contact@mc-legende.com" class="btn btn-light btn-custom">
          <i class="fas fa-envelope me-2"></i>Contactez-nous
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Footer Premium -->
<footer class="footer">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <img src="images/back.jpeg" alt="MC-LEGENDE" class="footer-logo rounded-circle shadow">
        <h3 class="footer-title">MC-LEGENDE</h3>
        <p>L'innovation au service de l'éducation</p>
        
        <div class="social-icons">
          <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
        </div>
        
        <div class="copyright">
          © 2025 MC-LEGENDE. Tous droits réservés.
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
  
  // Counter animation
  document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.feature-card h3');
    const speed = 200;
    
    counters.forEach(counter => {
      const target = +counter.innerText.replace('+', '');
      const count = +counter.innerText.replace('+', '');
      const increment = target / speed;
      
      if (count < target) {
        counter.innerText = '0';
        const updateCount = () => {
          const current = +counter.innerText;
          const incrementValue = Math.ceil(target / speed);
          
          if (current < target) {
            counter.innerText = Math.ceil(current + incrementValue);
            setTimeout(updateCount, 1);
          } else {
            counter.innerText = target;
            if (counter.textContent.includes('2000')) {
              counter.textContent += '+';
            }
            if (counter.textContent.includes('350')) {
              counter.textContent += '+';
            }
          }
        };
        updateCount();
      }
    });
  });
</script>
</body>
</html>