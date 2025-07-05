<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';

$roles_autorises = ['eleve', 'admin_simple', 'admin_principal'];
if (!isset($_SESSION['utilisateur']) || !in_array($_SESSION['utilisateur']['role'], $roles_autorises)) {
    header("Location: connexion.php");
    exit();
}

$id = $_SESSION['utilisateur']['id'];
$req = $pdo->prepare("SELECT nom, prenom, photo, role FROM utilisateurs WHERE id = ?");
$req->execute([$id]);
$utilisateur = $req->fetch();

$req = $pdo->prepare("SELECT id, titre FROM quiz WHERE statut = 'actif' LIMIT 1");
$req->execute();
$quiz_actif = $req->fetch();
$interro_active = $quiz_actif !== false;

if ($interro_active) {
    $derniere_notif = $pdo->prepare("SELECT 1 FROM notifications 
                                   WHERE utilisateur_id = ? AND quiz_id = ? AND type = 'quiz' 
                                   ORDER BY date_creation DESC LIMIT 1");
    $derniere_notif->execute([$id, $quiz_actif['id']]);

    if ($derniere_notif->fetch() === false) {
        addNotification(
            $pdo,
            $id,
            'quiz',
            'Nouvelle interrogation disponible',
            "L'interrogation '{$quiz_actif['titre']}' est prête à être commencée.",
            "quiz.php?id={$quiz_actif['id']}",
            $quiz_actif['id']
        );
    }
}

$nb_notifications = getUnreadNotificationsCount($pdo, $id);
$notifications = getRecentNotifications($pdo, $id);
$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'img/profil-default.jpg';

function getMessageParHeure() {
  $heure = (int)date('H');
  if ($heure >= 5 && $heure < 12) return "Bonjour";
  elseif ($heure >= 12 && $heure < 17) return "Bon après-midi";
  elseif ($heure >= 17 && $heure < 22) return "Bonsoir";
  else return "Bonne nuit";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tableau de bord - MC-LEGENDE</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    .notif-icon { position: relative; }
    .notif-badge { position: absolute; top: -5px; right: -5px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Barre de navigation principale -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link notif-icon" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <?php if ($nb_notifications > 0): ?>
            <span class="badge badge-danger navbar-badge notif-badge"><?= $nb_notifications ?></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <?php foreach ($notifications as $notif): ?>
            <a href="mark_notif_read.php?id=<?= $notif['id'] ?>&redirect=<?= urlencode($notif['lien']) ?>" class="dropdown-item">
              <i class="fas fa-<?= $notif['type'] === 'quiz' ? 'book' : 'info-circle' ?> mr-2"></i>
              <?= htmlspecialchars($notif['titre']) ?><br>
              <small><?= htmlspecialchars($notif['message']) ?></small>
            </a>
          <?php endforeach; ?>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
      </li>
    </ul>
  </nav>

  <!-- Barre latérale -->
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
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item">
            <a href="eleve.php" class="nav-link active">
              <i class="nav-icon fas fa-home"></i>
              <p>Tableau de bord</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="mes_interros.php" class="nav-link">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Mes Interros</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="resultats.php" class="nav-link">
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

  <!-- Contenu principal -->
  <div class="content-wrapper p-3">
    <div class="container-fluid">
      <div class="alert alert-info text-center animate__animated animate__fadeInDown">
        <h4 class="mb-0">👋 <?= getMessageParHeure() ?> <?= htmlspecialchars($utilisateur['prenom']) ?> ! Bienvenue dans votre espace personnel.</h4>
      </div>

      <div class="row">
        <div class="col-md-8">
          <div class="card animate__animated animate__zoomIn">
            <div class="card-header">
              <h5 class="card-title"><i class="fas fa-bolt text-warning mr-2"></i>État de l'interrogation</h5>
            </div>
            <div class="card-body">
              <?php if ($interro_active): ?>
                <div class="alert alert-success">
                  <h5><?= htmlspecialchars($quiz_actif['titre']) ?></h5>
                  <p>Une nouvelle interrogation est disponible pour vous !</p>
                  <a href="quiz.php?id=<?= $quiz_actif['id'] ?>" class="btn btn-success">
                    <i class="fas fa-play"></i> Commencer maintenant
                  </a>
                </div>
                <script>
                  const sound = new Audio('assets/audio/notify.mp3');
                  sound.play();
                </script>
              <?php else: ?>
                <div class="alert alert-secondary">
                  <i class="fas fa-info-circle"></i> Aucune interrogation active pour le moment.
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="card mt-3 animate__animated animate__fadeInUp">
            <div class="card-header">
              <h5><i class="fas fa-lightbulb text-info mr-2"></i>Conseils avant de commencer</h5>
            </div>
            <div class="card-body">
              <ul>
                <li>⏱️ Chaque question dure <strong>30 secondes</strong>.</li>
                <li>🔄 Vous passerez automatiquement à la question suivante.</li>
                <li>⚠️ <strong>Ne quittez pas la page</strong> pendant l'interrogation.</li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Pied de page -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Pour l'excellence pédagogique</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>
</div>

<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
