<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord Admin Principal - MC-LEGENDE</title>
  <link href="/assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/assets/dist/css/adminlte.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
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
      <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
        <img id="photo-profil" src="uploads/avatars/default.jpeg" class="img-circle elevation-2" alt="Admin">
      </div>
      <div class="info">
        <a href="#" class="d-block">Bienvenue <span id="prenom-admin">Admin</span></a>
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
                <h3 id="total-eleves">0</h3>
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
                <h3 id="total-interros">0</h3>
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
                <h3 id="total-notifications">0</h3>
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
                <h3 id="total-admins">0</h3>
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
// Chargement dynamique du dashboard via la route tout-en-un
fetch('/api/admin/dashboard_stats')
  .then(res => res.json())
  .then(data => {
    if(data.data) {
      // Widgets
      document.getElementById('total-eleves').textContent = data.data.total_eleves || 0;
      document.getElementById('total-interros').textContent = data.data.total_interros || 0;
      document.getElementById('total-notifications').textContent = data.data.total_notifications || 0;
      document.getElementById('total-admins').textContent = data.data.total_admins || 0;

      // Graphique sections
      const sectionLabels = (data.data.sections || []).map(s => s.section);
      const sectionValues = (data.data.sections || []).map(s => s.total);
      new Chart(document.getElementById('sectionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: sectionLabels,
          datasets: [{
            data: sectionValues,
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6610f2']
          }]
        }
      });

      // Graphique interros par jour
      const interroLabels = (data.data.interro_stats || []).map(row => row.jour);
      const interroValues = (data.data.interro_stats || []).map(row => row.total);
      new Chart(document.getElementById('interroChart').getContext('2d'), {
        type: 'line',
        data: {
          labels: interroLabels,
          datasets: [{
            label: 'Interros créées',
            data: interroValues,
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23,162,184,0.1)',
            fill: true
          }]
        }
      });

      // Graphique catégories d'activité
      const catLabels = (data.data.cat_activite || []).map(c => c.categorie_activite);
      const catValues = (data.data.cat_activite || []).map(c => c.total);
      new Chart(document.getElementById('catActiviteChart').getContext('2d'), {
        type: 'pie',
        data: {
          labels: catLabels,
          datasets: [{
            data: catValues,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
          }]
        }
      });

      // Graphique distribution des scores
      const scoreLabels = (data.data.score_stats || []).map(s => s.score);
      const scoreValues = (data.data.score_stats || []).map(s => s.total);
      new Chart(document.getElementById('scoreChart').getContext('2d'), {
        type: 'bar',
        data: {
          labels: scoreLabels,
          datasets: [{
            label: "Nombre d'élèves",
            data: scoreValues,
            backgroundColor: '#36b9cc'
          }]
        },
        options: {
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    }
  });
// TODO: Charger la photo et le prénom de l'admin via l'API utilisateur
</script>
</body>
</html>
