<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

// Sécurité session et CSRF
session_start();
if (empty($_SESSION['csrf_token_connexion'])) {
    $_SESSION['csrf_token_connexion'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token_connexion'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion - MC-LEGENDE</title>

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css" />

  <style>
    :root {
      --primary-color: #4361ee;
      
      --accent-color: #4cc9f0;
      --light-color: #f8f9fa;
      --dark-color: #212529;
      --secondary-color: #2c3e50;
    }
    
    body {
      background: linear-gradient(135deg, #e0f2fe, #bae6fd);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      flex-direction: column;
    }
    
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-brand {
      font-weight: 600;
      color: var(--secondary-color);
    }
    
    .nav-link {
      color: var(--dark-color);
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      transition: all 0.3s;
    }
    
    .nav-link:hover, .nav-link.active {
      color: var(--primary-color);
      background-color: rgba(67, 97, 238, 0.1);
    }
    
    .login-container {
      flex: 1;
      display: flex;
      align-items: center;
      padding: 2rem 0;
    }
    
    .card-login {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: white;
    }
    
    .login-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 2rem;
      text-align: center;
    }
    
    .login-logo {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
    }
    
    .login-title {
      font-weight: 700;
      margin-bottom: 0;
    }
    
    .login-body {
      padding: 2rem;
    }
    
    .form-control {
      padding: 0.75rem 1.25rem;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }
    
    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
    }
    
    .input-group-text {
      background-color: transparent;
      border-right: none;
    }
    
    .input-group .form-control {
      border-left: none;
    }
    
    .input-group:focus-within .input-group-text {
      color: var(--primary-color);
    }
    
    .btn-login {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
    }
    
    .login-footer a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
    }
    
    .login-footer a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }
    
        
    .alert-danger {
      border-left: 4px solid #dc3545;
    }
    
    @media (max-width: 768px) {
      .login-container {
        padding: 1rem;
      }
      
      .card-login {
        border-radius: 10px;
      }
      
      .login-header {
        padding: 1.5rem;
      }
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

  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index1.php">
        <img src="images/back.jpeg" alt="Logo" width="40" class="me-2 rounded-circle shadow-sm">
        <span>MC-LEGENDE</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="./"><i class="fas fa-home me-1"></i>Accueil</a></li>
          <li class="nav-item"><a class="nav-link active" href="connexion"><i class="fas fa-sign-in-alt me-1"></i>Connexion</a></li>
          <li class="nav-item"><a class="nav-link" href="inscription"><i class="fas fa-user-plus me-1"></i>Inscription</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="login-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          
          <!-- Alertes -->
          <?php
          $messages = [
            'pi' => "Veuillez vous inscrire avant de continuer.",
            'mi' => "Mot de passe incorrect.",
            'nr' => "Veuillez remplir tous les champs.",
            'success' => "Inscription réussie avec succès connectez vous."
          ];
          foreach ($messages as $key => $msg):
            if (isset($_GET[$key]) && $_GET[$key] == 'ok'): ?>
              <div class="alert alert-danger alert-dismissible fade show mb-4">
                <div class="d-flex align-items-center">
                  <i class="fas fa-exclamation-circle me-2"></i>
                  <div><?= $msg ?></div>
                  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
              </div>
              <script>
                if (window.history.replaceState) {
                  const url = new URL(window.location);
                  url.searchParams.delete('<?= $key ?>');
                  window.history.replaceState({}, document.title, url.toString());
                }
              </script>
          <?php endif; endforeach; ?>

          <div class="card card-login">
            <div class="login-header">
              <img src="images/back.jpeg" alt="Logo MC-LEGENDE" class="login-logo">
              <h3 class="login-title">Connexion</h3>
            </div>
            
            <div class="login-body">
              <form action="login" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                <div class="mb-4">
                  <label for="identifiant" class="form-label fw-medium">Identifiant</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="identifiant" id="identifiant" class="form-control" 
                           placeholder="Email ou téléphone" required pattern="[0-9+@.a-zA-Z]{5,50}" maxlength="50" autocomplete="username">
                  </div>
                </div>
                <div class="mb-4">
                  <label for="mot_de_passe" class="form-label fw-medium">Mot de passe</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" 
                           class="form-control" placeholder="••••••••" required minlength="6" maxlength="50" autocomplete="current-password">
                  </div>
                </div>
                
                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-login btn-lg text-white">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                  </button>
                </div>
                
                <div class="login-footer mt-4 pt-3 border-top">
                  <div class="mb-2">
                    <a href="inscription.php" class="me-3">
                      <i class="fas fa-user-plus me-1"></i>Créer un compte
                    </a>
                    <a href="mot_de_passe_oublie.php">
                      <i class="fas fa-key me-1"></i>Mot de passe oublié?
                    </a>
                  </div>
                  <small class="text-muted">En continuant, vous acceptez nos conditions d'utilisation</small>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

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
  <script src="adminlte/plugins/jquery/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="adminlte/dist/js/adminlte.min.js"></script>
  
  <script>
    // Animation pour les champs de formulaire
    document.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.querySelector('.input-group-text').style.color = '#4361ee';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.querySelector('.input-group-text').style.color = '';
      });
    });
  </script>
</body>
</html>