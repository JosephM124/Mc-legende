<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';
require_once 'log_admin.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

$id = $_SESSION['utilisateur']['id'];

// Infos utilisateur
$req = $pdo->prepare("SELECT nom, prenom, photo, role FROM utilisateurs WHERE id = ?");
$req->execute([$id]);
$utilisateur = $req->fetch();

// Récupérer les résultats publiés
$sql = "SELECT r.id, q.titre, r.score, r.date_passage 
        FROM resultats r
        JOIN quiz q ON r.quiz_id = q.id 
        WHERE r.utilisateur_id = ? AND r.statut = 1 
        ORDER BY r.date_passage DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titres = [];
$scores = [];
$dates = [];

foreach ($resultats as $res) {
    $titres[] = $res['titre'];
    $scores[] = (int)$res['score'];
    $dates[] = date('d/m', strtotime($res['date_passage']));
}

// Récupérer la catégorie d'activité de l'élève
$stmt = $pdo->prepare("
    SELECT e.categorie_activite 
    FROM eleves e 
    INNER JOIN utilisateurs u ON e.utilisateur_id = u.id 
    WHERE u.id = ?
");
$stmt->execute([$id]);
$categorie = $stmt->fetchColumn();

// Récupérer les notifications destinées à cet élève
$stmt = $pdo->prepare("
    SELECT * FROM notifications 
    WHERE (
        (est_generale = 1 AND role_destinataire = 'eleve' AND (
            (categorie IS NULL) OR (categorie = :cat)
        ))
        AND (utilisateur_id = :id)
    )
    ORDER BY date_creation DESC LIMIT 5
");
$stmt->execute([
    'cat' => $categorie,
    'id' => $id
]);
$notifications = $stmt->fetchAll();

// Nombre de notifications non lues
$nb_notifications = 0;
foreach ($notifications as $notif) {
    if (!$notif['lue']) {
        $nb_notifications++;
    }
}

$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'img/profil-default.jpg';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Résultats - MC-LEGENDE</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  
</head>
  
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Barre de navigation -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <!-- Notification -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <?php if ($nb_notifications > 0): ?>
          <span class="badge badge-danger navbar-badge"><?= $nb_notifications ?></span>
        <?php endif; ?>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">
          <?= $nb_notifications ?> nouvelle(s) notification(s)
        </span>
        <div class="dropdown-divider"></div>

        <?php if (empty($notifications)): ?>
          <span class="dropdown-item text-muted">Aucune notification</span>
        <?php else: ?>
          <?php foreach ($notifications as $notif): ?>
            <a href="mark_notif_read.php?id=<?= $notif['id'] ?>&redirect=<?= urlencode($notif['lien'] ?? '#') ?>" 
               class="dropdown-item <?= empty($notif['lue']) ? 'bg-light' : '' ?>">
              <i class="fas fa-<?= ($notif['type'] ?? 'info') === 'quiz' ? 'book' : 'info-circle' ?> mr-2"></i>
              <strong><?= htmlspecialchars($notif['titre']) ?></strong><br>
              <small><?= htmlspecialchars(mb_strimwidth($notif['message'], 0, 50, '...')) ?></small>
            </a>
            <div class="dropdown-divider"></div>
          <?php endforeach; ?>
        <?php endif; ?>

        <a href="notifications.php" class="dropdown-item dropdown-footer text-primary">
          Voir toutes les notifications
        </a>
      </div>
    </li>

    <!-- Déconnexion -->
    <li class="nav-item">
      <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </li>
  </ul>
</nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center d-flex align-items-center justify-content-center">
      <img src="images/back.jpeg" alt="Logo" width="36" class="me-2 rounded-circle shadow-sm">
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
            <a href="home" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Accueil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="interro" class="nav-link ">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Mes Interros</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Mes Résultats</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profil" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- CONTENU -->
  <div class="content-wrapper p-3">

    <section class="content">
      <div class="container-fluid">
        <h3 class="mb-4">Mes Résultats</h3>

        <?php if (count($resultats) > 0): ?>
          <div class="card">
            <div class="card-body p-0">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Intitulé de l'interro</th>
                    <th>Score</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($resultats as $res): ?>
                  <tr>
                    <td><?= htmlspecialchars($res['titre']) ?></td>
                    <td><strong><?= $res['score'] ?>/10</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($res['date_passage'])) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            <i class="fas fa-hourglass-half"></i> Aucun résultat n'a encore été publié.
          </div>
        <?php endif; ?>

        <div class="card mt-4">
          <div class="card-header">
            <h5><i class="fas fa-chart-line"></i> Évolution de mes résultats</h5>
          </div>
          <div class="card-body">
            <canvas id="graphResultats"></canvas>
          </div>
        </div>

      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Pour l'excellence pédagogique
    </div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graphResultats').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
            label: 'Score',
            data: <?= json_encode($scores) ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false,
            tension: 0.3,
            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>
</body>
</html>
