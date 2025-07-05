<?php
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header("Location: connexion.php");
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

// Avant le HTML, dans PHP :
// Récupérer les notifications destinées à cet élève
$stmt = $pdo->prepare("
    SELECT * FROM notifications 
    WHERE 
        est_generale = 1 AND role_destinataire = 'admin_simple' AND 
            utilisateur_id = :id
    
    ORDER BY date_creation DESC LIMIT 5
");
$stmt->execute([
    
    'id' => $id
]);
$notifications = $stmt->fetchAll();

// Compter seulement les notifications non lues
$nbNotifNonLues = 0;
foreach ($notifications as $n) {
    if (empty($n['lue'])) {
        $nbNotifNonLues++;
    }
}

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
.notification-unread {
  background-color: #f0f8ff !important; /* Bleu très clair */
  border-left: 4px solid #007bff;
}
.notification-unread:hover {
  background-color: #e0f0ff !important;
}
</style>


</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    
      

    
    <ul class="navbar-nav ml-auto">
   <!-- Notifications -->
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="far fa-bell"></i>
    <?php if ($nbNotifNonLues > 0): ?>
      <span class="badge badge-danger navbar-badge"><?= $nbNotifNonLues ?></span>
    <?php endif; ?>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <span class="dropdown-item dropdown-header">
      <?= $nbNotifNonLues ?> nouvelle(s) notification(s)
    </span>
    <div class="dropdown-divider"></div>

    <?php if (empty($notifications)): ?>
      <span class="dropdown-item text-muted">Aucune notification</span>
    <?php else: ?>
      <?php foreach ($notifications as $notif): ?>
        <a href="mark_notif_read_as.php?id=<?= (int)$notif['id'] ?>" 
           class="dropdown-item <?= empty($notif['lue']) ? 'notification-unread bg-light' : '' ?>"
>
          <i class="fas fa-<?= ($notif['type'] ?? 'info') === 'systeme' ? 'cog' : 'info-circle' ?> mr-2"></i>
          <?= htmlspecialchars($notif['titre']) ?><br>
          <small class="text-muted"><?= htmlspecialchars($notif['date_creation']) ?> - <?= htmlspecialchars(substr($notif['message'], 0, 50)) ?>...</small>
        </a>
        <div class="dropdown-divider"></div>
      <?php endforeach; ?>
    <?php endif; ?>

    <a href="notifications_as.php" class="dropdown-item dropdown-footer text-primary">
      Voir toutes les notifications
    </a>
  </div>
</li>

  <!-- Déconnexion -->
  <li class="nav-item">
    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </li>
</ul>

  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center d-flex align-items-center justify-content-center">
      <img src="images/back.jpeg" alt="Logo" width="36" class="me-2 rounded-circle shadow-sm">
      <span class="brand-text font-weight-light">MC-LEGENDE</span>
    </a>    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?= htmlspecialchars($utilisateur['photo']) ?>" class="img-circle elevation-2" alt="Admin">
        </div>
        <div class="info">
          <a href="profil_admin_simple.php" class="d-block">Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="admin_simple.php" class="nav-link ">
              <i class="nav-icon fas fa-home"></i>
              <p>Accueil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="gestion_eleve_as.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Gérer les élèves</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="question_admin_simple.php" class="nav-link">
              <i class="nav-icon fas fa-question"></i>
              <p>Questions</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profil_admin_simple.php" class="nav-link active">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
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
        <form method="POST" action="updat_profil_admin_simple.php" enctype="multipart/form-data">
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
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($admin_data['nom']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Post-nom</label>
                            <input type="text" name="postnom" class="form-control" value="<?= htmlspecialchars($admin_data['postnom']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($admin_data['prenom']) ?>" required>
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
                            <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($admin_data['telephone']) ?>">
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
    <div class="float-right d-none d-sm-inline">
      Admin Simple
    </div>
    <strong>&copy; 2025 MC-LEGENDE</strong> - Tous droits réservés.
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
