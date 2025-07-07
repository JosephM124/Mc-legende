<?php
// Sécurisation avancée de la session et headers HTTP
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
require_once 'fonctions.php'; // Pour CSRF et filtrage


require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['utilisateur']['id'];
// Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();



// Récupérer les dernières infos depuis la BD
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$admin_data = $stmt->fetch();

// Génération du token CSRF pour modification profil admin
if (!isset($_SESSION['csrf_token_profil_admin'])) {
    $_SESSION['csrf_token_profil_admin'] = generer_csrf_token('modif_profil_admin');
}
$csrf_token_profil_admin = $_SESSION['csrf_token_profil_admin'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil Administrateur</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <style>
body {
    background-color: #f5f8fc;
}
.card {
    border-radius: 10px;
}
label.form-label {
    font-weight: 500;
}
input.form-control {
    border-radius: 6px;
}
</style>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- NAVBAR -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Menu à gauche -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="admin_principal.php" class="nav-link">Accueil</a>
    </li>
  </ul>

  <!-- Menu à droite -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </li>
  </ul>
</nav>

<!-- SIDEBAR -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Marque/logo -->
  <a href="#" class="brand-link text-center d-flex align-items-center justify-content-center">
      <img src="images/back.jpeg" alt="Logo" width="36" class="me-2 rounded-circle shadow-sm">
      <span class="brand-text font-weight-light">MC-LEGENDE</span>
    </a>
  <!-- Infos utilisateur -->
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?= htmlspecialchars($utilisateur['photo']) ?>" class="img-circle elevation-2" alt="Admin">
      </div>
      <div class="info">
        <a href="#" class="d-block">Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?></a>
      </div>
    </div>

    <!-- Menu principal -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
          <a href="admin_principal.php" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Tableau de bord</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="admin_eleve.php" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>Élèves</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="interro_admin.php" class="nav-link">
            <i class="nav-icon fas fa-book"></i>
            <p>Interrogations</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="resultats_admin.php" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Résultats</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="question_admin.php" class="nav-link ">
            <i class="nav-icon fas fa-question"></i>
            <p>Questions</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="gestion_notifications.php" class="nav-link ">
            <i class="nav-icon fas fa-bell"></i>
            <p>Notifications</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="gestion_admins.php" class="nav-link">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Admins</p>
          </a>
        </li>
        <li class="nav-item">
            <a href="profil_admin.php" class="nav-link active">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
            </a>
          </li>

        <!-- 🔥 Lien historique ajouté ici -->
        <li class="nav-item">
          <a href="historique_activites.php" class="nav-link ">
            <i class="nav-icon fas fa-history"></i>
            <p>Historique des activités</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>


  <div class="content-wrapper">
    <section class="content-header">
      <h1>Mon Profil</h1>
    </section>

    <section class="content">
        
      <div class="container mt-5 mb-5">
        
<?php if (isset($_GET['success']) && $_GET['success'] === 'ok') : ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" id="messageAlert">
        ✅ Modifications enregistrées avec succès.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('success');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>
<?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'confirmation') : ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert" id="messageAlert">
        ❌ Erreur : Les mots de passe ne correspondent pas.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('erreur');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>
<?php endif; ?>


    <div class="card shadow p-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-user-edit"></i> Modifier mon profil
        </div>
        <form method="POST" action="updat_profil_admin.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token_profil_admin ?>">
            <div class="row mt-4">

                <!-- Avatar -->
                <div class="col-md-3 text-center">
                    <img id="avatarPreview" src="<?= htmlspecialchars($admin_data['photo'] ?? 'uploads/avatars/default.png') ?>" alt="Photo de profil" class="img-fluid rounded-circle mb-2" width="120">
                    <div class="mb-3">
                        <label class="form-label">Changer la photo</label>
                        <input type="file" name="photo" class="form-control" onchange="previewAvatar(this)">
                    </div>
                </div>

                <!-- Infos -->
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($admin_data['nom']) ?>" required pattern="^[A-Za-zÀ-ÿ\-\s]+$" title="Lettres uniquement">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Post-nom</label>
                            <input type="text" name="postnom" class="form-control" value="<?= htmlspecialchars($admin_data['postnom']) ?>" pattern="^[A-Za-zÀ-ÿ\-\s]*$" title="Lettres uniquement">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($admin_data['prenom']) ?>" required pattern="^[A-Za-zÀ-ÿ\-\s]+$" title="Lettres uniquement">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($admin_data['adresse']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin_data['email']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($admin_data['telephone']) ?>" pattern="^[0-9]+$" title="Chiffres uniquement" maxlength="15" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($admin_data['naissance']) ?>">
                        </div>
<div class="col-md-6">
    <label class="form-label">Mot de passe (laisser vide si inchangé)</label>
    <input type="password" name="mot_de_passe" class="form-control">
</div>
<div class="col-md-6">
    <label class="form-label">Confirmer le mot de passe</label>
    <input type="password" name="confirmation_mot_de_passe" class="form-control">
</div>

                        
                    </div>
                </div>
            </div>

            <!-- Bouton -->
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

    </section>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Admin Principal</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>

</div>

<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>


<script>
  // Fermer automatiquement l'alerte après 5 secondes
  setTimeout(function () {
    const alert = document.getElementById('messageAlert');
    if (alert) {
      // Animation de disparition (douce)
      alert.classList.remove('show');
      alert.classList.add('fade');
      setTimeout(() => alert.remove(), 500); // Retirer totalement après l'effet
    }
  }, 5000);
</script>

</body>
</html>
