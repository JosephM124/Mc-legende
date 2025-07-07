<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header('Location: connexion.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

require_once 'databaseconnect.php';
require_once 'log_admin.php';

$id = $_SESSION['utilisateur']['id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();
$statut = strtolower($utilisateur['statut']);

$message = '';

if ($statut === 'actif') {
  // Traitement modification
  if (isset($_POST['update_question'])) {
      $id_q = $_POST['question_id'];
      $texte = $_POST['texte_question'];
      $opt1 = $_POST['option_1'];
      $opt2 = $_POST['option_2'];
      $opt3 = $_POST['option_3'];
      $opt4 = $_POST['option_4'];
      $bonne = $_POST['bonne_reponse'];
      $cat = $_POST['categorie'];

      $stmt = $pdo->prepare("UPDATE questions SET texte_question = ?, option_1 = ?, option_2 = ?, option_3 = ?, option_4 = ?, bonne_reponse = ?, categorie = ? WHERE id = ?");
      $stmt->execute([$texte, $opt1, $opt2, $opt3, $opt4, $bonne, $cat, $id_q]);

      enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Modification d'une question",  "Question: $texte   |  Catégorie :$cat ");

      header("Location: question_admin_simple.php?modif=ok");
      exit();
  }

  
}

// Récupérer les questions filtrées
$categorie = $_GET['categorie'] ?? '';
if ($categorie != '') {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE categorie = ?");
    $stmt->execute([$categorie]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM questions");
    $stmt->execute();
}
$questions = $stmt->fetchAll();

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
  <title>Gestion des Questions</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

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
            <a href="question_admin_simple.php" class="nav-link active">
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

  
  <!-- Contenu principal -->
  <div class="content-wrapper p-3">
    <h2>Gestion des Questions</h2>
    
    <?php if (isset($_GET['modif']) && $_GET['modif'] == 'ok'): ?>
    <div class="alert alert-success">Questions modifiée avec succès. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('modif');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>


    <?php if ($message): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['success']) && $_GET['success'] == 'ok'): ?>
    <div class="alert alert-success">Questions supprimée avec succès. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('success');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>
    <?php if (isset($_GET['success']) && $_GET['success'] == 'echec'): ?>
    <div class="alert alert-danger">Echec lors de la suppression. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('success');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>
    <?php if (isset($_GET['import']) && $_GET['import'] == 'ok'): ?>
    <div class="alert alert-success">Questions importées avec succes. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('import');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>
    <?php if (isset($_GET['import']) && $_GET['import'] == 'echec'): ?>
    <div class="alert alert-danger">Echec lors de l'importation. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('import');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>
<form method="get" class="form-inline mb-3">
  <div class="form-group mr-2">
    <label for="filtre_categorie" class="mr-2">Catégorie :</label>
    <select name="categorie" id="filtre_categorie" class="form-control">
      <option value="">Toutes</option>
      <option value="Culture générale" <?= ($_GET['categorie'] ?? '') == 'Culture générale' ? 'selected' : '' ?>>Culture générale</option>
      <option value="Musique" <?= ($_GET['categorie'] ?? '') == 'Musique' ? 'selected' : '' ?>>Musique</option>
      <option value="Danse" <?= ($_GET['categorie'] ?? '') == 'Danse' ? 'selected' : '' ?>>Danse</option>
      <option value="Art" <?= ($_GET['categorie'] ?? '') == 'Art' ? 'selected' : '' ?>>Art</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Filtrer</button>
</form>

    <?php if ($statut === 'actif'): ?>
<!-- bouton importer, modals importer, supprimer toutes -->
<!-- boutons Modifier / Supprimer dans les lignes de questions -->
 <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalImport">+ Importer des questions</button>
<?php endif; ?>


    
 <div class="table-responsive">
    <table class="table table-bordered bg-white shadow-sm rounded" id="questionsTable">


      <thead>
        <tr>
          <th>Question</th>
          <th>Catégorie</th>
          <th>Réponses</th>
          <th>Bonne réponse</th>
          <?php if ($statut === 'actif'): ?><th>Actions</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($questions as $q): ?>
          <tr>
            <td><?= htmlspecialchars($q['texte_question']) ?></td>
            <td><?= htmlspecialchars($q['categorie']) ?></td>
            <td>
              <ol type="A">
                <li><?= $q['option_1'] ?></li>
                <li><?= $q['option_2'] ?></li>
                <li><?= $q['option_3'] ?></li>
                <li><?= $q['option_4'] ?></li>
              </ol>
            </td>
            <td><?= $q['bonne_reponse'] ?></td>
              <?php if ($statut === 'actif'): ?>
  <td>
    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?= $q['id'] ?>">Modifier</button>
    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalSupprimer<?= $q['id'] ?>">Supprimer</button>
  </td>
  <?php endif; ?>
  <!-- Modal de modification pour la question ID <?= $q['id'] ?> -->
<div class="modal fade" id="modalEdit<?= $q['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel<?= $q['id'] ?>" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST">
      <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title">Modifier la question</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Texte de la question</label>
            <textarea name="texte_question" class="form-control" required><?= htmlspecialchars($q['texte_question']) ?></textarea>
          </div>
          <div class="form-group">
            <label>Option A</label>
            <input type="text" name="option_1" class="form-control" value="<?= htmlspecialchars($q['option_1']) ?>" required>
          </div>
          <div class="form-group">
            <label>Option B</label>
            <input type="text" name="option_2" class="form-control" value="<?= htmlspecialchars($q['option_2']) ?>" required>
          </div>
          <div class="form-group">
            <label>Option C</label>
            <input type="text" name="option_3" class="form-control" value="<?= htmlspecialchars($q['option_3']) ?>" required>
          </div>
          <div class="form-group">
            <label>Option D</label>
            <input type="text" name="option_4" class="form-control" value="<?= htmlspecialchars($q['option_4']) ?>" required>
          </div>
          <div class="form-group">
            <label>Bonne réponse (A, B, C, D)</label>
            <input type="text" name="bonne_reponse" class="form-control" value="<?= htmlspecialchars($q['bonne_reponse']) ?>" required>
          </div>
          <div class="form-group">
            <label>Catégorie</label>
            <input type="text" name="categorie" class="form-control" value="<?= htmlspecialchars($q['categorie']) ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update_question" class="btn btn-primary">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="modalSupprimer<?= $q['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalSupprimerLabel<?= $q['id'] ?>" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="supprimer_question_as.php">
      <input type="hidden" name="supprimer_id" value="<?= $q['id'] ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          Voulez-vous vraiment supprimer cette question ?
          <strong><?= htmlspecialchars($q['texte_question']) ?></strong>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </div>
      </div>
    </form>
  </div>
</div>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
   </div> 
   

  

  </div>
  
  

  <!-- Modal Import Excel -->
  <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" enctype="multipart/form-data" action="importer_question_as.php">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Importer des questions via Excel</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="file" name="excel_file" class="form-control" required>
            <small class="form-text text-muted">Format : question, option1, option2, option3, option4, bonne_reponse, categorie</small>
          </div>
          <div class="modal-footer">
            <button type="submit" name="import_excel" class="btn btn-primary">Importer</button>
          </div>
        </div>
      </form>
    </div>
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
<script>
  $(function () {
    $('#questionsTable').DataTable();
  });
</script>

</body>
</html>
