<?php 
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
if (empty($_SESSION['csrf_token_oubli'])) {
  $_SESSION['csrf_token_oubli'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token_oubli'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Récupération de compte - MC-LEGENDE</title>
  
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
    
    .recovery-container {
      flex: 1;
      display: flex;
      align-items: center;
      padding: 2rem 0;
    }
    
    .card-recovery {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: white;
    }
    
    .recovery-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 2rem;
      text-align: center;
    }
    
    .recovery-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: white;
    }
    
    .recovery-body {
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
    
    .btn-recovery {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }
    
    .btn-recovery:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .recovery-footer {
      text-align: center;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid #eee;
    }
    
    .recovery-footer a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
    }
    
    .recovery-footer a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }
    
    .page-footer {
      background-color: white;
      padding: 1.5rem 0;
      margin-top: auto;
      box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 768px) {
      .recovery-container {
        padding: 1rem;
      }
      
      .card-recovery {
        border-radius: 10px;
      }
      
      .recovery-header {
        padding: 1.5rem;
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
          <li class="nav-item"><a class="nav-link active" href="inscription.php"><i class="fas fa-user-plus me-1"></i>Inscription</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="recovery-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card card-recovery">
            <div class="recovery-header">
              <div class="recovery-icon">
                <i class="fas fa-key"></i>
              </div>
              <h3 class="recovery-title">Réinitialisation du mot de passe</h3>
            </div>
            
            <div class="recovery-body">
              <!-- Messages -->
              <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?= htmlspecialchars($_SESSION['success']); ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                  </div>
                </div>
                <?php unset($_SESSION['success']); ?>
              <?php endif; ?>

              <?php if (isset($_SESSION['erreur'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= htmlspecialchars($_SESSION['erreur']); ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                  </div>
                </div>
                <?php unset($_SESSION['erreur']); ?>
              <?php endif; ?>

              <form action="traitement_oubli.php" method="POST" id="recoveryForm" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                <div class="mb-4">
                  <label for="email" class="form-label fw-medium">Adresse e-mail</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="votre@email.com" required>
                  </div>
                  <small class="text-muted">Un lien de réinitialisation vous sera envoyé</small>
                </div>

                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-recovery btn-lg text-white">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien
                  </button>
                </div>
              </form>

              <div class="recovery-footer">
                <p class="mb-0">
                  Vous souvenez-vous de votre mot de passe ? 
                  <a href="connexion.php">Connectez-vous ici</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="page-footer">
    <div class="container text-center">
      <p class="mb-1">&copy; 2025 MC-LEGENDE. Tous droits réservés.</p>
      <small class="text-muted">Plateforme sécurisée - Vos données sont protégées</small>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="adminlte/plugins/jquery/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="adminlte/dist/js/adminlte.min.js"></script>
  
  <script>
    // Animation pour le champ email
    document.getElementById('email').addEventListener('focus', function() {
      this.parentElement.querySelector('.input-group-text').style.color = '#4361ee';
    });
    
    document.getElementById('email').addEventListener('blur', function() {
      this.parentElement.querySelector('.input-group-text').style.color = '';
    });

    // Validation basique du formulaire
    document.getElementById('recoveryForm').addEventListener('submit', function(e) {
      const email = document.getElementById('email').value;
      if (!email.includes('@') || !email.includes('.')) {
        e.preventDefault();
        alert('Veuillez entrer une adresse email valide');
      }
    });
  </script>
</body>
</html>