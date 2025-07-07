<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location:connexion.php");
    exit();
}
$id = $_SESSION['utilisateur']['id'];
// Récupération des stats (exemples)
$total_eleves = $pdo->query("SELECT COUNT(*) FROM eleves")->fetchColumn();
$total_interros = $pdo->query("SELECT COUNT(*) FROM quiz WHERE statut = 'actif'")->fetchColumn();
$total_notifications = $pdo->query("SELECT COUNT(*) FROM notifications WHERE lue = 0 ")->fetchColumn();
$total_admins = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin_simple'")->fetchColumn();

$sections = $pdo->query("SELECT section, COUNT(*) as total FROM eleves GROUP BY section")->fetchAll();
$interro_stats = $pdo->query("SELECT DATE(date_lancement) as jour, COUNT(*) as total FROM quiz GROUP BY jour ORDER BY jour DESC LIMIT 7")->fetchAll();

// Récupérer la répartition des élèves par catégorie_activité
$cat_activite = $pdo->query("SELECT categorie_activite, COUNT(*) as total FROM eleves GROUP BY categorie_activite")->fetchAll();

// Récupérer la distribution des scores (arrondis à l'entier)
$score_stats = $pdo->query("SELECT FLOOR(score) as score, COUNT(*) as total FROM resultats WHERE score IS NOT NULL GROUP BY FLOOR(score) ORDER BY score")->fetchAll();

// Gestion de la photo
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoPath = 'uploads/avatars/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }
    

    // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();
$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'uploads/avatars/default.jpeg';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord Admin Principal - MC-LEGENDE</title>
  <link href="assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/dist/css/adminlte.min.css" rel="stylesheet">
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
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
          <a href="admin_principal.php" class="nav-link active">
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
          <a href="question_admin.php" class="nav-link">
            <i class="nav-icon fas fa-question"></i>
            <p>Questions</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="gestion_notifications.php" class="nav-link">
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
            <a href="profil_admin.php" class="nav-link ">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
            </a>
          </li>


        <!-- 🔥 Lien historique ajouté ici -->
        <li class="nav-item">
          <a href="historique_activites.php" class="nav-link">
            <i class="nav-icon fas fa-history"></i>
            <p>Historique des activités</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>


  <div class="content-wrapper p-3">
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $total_eleves ?></h3>
                <p>Elèves inscrits</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-graduate"></i>
              </div>
              <a href="admin_eleve.php" class="small-box-footer">Voir plus <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $total_interros ?></h3>
                <p>Interros actives</p>
              </div>
              <div class="icon">
                <i class="fas fa-book"></i>
              </div>
              <a href="interro_admin.php" class="small-box-footer">Voir plus <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $total_notifications ?></h3>
                <p>Notifications</p>
              </div>
              <div class="icon">
                <i class="fas fa-bell"></i>
              </div>
              <a href="gestion_notifications.php" class="small-box-footer">Voir plus <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $total_admins ?></h3>
                <p>Admins simples</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-shield"></i>
              </div>
              <a href="gestion_admins.php" class="small-box-footer">Voir plus <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <!-- Graphiques -->
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><h5>Répartition des élèves par section</h5></div>
              <div class="card-body">
                <canvas id="sectionChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><h5>Interros créées par jour</h5></div>
              <div class="card-body">
                <canvas id="interroChart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><h5>Élèves par catégorie d'activité</h5></div>
              <div class="card-body">
                <canvas id="catActiviteChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><h5>Distribution des scores</h5></div>
              <div class="card-body">
                <canvas id="scoreChart"></canvas>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Admin Principal</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>
</div>

<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/plugins/chart.js/Chart.min.js"></script>
<script>
  const sectionCtx = document.getElementById('sectionChart').getContext('2d');
  const interroCtx = document.getElementById('interroChart').getContext('2d');
  const catActiviteCtx = document.getElementById('catActiviteChart').getContext('2d');
  const scoreCtx = document.getElementById('scoreChart').getContext('2d');

  new Chart(sectionCtx, {
    type: 'doughnut',
    data: {
      labels: [<?php foreach ($sections as $s) echo "'{$s['section']}',"; ?>],
      datasets: [{
        data: [<?php foreach ($sections as $s) echo "{$s['total']},"; ?>],
        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6610f2']
      }]
    }
  });

  new Chart(interroCtx, {
    type: 'line',
    data: {
      labels: [<?php foreach ($interro_stats as $row) echo "'{$row['jour']}',"; ?>],
      datasets: [{
        label: 'Interros créées',
        data: [<?php foreach ($interro_stats as $row) echo "{$row['total']},"; ?>],
        borderColor: '#17a2b8',
        backgroundColor: 'rgba(23,162,184,0.1)',
        fill: true
      }]
    }
  });

  // Élèves par catégorie_activite
  new Chart(catActiviteCtx, {
    type: 'pie',
    data: {
      labels: [<?php foreach ($cat_activite as $c) echo "'{$c['categorie_activite']}',"; ?>],
      datasets: [{
        data: [<?php foreach ($cat_activite as $c) echo "{$c['total']},"; ?>],
        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
      }]
    }
  });

  // Distribution des scores
  new Chart(scoreCtx, {
    type: 'bar',
    data: {
      labels: [<?php foreach ($score_stats as $s) echo "'{$s['score']}',"; ?>],
      datasets: [{
        label: 'Nombre d\'élèves',
        data: [<?php foreach ($score_stats as $s) echo "{$s['total']},"; ?>],
        backgroundColor: '#36b9cc'
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>
</body>
</html>
