<?php
// Sécurisation avancée de la session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}
session_start();
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}
// Headers HTTP de sécurité
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");


require_once 'databaseconnect.php'; // Connexion BDD
require_once 'fonctions.php'; // Fonction de sécurité/session

// Vérifie si l'utilisateur est connecté et est admin principal
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: connexion.php');
    exit;
}
$id = $_SESSION['utilisateur']['id'];
 // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();
$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'uploads/avatars/default.jpg';

// Récupération des admin_simple
$sql = "SELECT * FROM utilisateurs where role='admin_simple' ORDER BY date_inscription DESC";
$stmt = $pdo->query($sql);
$admins = $stmt->fetchAll();

// Protection CSRF pour la suppression d'admin : régénération à chaque affichage
$_SESSION['csrf_delete_admin'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Admins Simples - MC-LEGENDE</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
          <a href="admin_principal.php" class="nav-link">
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
          <a href="question_admin.php" class="nav-link ">
            <i class="nav-icon fas fa-question"></i>
            <p>Questions</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="gestion_notifications.php" class="nav-link ">
            <i class="nav-icon fas fa-bell"></i>
            <p>Notifications</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="gestion_admins.php" class="nav-link active">
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
          <a href="historique_activites.php" class="nav-link ">
            <i class="nav-icon fas fa-history"></i>
            <p>Historique des activités</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>


  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0 text-dark">Gestion des Admins Simples</h1>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
       <?php if (isset($_GET['success']) && $_GET['success'] == 'ok'): ?>
    <div class="alert alert-success">admin ajouté avec succès. !
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
    <?php if (isset($_GET['success']) && $_GET['success'] == 'modif'): ?>
    <div class="alert alert-success">admin modifié avec succès. !
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
    <?php if (isset($_GET['success']) && $_GET['success'] == 'suppression'): ?>
    <div class="alert alert-success">admin supprimé avec succès. !
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

        <!-- Bouton pour ouvrir le modal -->
<button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAjoutAdmin">
  <i class="fas fa-user-plus"></i> Ajouter un admin simple
</button>


        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Liste des admins simples</h3>
          </div>
          <div class="card-body">
            <table id="tableAdmins" class="table table-bordered table-striped dt-responsive nowrap">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Prénom</th>
                  <th>Email</th>
                  <th>Téléphone</th>
                  <th>Statut</th>
                  <th>Date création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($admins as $admin): ?>
                  <tr>
                    <td><?= htmlspecialchars($admin['nom']) ?></td>
                    <td><?= htmlspecialchars($admin['prenom']) ?></td>
                    <td><?= htmlspecialchars($admin['email']) ?></td>
                    <td><?= htmlspecialchars($admin['telephone']) ?></td>
                    <td>
                      <span class="badge badge-<?= $admin['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($admin['statut']) ?>
                      </span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($admin['date_inscription'])) ?></td>
                    <td>
                      <a href="#" class="btn btn-sm btn-warning btn-edit-admin"
                      data-id="<?= $admin['id'] ?>"
                      data-nom="<?= htmlspecialchars($admin['nom'], ENT_QUOTES) ?>"
                      data-prenom="<?= htmlspecialchars($admin['prenom'], ENT_QUOTES) ?>"
                      data-email="<?= htmlspecialchars($admin['email'], ENT_QUOTES) ?>"
                      data-telephone="<?= htmlspecialchars($admin['telephone'], ENT_QUOTES) ?>"
                      data-statut="<?= $admin['statut'] ?>"
                      data-toggle="modal" data-target="#modalModifierAdmin"
                      title="Modifier">
                      <i class="fas fa-edit"></i>
                      </a>

                      <a href="#" class="btn btn-sm btn-danger btn-delete-admin"
                      data-id="<?= $admin['id'] ?>"
                      data-nom="<?= htmlspecialchars($admin['nom'] . ' ' . $admin['prenom']) ?>"
                      data-toggle="modal"
                      data-target="#modalSuppressionAdmin"
                      title="Supprimer">
                      <i class="fas fa-trash-alt"></i>
                       </a>

                      <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalVoirAdmin<?= $admin['id'] ?>" title="Voir">
                     <i class="fas fa-eye"></i>
                      </a>

                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

<?php foreach ($admins as $admin): 
?>
<div class="modal fade" id="modalVoirAdmin<?= $admin['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalVoirAdminLabel<?= $admin['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalVoirAdminLabel<?= $admin['id'] ?>">Détails de l’administrateur</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body row">
        <div class="col-md-4 text-center">
          <img src="<?= htmlspecialchars($admin['photo']) ?>" alt="Photo" class="img-fluid rounded-circle img-thumbnail" style="max-height: 200px;">
        </div>
        <div class="col-md-8">
          <ul class="list-group">
            <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($admin['nom']) ?></li>
            <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($admin['prenom']) ?></li>
            <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($admin['email']) ?></li>
            <li class="list-group-item"><strong>Téléphone :</strong> <?= htmlspecialchars($admin['telephone']) ?></li>
            <li class="list-group-item"><strong>Statut :</strong>
              <span class="badge badge-<?= $admin['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                <?= ucfirst($admin['statut']) ?>
              </span>
            </li>
            <li class="list-group-item"><strong>Date d’inscription :</strong> <?= date('d/m/Y H:i', strtotime($admin['date_inscription'])) ?></li>
          </ul>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

            </table>
          </div>
        </div>

      </div>
    </section>
  </div>
<!-- Modal d'ajout d'un admin simple -->
<div class="modal fade" id="modalAjoutAdmin" tabindex="-1" role="dialog" aria-labelledby="modalAjoutAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="ajout_admin.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title" id="modalAjoutAdminLabel">Ajouter un admin simple</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body row">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div class="form-group col-md-6">
          <label for="nom">Nom </label>
          <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="prenom">Prénom </label>
          <input type="text" name="prenom" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="email">Email </label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="telephone">Téléphone </label>
          <input type="text" name="telephone" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="mot_de_passe">Mot de passe </label>
          <input type="password" name="mot_de_passe" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="photo">Photo (optionnelle)</label>
          <input type="file" name="photo" class="form-control-file">
        </div>

        <div class="form-group col-md-6">
          <label for="statut">Statut </label>
          <select name="statut" class="form-control" required>
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
          </select>
        </div>

        <div class="form-group col-md-6">
          <label for="inscription_complete">Inscription complète ? </label>
          <select name="inscription_complete" class="form-control" required>
            <option value="1">1</option>
            <option value="0">0</option>
          </select>
        </div>

        <!-- champ caché pour rôle -->
        <input type="hidden" name="role" value="admin_simple">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalModifierAdmin" tabindex="-1" role="dialog" aria-labelledby="modalModifierAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="modifier_admin.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="modalModifierAdminLabel">Modifier un admin simple</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body row">
        <input type="hidden" name="id" id="edit-id">

        <div class="form-group col-md-6">
          <label for="edit-nom">Nom</label>
          <input type="text" name="nom" id="edit-nom" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="edit-prenom">Prénom</label>
          <input type="text" name="prenom" id="edit-prenom" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="edit-email">Email</label>
          <input type="email" name="email" id="edit-email" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="edit-telephone">Téléphone</label>
          <input type="text" name="telephone" id="edit-telephone" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
          <label for="edit-statut">Statut</label>
          <select name="statut" id="edit-statut" class="form-control" required>
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
          </select>
        </div>

        <div class="form-group col-md-6">
          <label for="photo">Nouvelle photo (optionnelle)</label>
          <input type="file" name="photo" class="form-control-file">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-warning">Modifier</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalSuppressionAdmin" tabindex="-1" role="dialog" aria-labelledby="modalSuppressionAdminLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="supprimer_admin.php" method="POST" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalSuppressionAdminLabel">Confirmation de suppression</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="delete-id">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_delete_admin']) ?>">
        <p>Voulez-vous vraiment supprimer <strong id="delete-nom"></strong> ? Cette action est irréversible.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-danger">Supprimer</button>
      </div>
    </form>
  </div>
</div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Admin Principal</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>
</div>

<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    $("#tableAdmins").DataTable({
      responsive: true,
      autoWidth: false,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
      }
    });
  });
</script>
<script>
$(document).ready(function() {
  $('.btn-edit-admin').on('click', function() {
    $('#edit-id').val($(this).data('id'));
    $('#edit-nom').val($(this).data('nom'));
    $('#edit-prenom').val($(this).data('prenom'));
    $('#edit-email').val($(this).data('email'));
    $('#edit-telephone').val($(this).data('telephone'));
    $('#edit-statut').val($(this).data('statut'));
  });
});
</script>
<script>
$(document).ready(function() {
  $('.btn-delete-admin').on('click', function() {
    const id = $(this).data('id');
    const nom = $(this).data('nom');
    $('#delete-id').val(id);
    $('#delete-nom').text(nom);
  });
});
</script>

</body>
</html>
