<?php
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header("Location: login.php");
    exit();
}

$admin = $_SESSION['utilisateur'];
$id = $admin['id'];

// Récupérer les statistiques
$nbEleves = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='eleve'")->fetchColumn();
$nbQuestions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();

// Préparer données mensuelles pour les graphiques
$mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];

$eleves_par_mois = array_fill(0, 12, 0);
$questions_par_mois = array_fill(0, 12, 0);

$req = $pdo->query("SELECT MONTH(date_inscription) as mois, COUNT(*) as total FROM utilisateurs WHERE role='eleve' GROUP BY mois");
while ($row = $req->fetch()) {
    $eleves_par_mois[$row['mois'] - 1] = (int)$row['total'];
}

$req2 = $pdo->query("SELECT MONTH(created_at) as mois, COUNT(*) as total FROM questions GROUP BY mois");
while ($row = $req2->fetch()) {
    $questions_par_mois[$row['mois'] - 1] = (int)$row['total'];
}

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
  <title>Admin Simple - Tableau de bord</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link href="assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/dist/css/adminlte.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
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
    </a>
    <div class="sidebar">
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
            <a href="admin_simple.php" class="nav-link active">
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
            <a href="profil_admin_simple.php" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="alert alert-primary">
          Bonjour <strong><?= htmlspecialchars($utilisateur['prenom']) ?></strong>, voici les statistiques du jour !
        </div>

        <div class="row">
          <!-- Widget Elèves -->
          <div class="col-lg-6 col-12">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $nbEleves ?></h3>
                <p>Total des élèves</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-graduate"></i>
              </div>
              <a href="gestion_eleve_as.php" class="small-box-footer">Voir les élèves <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <!-- Widget Questions -->
          <div class="col-lg-6 col-12">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $nbQuestions ?></h3>
                <p>Total des questions</p>
              </div>
              <div class="icon">
                <i class="fas fa-question-circle"></i>
              </div>
              <a href="question_admin_simple.php" class="small-box-footer">Voir les questions <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-info text-white">
                <h5 class="card-title">Évolution des élèves par mois</h5>
              </div>
              <div class="card-body">
                <canvas id="elevesChart"></canvas>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-success text-white">
                <h5 class="card-title">Évolution des questions par mois</h5>
              </div>
              <div class="card-body">
                <canvas id="questionsChart"></canvas>
              </div>
            </div>
          </div>
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
<script src="adminlte/plugins/chart.js/Chart.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>


<script>
  const mois = <?= json_encode($mois) ?>;
  const dataEleves = <?= json_encode($eleves_par_mois) ?>;
  const dataQuestions = <?= json_encode($questions_par_mois) ?>;

  new Chart(document.getElementById('elevesChart'), {
    type: 'line',
    data: {
      labels: mois,
      datasets: [{
        label: 'Élèves inscrits',
        data: dataEleves,
        backgroundColor: 'rgba(23, 162, 184, 0.2)',
        borderColor: 'rgba(23, 162, 184, 1)',
        borderWidth: 2
      }]
    }
  });

  new Chart(document.getElementById('questionsChart'), {
    type: 'bar',
    data: {
      labels: mois,
      datasets: [{
        label: 'Questions créées',
        data: dataQuestions,
        backgroundColor: 'rgba(40, 167, 69, 0.6)'
      }]
    }
  });
</script>
</body>
</html>
