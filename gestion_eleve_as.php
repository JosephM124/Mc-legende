<?php
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header("Location: connexion.php");
    exit();
}

$admin = $_SESSION['utilisateur'];
$id = $admin['id'];

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();


// Récupération des catégories pour le filtre
$categories = $pdo->query("SELECT DISTINCT categorie_activite FROM eleves")->fetchAll(PDO::FETCH_COLUMN);

// Traitement des filtres
$filtres = ["u.role = 'eleve' "];
$params = [];

if (!empty($_GET['categorie'])) {
    $filtres[] = "e.categorie_activite = :categorie";
    $params['categorie'] = $_GET['categorie'];
}

$where = implode(' AND ', $filtres);

$sql = "
    SELECT u.id, u.nom, u.prenom, u.email, e.etablissement, e.section, e.categorie_activite 
    FROM utilisateurs u
    JOIN eleves e ON u.id = e.utilisateur_id
    WHERE $where
    ORDER BY u.nom ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <title>Gestion des élèves</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
            <a href="gestion_eleve_as.php" class="nav-link active">
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

  <div class="content-wrapper p-3">
    <div class="container-fluid">

<?php if (isset($_GET['success']) && $_GET['success'] == 'ok'): ?>
    <div class="alert alert-success">Elève ajouté avec succès. !
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


      <div class="row mb-3">
        <div class="col-md-6">
          <h3>Liste des élèves actifs</h3>
        </div>

        

        <?php if ($utilisateur['statut'] !== 'actif'): ?>
  <div class="alert alert-warning mt-3">
    <i class="fas fa-exclamation-triangle"></i> Votre statut est inactif. Vous ne pouvez pas ajouter de nouveaux élèves.
  </div>
<?php endif; ?>

        <div class="col-md-6 text-right">
          <?php if ($utilisateur['statut'] === 'actif'): ?>
  <a href="ajouter_eleve_as.php" class="btn btn-success">+ Ajouter un élève</a>
<?php endif; ?>

        </div>
      </div>

      <!-- Filtre Catégorie -->
      <form method="get" class="mb-3">
        <div class="form-inline">
          <label for="categorie">Catégorie :</label>
          <select name="categorie" id="categorie" class="form-control ml-2">
            <option value="">Toutes</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>" <?= ($_GET['categorie'] ?? '') === $cat ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-secondary ml-2">Filtrer</button>
        </div>
      </form>

      <!-- Tableau -->
      <div class="table-responsive">
        <table id="tableEleves" class="table table-bordered bg-white shadow-sm rounded">
          <thead>
            <tr>
              <th>Nom</th><th>Prénom</th><th>Email</th><th>École</th><th>Section</th><th>Catégorie</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($eleves as $e): ?>
              <tr>
                <td><?= htmlspecialchars($e['nom']) ?></td>
                <td><?= htmlspecialchars($e['prenom']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= htmlspecialchars($e['etablissement']) ?></td>
                <td><?= htmlspecialchars($e['section']) ?></td>
                <td><?= htmlspecialchars($e['categorie_activite']) ?></td>
                <td>
                  <button class="btn btn-info btn-sm voir-btn" data-id="<?= $e['id'] ?>">
  <i class="fas fa-eye"></i> Voir
</button>



                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">



<script>
  $(document).ready(function () {
    $('#tableEleves').DataTable({
      "language": {
        "lengthMenu": "Afficher _MENU_ élèves par page",
        "zeroRecords": "Aucun résultat trouvé",
        "info": "Page _PAGE_ sur _PAGES_",
        "infoEmpty": "Aucun élève disponible",
        "infoFiltered": "(filtré parmi _MAX_ élèves)",
        "search": "Rechercher :",
        "paginate": {
          "previous": "Précédent",
          "next": "Suivant"
        }
      }
    });
  });
</script>
<script>
$(document).ready(function() {
  $('.voir-btn').on('click', function() {
    const id = $(this).data('id');
    
    $('#modalDetailsContent').html(`
      <div class="text-center my-4">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Chargement...</span>
        </div>
        <p class="mt-2">Chargement des informations...</p>
      </div>
    `);

    $('#voirModal').modal('show');

    $.ajax({
      url: 'ajax/voir_eleve_as.php',
      type: 'GET',
      data: { id: id },
      success: function(response) {
        $('#modalDetailsContent').html(response);
      },
      error: function() {
        $('#modalDetailsContent').html('<div class="alert alert-danger">Erreur de chargement des données.</div>');
      }
    });
  });
});
</script>


<!-- Modal Élève - Voir les détails -->
<div class="modal fade" id="voirModal" tabindex="-1" aria-labelledby="voirModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="voirModalLabel"><i class="fas fa-user-graduate mr-2"></i> Détails de l’élève</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body bg-light" id="modalDetailsContent">
        <!-- Le contenu AJAX (voir_eleve.php) viendra ici -->
        <div class="text-center my-4">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Chargement des informations...</p>
        </div>
      </div>

      <div class="modal-footer bg-white">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times"></i> Fermer
        </button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
