<?php 
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
// Génération d'un token CSRF à chaque affichage du formulaire d'inscription
$_SESSION['csrf_token_inscription'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token_inscription'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inscription - MC-LEGENDE</title>
  
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css" />

  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #2c3e50;
      --accent-color: #4cc9f0;
      --light-color: #f8f9fa;
      --dark-color: #212529;
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
    
    .register-container {
      flex: 1;
      display: flex;
      align-items: center;
      padding: 2rem 0;
    }
    
    .card-register {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: white;
    }
    
    .register-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 2rem;
      text-align: center;
    }
    
    .register-logo {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
    }
    
    .register-title {
      font-weight: 700;
      margin-bottom: 0;
    }
    
    .register-body {
      padding: 2rem;
    }
    
    .form-control {
      padding: 0.75rem 1.25rem;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      transition: all 0.3s;
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
    
    .btn-register {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }
    
    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .register-footer {
      text-align: center;
      margin-top: 1.5rem;
    }
    
    .register-footer a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
    }
    
    .register-footer a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }
    
    .page-footer {
      background-color: white;
      padding: 1.5rem 0;
      margin-top: auto;
      box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
    }
    
    .alert-danger {
      border-left: 4px solid #dc3545;
    }
    
    .form-section {
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid #eee;
    }
    
    .form-section-title {
      font-size: 1.1rem;
      color: var(--secondary-color);
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
    }
    
    .form-section-title i {
      margin-right: 0.5rem;
    }
    
    @media (max-width: 768px) {
      .register-container {
        padding: 1rem;
      }
      
      .card-register {
        border-radius: 10px;
      }
      
      .register-header {
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
          <li class="nav-item"><a class="nav-link" href="connexion"><i class="fas fa-sign-in-alt me-1"></i>Connexion</a></li>
          <li class="nav-item"><a class="nav-link active" href="inscription"><i class="fas fa-user-plus me-1"></i>Inscription</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="register-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          
          <!-- Alertes -->
          <?php
          $messages = [
            'mpe' => "Email inexistant ou les mots de passe ne correspondent pas.",
            'eu' => "Votre email ou numero de téléphone sont déjà utilisés.",
            'e' => "Erreur lors de l'inscription veuillez réessayer."
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

          <div class="card card-register">
            <div class="register-header">
              <img src="images/back.jpeg" alt="Logo MC-LEGENDE" class="register-logo">
              <h3 class="register-title">Créer un compte</h3>
            </div>
            
            <div class="register-body">
              <form action="register" method="POST" id="registerForm" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                
                <!-- Section Informations Personnelles -->
                <div class="form-section">
                  <h5 class="form-section-title">
                    <i class="fas fa-user-circle"></i> Informations personnelles
                  </h5>
                  
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="nom" class="form-label">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" required pattern="[A-Za-zÀ-ÿ\-\s]{2,30}" maxlength="30">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                      <label for="postnom" class="form-label">Postnom</label>
                      <input type="text" class="form-control" id="postnom" name="postnom" required pattern="[A-Za-zÀ-ÿ\-\s]{2,30}" maxlength="30">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                      <label for="prenom" class="form-label">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" required pattern="[A-Za-zÀ-ÿ\-\s]{2,30}" maxlength="30">
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="naissance" class="form-label">Date de naissance</label>
                      <input type="date" class="form-control" id="naissance" name="naissance" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Sexe</label>
                      <select name="sexe" class="form-select" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <!-- Section Coordonnées -->
                <div class="form-section">
                  <h5 class="form-section-title">
                    <i class="fas fa-address-book"></i> Coordonnées
                  </h5>
                  
                  <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                      <input type="email" class="form-control" id="email" name="email" required maxlength="60">
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" id="telephone" name="telephone" required pattern="[0-9]{8,15}" maxlength="15">
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                      <input type="text" class="form-control" id="adresse" name="adresse" required maxlength="100">
                    </div>
                  </div>
                </div>
                
                <!-- Section Sécurité -->
                <div class="form-section">
                  <h5 class="form-section-title">
                    <i class="fas fa-lock"></i> Sécurité du compte
                  </h5>
                  
                  <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-key"></i></span>
                      <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="6">
                    </div>
                    <small class="text-muted">Minimum 6 caractères</small>
                  </div>
                  
                  <div class="mb-3">
                    <label for="confirmer" class="form-label">Confirmer le mot de passe</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-key"></i></span>
                      <input type="password" class="form-control" id="confirmer" name="confirmer" required>
                    </div>
                    <div id="passwordMatch" class="text-danger small"></div>
                  </div>
                </div>
                
                <input type="hidden" name="role" value="eleve">
                
                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-register btn-lg text-white">
                    <i class="fas fa-user-plus me-2"></i>S'inscrire
                  </button>
                </div>
                
                <div class="register-footer mt-4 pt-3 border-top">
                  <p class="mb-0">
                    Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous ici</a>
                  </p>
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
    // Vérification des mots de passe en temps réel
    const password = document.getElementById('mot_de_passe');
    const confirm = document.getElementById('confirmer');
    const matchMsg = document.getElementById('passwordMatch');
    
    function checkPasswordMatch() {
      if (password.value !== confirm.value) {
        matchMsg.textContent = "Les mots de passe ne correspondent pas";
        confirm.classList.add('is-invalid');
      } else {
        matchMsg.textContent = "";
        confirm.classList.remove('is-invalid');
      }
    }
    
    password.addEventListener('input', checkPasswordMatch);
    confirm.addEventListener('input', checkPasswordMatch);
    
    // Validation du formulaire
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      if (password.value !== confirm.value) {
        e.preventDefault();
        matchMsg.textContent = "Les mots de passe doivent correspondre";
        confirm.focus();
      }
    });
    
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