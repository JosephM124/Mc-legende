<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
// Sécurité : vérification que l'utilisateur a passé la première étape
if (!isset($_SESSION['inscription1'])) {
  header('Location: inscription.php');
  exit();
}
if (empty($_SESSION['csrf_token_form2'])) {
  $_SESSION['csrf_token_form2'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token_form2'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Informations scolaires - MC-LEGENDE</title>
  
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">

  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
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
    }
    
    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
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
    
    .page-footer {
      background-color: white;
      padding: 1.5rem 0;
      margin-top: auto;
      box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
    }
    
    .alert-danger {
      border-left: 4px solid #dc3545;
    }
    
    .optional-badge {
      background-color: #6c757d;
      color: white;
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      margin-left: 0.5rem;
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
          <li class="nav-item"><a class="nav-link" href="index1.php">Accueil</a></li>
          <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
          <li class="nav-item"><a class="nav-link active" href="inscription.php">Inscription</a></li>
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
            'ii' => "Vous n'aviez pas achevé votre inscription veuillez compléter.",
            'nr' => "Veuillez remplir tous les champs nécessaires.",
            'e' => "Erreur lors de la finalisation de votre inscription."
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
              <h3 class="register-title">Informations Scolaires</h3>
              <p class="mb-0">Étape 2/2 - Complétez votre profil</p>
            </div>
            
            <div class="register-body">
              <form action="traitement_form2.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="pays" class="form-label">Pays</label>
                    <select class="form-select" id="pays" name="pays" required>
                      <option value="">-- Sélectionnez votre pays --</option>
                      <option value="RDC">République Démocratique du Congo</option>
                      <option value="France">France</option>
                      <option value="Belgique">Belgique</option>
                      <option value="Autre">Autre</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="province" class="form-label">Province/Ville</label>
                    <input type="text" class="form-control" id="province" name="province" required>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="etablissement" class="form-label">Nom de l'établissement</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-school"></i></span>
                    <input type="text" class="form-control" id="etablissement" name="etablissement" required>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="section" class="form-label">
                    Option/Section 
                    <span class="optional-badge">Optionnel si classe inférieure</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                    <input type="text" class="form-control" id="section" name="section">
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="adresse_ecole" class="form-label">Adresse de l'école</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" class="form-control" id="adresse_ecole" name="adresse_ecole" required>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="categorie" class="form-label">Catégorie d'activité</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tags"></i></span>
                    <select class="form-select" id="categorie" name="categorie" required>
                      <option value="">-- Sélectionner une catégorie --</option>
                      <option value="musique">Musique</option>
                      <option value="danse">Danse</option>
                      <option value="culture générale">Culture générale</option>
                      <option value="art">Art</option>
                      <option value="autre">Autre</option>
                    </select>
                  </div>
                </div>
                
                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-register btn-lg text-white">
                    <i class="fas fa-check-circle me-2"></i>Finaliser l'inscription
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
    // Gestion dynamique des provinces en fonction du pays
    document.getElementById('pays').addEventListener('change', function() {
      const provinceField = document.getElementById('province');
      
      if (this.value === 'RDC') {
        // Créer un select pour les provinces de RDC
        provinceField.outerHTML = `
          <select class="form-select" id="province" name="province" required>
            <option value="">-- Sélectionnez votre province --</option>
            <option value="Kinshasa">Kinshasa</option>
            <option value="Kongo-Central">Kongo-Central</option>
            <option value="Kwango">Kwango</option>
            <option value="Kwilu">Kwilu</option>
            <option value="Mai-Ndombe">Mai-Ndombe</option>
            <option value="Kasai">Kasai</option>
            <option value="Kasai-Central">Kasai-Central</option>
            <option value="Kasai-Oriental">Kasai-Oriental</option>
            <option value="Lomami">Lomami</option>
            <option value="Sankuru">Sankuru</option>
            <option value="Maniema">Maniema</option>
            <option value="Sud-Kivu">Sud-Kivu</option>
            <option value="Nord-Kivu">Nord-Kivu</option>
            <option value="Ituri">Ituri</option>
            <option value="Haut-Uele">Haut-Uele</option>
            <option value="Tshopo">Tshopo</option>
            <option value="Bas-Uele">Bas-Uele</option>
            <option value="Nord-Ubangi">Nord-Ubangi</option>
            <option value="Mongala">Mongala</option>
            <option value="Sud-Ubangi">Sud-Ubangi</option>
            <option value="Equateur">Equateur</option>
            <option value="Tshuapa">Tshuapa</option>
            <option value="Tanganyika">Tanganyika</option>
            <option value="Haut-Lomami">Haut-Lomami</option>
            <option value="Lualaba">Lualaba</option>
            <option value="Haut-Katanga">Haut-Katanga</option>
          </select>
        `;
      } else {
        // Remettre un input text pour les autres pays
        provinceField.outerHTML = `
          <input type="text" class="form-control" id="province" name="province" required>
        `;
      }
    });
  </script>
</body>
</html>