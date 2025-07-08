<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

$id = $_SESSION['utilisateur']['id'];
$req = $pdo->prepare("SELECT nom, prenom, photo, role FROM utilisateurs WHERE id = ?");
$req->execute([$id]);
$utilisateur = $req->fetch();
try {
    $pdo->exec("UPDATE quiz SET statut = 'prévu' WHERE date_lancement > NOW()");
} catch (PDOException $e) {
    echo "Erreur 1 : " . $e->getMessage();
}

try {
    $pdo->prepare("
        UPDATE quiz 
        SET statut = 'actif'
        WHERE statut != 'actif' 
          AND NOW() >= date_lancement 
          AND NOW() < DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE)
    ")->execute();
} catch (PDOException $e) {
    echo "Erreur 2 : " . $e->getMessage();
}

try {
    $pdo->prepare("
        UPDATE quiz 
        SET statut = 'inactif'
        WHERE statut != 'inactif' 
          AND NOW() >= DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE)
    ")->execute();
} catch (PDOException $e) {
    echo "Erreur 3 : " . $e->getMessage();
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



$interros_actives = $pdo->prepare("SELECT * FROM quiz WHERE statut = 'actif' AND categorie = ?");
$interros_actives->execute([$categorie]);
$actives = $interros_actives->fetchAll();

$interros_passees = $pdo->prepare("
    SELECT q.titre, q.date_lancement, r.score, r.date_passage, r.statut
    FROM resultats r
    JOIN quiz q ON r.quiz_id = q.id
    WHERE r.utilisateur_id = ? AND q.categorie = ?
    ORDER BY r.date_passage DESC
");
$interros_passees->execute([$id, $categorie]);
$passees = $interros_passees->fetchAll();

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

$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'uploads/avatars/default.jpg';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Interros - MC-LEGENDE</title>
  <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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
          <a href="#" class="d-block">Bienvenue, <?= htmlspecialchars($utilisateur['prenom']) ?></a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="/eleve/home" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Accueil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/eleve/interro" class="nav-link active">
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
      
    <?php if (isset($_GET['deja']) && $_GET['deja'] == 'ok'): ?>
    <div class="alert alert-danger">Vous avez déjà passé cette interrogation. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('deja');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>

    <?php if (isset($_GET['invalid']) && $_GET['inavalid'] == 'ok'): ?>
    <div class="alert alert-danger">Interrogation invalide. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('invalid');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>

      <div class="content-header">
        <div class="container-fluid">
          <h1 class="m-0 text-dark">Mes Interrogations</h1>
        </div>
      </div>

      <!-- Interros Actives -->
      <div class="card animate__animated animate__fadeInLeft">
        <div class="card-header bg-success">
          <h3 class="card-title"><i class="fas fa-bolt"></i> Interrogations Disponibles</h3>
        </div>
        <div class="card-body">
          <?php if (count($actives) > 0): ?>
            <ul class="list-group">
              <?php foreach ($actives as $interro): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?= htmlspecialchars($interro['titre']) ?>
                  <a href="passer_interro.php?id=<?= $interro['id'] ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-play"></i> Commencer
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">Aucune interrogation active pour le moment.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Interros Passées -->
      <div class="card mt-4 animate__animated animate__fadeInRight">
        <div class="card-header bg-info">
          <h3 class="card-title"><i class="fas fa-history"></i> Interrogations Passées</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Date</th>
                <th>Score</th>
                <th>État</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($passees) > 0): ?>
                <?php foreach ($passees as $past): ?>
                  <tr>
                    <td><?= htmlspecialchars($past['titre']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($past['date_passage'])) ?></td>
                    <td>
                    <?php if ($past['statut'] == 1): ?>
                    <?= $past['score'] ?>/10
                     <?php else: ?>
                    <span class="text-muted"><i class="fas fa-lock"></i> Indisponible</span>
                    <?php endif; ?>
                     </td>

                    <td><i class="fas fa-check-circle text-success"></i> Terminé</td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">Aucune interrogation passée.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
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
<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
