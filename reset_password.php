<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
include 'databaseconnect.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    $_SESSION['erreur'] = "Lien invalide ou expiré.";
    header("Location: mot_de_passe_oublie.php");
    exit();
}

// Vérification approfondie du token
$stmt = $pdo->prepare("SELECT id, email FROM utilisateurs WHERE reset_token = ? AND token_expiration > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['erreur'] = "Ce lien de réinitialisation a expiré ou est invalide.";
    header("Location: mot_de_passe_oublie.php");
    exit();
}

// Génération d'un token CSRF à chaque affichage du formulaire
$_SESSION['reset_csrf'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['reset_csrf'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réinitialisation - MC-LEGENDE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">

  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #2c3e50;
      --success-color: #28a745;
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
    
    .reset-container {
      flex: 1;
      display: flex;
      align-items: center;
      padding: 2rem 0;
    }
    
    .card-reset {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: white;
    }
    
    .reset-header {
      background: linear-gradient(135deg, var(--success-color), #218838);
      color: white;
      padding: 2rem;
      text-align: center;
    }
    
    .reset-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
    }
    
    .reset-body {
      padding: 2rem;
    }
    
    .password-input {
      position: relative;
    }
    
    .password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--secondary-color);
    }
    
    .btn-reset {
      background: linear-gradient(135deg, var(--success-color), #218838);
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .btn-reset:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    
    .strength-meter {
      height: 5px;
      background: #e9ecef;
      border-radius: 3px;
      margin-top: 0.5rem;
      overflow: hidden;
    }
    
    .strength-bar {
      height: 100%;
      width: 0;
      transition: width 0.3s;
    }
    
    @media (max-width: 768px) {
      .reset-container {
        padding: 1rem;
      }
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
          <li class="nav-item"><a class="nav-link" href="index1.php"><i class="fas fa-home me-1"></i>Accueil</a></li>
          <li class="nav-item"><a class="nav-link" href="connexion.php"><i class="fas fa-sign-in-alt me-1"></i>Connexion</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="reset-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card card-reset">
            <div class="reset-header">
              <div class="reset-icon">
                <i class="fas fa-key"></i>
              </div>
              <h3>Réinitialisation du mot de passe</h3>
            </div>
            
            <div class="reset-body">
              <?php if (isset($_SESSION['erreur'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= htmlspecialchars($_SESSION['erreur']); ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                  </div>
                </div>
                <?php unset($_SESSION['erreur']); ?>
              <?php endif; ?>

              <form action="traitement_reset.php" method="POST" id="resetForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="mb-4">
                  <label for="email" class="form-label fw-medium">Email associé</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                </div>
                
                <div class="mb-4 password-input">
                  <label for="mot_de_passe" class="form-label fw-medium">Nouveau mot de passe</label>
                  <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" 
                         placeholder="Minimum 8 caractères" required minlength="8">
                  <i class="fas fa-eye password-toggle" id="togglePassword1"></i>
                  <div class="strength-meter mt-2">
                    <div class="strength-bar" id="strengthBar"></div>
                  </div>
                  <small class="text-muted">Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre</small>
                </div>
                
                <div class="mb-4 password-input">
                  <label for="mot_de_passe2" class="form-label fw-medium">Confirmer le mot de passe</label>
                  <input type="password" class="form-control" id="mot_de_passe2" name="mot_de_passe2" 
                         placeholder="Retapez votre mot de passe" required minlength="8">
                  <i class="fas fa-eye password-toggle" id="togglePassword2"></i>
                  <div id="passwordMatch" class="text-danger small"></div>
                </div>

                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-reset btn-lg text-white">
                    <i class="fas fa-sync-alt me-2"></i>Réinitialiser
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="text-center py-3 bg-white">
    <div class="container">
      <p class="mb-1">&copy; 2025 MC-LEGENDE. Tous droits réservés.</p>
      <small class="text-muted">Sécurité et confidentialité garanties</small>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="adminlte/plugins/jquery/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="adminlte/dist/js/adminlte.min.js"></script>
  
  <script>
    // Fonction pour vérifier la force du mot de passe
    function checkPasswordStrength(password) {
      let strength = 0;
      
      // Longueur minimale
      if (password.length >= 8) strength += 1;
      
      // Contient des lettres majuscules et minuscules
      if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
      
      // Contient des chiffres
      if (password.match(/([0-9])/)) strength += 1;
      
      // Contient des caractères spéciaux
      if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
      
      return strength;
    }

    // Afficher la force du mot de passe
    document.getElementById('mot_de_passe').addEventListener('input', function() {
      const strength = checkPasswordStrength(this.value);
      const strengthBar = document.getElementById('strengthBar');
      
      switch(strength) {
        case 0:
        case 1:
          strengthBar.style.width = '25%';
          strengthBar.style.backgroundColor = '#dc3545';
          break;
        case 2:
          strengthBar.style.width = '50%';
          strengthBar.style.backgroundColor = '#fd7e14';
          break;
        case 3:
          strengthBar.style.width = '75%';
          strengthBar.style.backgroundColor = '#ffc107';
          break;
        case 4:
          strengthBar.style.width = '100%';
          strengthBar.style.backgroundColor = '#28a745';
          break;
      }
    });

    // Basculer la visibilité du mot de passe
    document.querySelectorAll('.password-toggle').forEach(toggle => {
      toggle.addEventListener('click', function() {
        const inputId = this.id === 'togglePassword1' ? 'mot_de_passe' : 'mot_de_passe2';
        const input = document.getElementById(inputId);
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });
    });

    // Vérifier la correspondance des mots de passe
    document.getElementById('resetForm').addEventListener('submit', function(e) {
      const pwd1 = document.getElementById('mot_de_passe').value;
      const pwd2 = document.getElementById('mot_de_passe2').value;
      const matchMsg = document.getElementById('passwordMatch');
      
      if (pwd1 !== pwd2) {
        e.preventDefault();
        matchMsg.textContent = "Les mots de passe ne correspondent pas";
        document.getElementById('mot_de_passe2').focus();
      } else if (pwd1.length < 8) {
        e.preventDefault();
        matchMsg.textContent = "Le mot de passe doit faire au moins 8 caractères";
      }
    });

    // Vérification en temps réel
    document.getElementById('mot_de_passe2').addEventListener('input', function() {
      const pwd1 = document.getElementById('mot_de_passe').value;
      const matchMsg = document.getElementById('passwordMatch');
      
      if (this.value !== pwd1) {
        matchMsg.textContent = "Les mots de passe ne correspondent pas";
      } else {
        matchMsg.textContent = "";
      }
    });
  </script>
</body>
</html>