<?php
session_start();
require_once 'databaseconnect.php'; // Connexion à la base
require_once 'fonctions.php'; // Si tu as des fonctions utiles

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

$id = $_SESSION['utilisateur']['id'];
$req = $pdo->prepare("SELECT nom, prenom, photo, role FROM utilisateurs WHERE id = ?");
$req->execute([$id]);
$utilisateur = $req->fetch();


// Récupérer la catégorie d'activité de l'élève
$stmt = $pdo->prepare("
    SELECT e.categorie_activite 
    FROM eleves e 
    INNER JOIN utilisateurs u ON e.utilisateur_id = u.id 
    WHERE u.id = ?
");
$stmt->execute([$id]);
$categorie = $stmt->fetchColumn();



$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'uploads/avatars/default.jpg';


// Récupérer les notifications destinées à cet élève
$stmt = $pdo->prepare("
    SELECT * FROM notifications 
    WHERE (
        (est_generale = 1 AND role_destinataire = 'eleve' AND (
            (categorie IS NULL) OR (categorie = :cat)
        ))
        AND (utilisateur_id = :id)
    )
    ORDER BY date_creation DESC LIMIT 20
");
$stmt->execute([
    'cat' => $categorie,
    'id' => $id
]);
$notification = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes notifications | MC-LEGENDE</title>
  <link href="assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/dist/css/adminlte.min.css" rel="stylesheet">
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
  }
</script>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
   
    <!-- Déconnexion -->
    <li class="nav-item">
      <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </li>
  </ul>
</nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center">
      <span class="brand-text font-weight-light">MC-LEGENDE</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?= $photo_profil ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Bienvenue, <?= htmlspecialchars($utilisateur['prenom']) ?></a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="eleve.php" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Accueil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="mes_interro.php" class="nav-link ">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Mes Interros</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="resultats.php" class="nav-link ">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Mes Résultats</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profil.php" class="nav-link">
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
      <h1 class="m-0">Centre de notifications</h1>
    </section>

    <section class="content">
      <div class="container-fluid">
        <?php if (empty($notification)): ?>
          <div class="alert alert-info">Aucune notification pour le moment.</div>
        <?php else: ?>
          <div class="card">
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                <?php foreach ($notification as $notif): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                      <strong><?= htmlspecialchars($notif['titre']) ?></strong><br>
                      <small class="text-muted"><?= date('d/m/Y à H:i', strtotime($notif['date_creation'])) ?></small><br>
                      <?= htmlspecialchars($notif['message']) ?>
                    </div>
                    <?php if (!empty($notif['url_action'])): ?>
                      <a href="<?= htmlspecialchars($notif['url_action']) ?>" class="btn btn-sm btn-primary ml-2">
                        Voir
                      </a>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Pour l'excellence pédagogique</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>


</div>

<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>

</body>
</html>
