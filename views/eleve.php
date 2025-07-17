<?php
// Sécurise toutes les variables attendues par la vue
$utilisateur = $utilisateur ?? null;
$quizActif = $quizActif ?? null;
$notifications = $notifications ?? [];
$interrosAVenir = $interrosAVenir ?? [];
$categorie = $categorie ?? '';
$interro_active = $interro_active ?? false;

$photo_profil = ($utilisateur && !empty($utilisateur['photo'])) ? $utilisateur['photo'] : 'uploads/avatars/-default.jpg';
$nb_notifications = 0;
foreach ($notifications as $notif) {
    if (!$notif['lue']) {
        $nb_notifications++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tableau de bord - MC-LEGENDE</title>
  <link href="/assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/assets/dist/css/adminlte.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .notif-icon {
      position: relative;
    }
    .notif-badge {
      position: absolute;
      top: -5px;
      right: -5px;
    }
  </style>
  <script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
  }
</script>

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
          <a href="#" class="d-block">Bienvenue, <?= $utilisateur ? htmlspecialchars($utilisateur['nom']) : '' ?></a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-home"></i>
              <p>Accueil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/eleve/interro" class="nav-link">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Mes Interros</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/eleve/resultats" class="nav-link ">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Mes Résultats</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/eleve/profil" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper p-3">
    <div class="container-fluid">
      <div class="alert alert-info text-center animate__animated animate__fadeInDown">
        <h4 class="mb-0">👋 Bonjour <?= $utilisateur ? htmlspecialchars($utilisateur['prenom']) : '' ?> ! Bienvenue dans votre espace personnel.</h4>
      </div>

            <div class="row">
        <!-- Catégorie : visible en haut sur mobile, à droite sur desktop -->
        <div class="col-12 col-md-4 order-1 order-md-2 mb-3 mb-md-0">
          <div class="d-flex justify-content-end mb-2">
            <span class="badge badge-pill badge-warning shadow p-3 w-100 text-center" style="font-size:1.1rem;">
              <i class="fas fa-tag mr-2"></i>
              Catégorie : <strong><?= htmlspecialchars($categorie) ?></strong>
            </span>
          </div>
        </div>
        <div class="col-12 col-md-8 order-2 order-md-1">
          <div class="card animate__animated animate__zoomIn">
            <div class="card-header">
              <h5 class="card-title"><i class="fas fa-bolt text-warning me-2"></i>État de l'interrogation</h5>
            </div>
            <div class="card-body">
              <?php if ($interro_active): ?>
                <div class="alert alert-success">
                  <h5><?= htmlspecialchars($quizActif['titre']) ?></h5>
                  <p>Une nouvelle interrogation est disponible pour vous !</p>
                  <a href="mes_interro.php?id=<?= $quizActif['id'] ?>" class="btn btn-success">
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
              <h5><i class="fas fa-lightbulb text-info me-2"></i>Conseils avant de commencer</h5>
            </div>
            <div class="card-body">
              <ul>
                <li>⏱️ L'épreuve <strong>est délimitée en minutes</strong> (chronomètre strict, soumission automatique à la fin).</li>
                <li>➡️ <strong>Une fois que vous passez à la question suivante, il n'est plus possible de revenir en arrière.</strong></li>
                <li>🚫 <strong>Ne quittez pas la page, ne changez pas d'onglet, n'actualisez pas et n'utilisez pas le bouton retour du navigateur</strong> sous peine d'annulation immédiate de l'épreuve.</li>
                <li>🔒 <strong>Il est interdit de modifier l'URL ou d'utiliser des outils de triche</strong> (toute tentative sera détectée et l'épreuve annulée).</li>
                <li>🛑 <strong>Une seule tentative est autorisée par interrogation.</strong></li>
                <li>⚠️ Toute tentative de triche détectée entraîne l'annulation immédiate de l'interrogation.</li>
              </ul>
            </div>
          </div>
          <div class="card mt-3 animate__animated animate__fadeInUp">
            <div class="card-header">
              <h5><i class="fas fa-calendar-alt text-primary me-2"></i>Calendrier des Interrogations (à venir)</h5>
            </div>
            <div class="card-body">
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
          
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Pour l'excellence pédagogique
    </div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>
</div>




<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        events: [
          <?php if (!empty($interrosAVenir)) foreach ($interrosAVenir as $interro): ?>
            {
              title: <?= json_encode($interro['titre'] . ' à ' . date('H:i', strtotime($interro['date_lancement']))) ?>,
              start: <?= json_encode(date('Y-m-d\TH:i:s', strtotime($interro['date_lancement']))) ?>,
              url: 'mes_interro.php?id=<?= $interro['id'] ?>',
              color: '#e83e8c', // Couleur rose/violet pour bien ressortir
              textColor: '#fff' // Texte blanc pour le contraste
            },
          <?php endforeach; ?>
        ],
        eventClick: function(info) {
          if (info.event.url) {
            window.location.href = info.event.url;
            info.jsEvent.preventDefault();
          }
        }
      });
      calendar.render();
    }
  });
</script>
</body>
</html>
