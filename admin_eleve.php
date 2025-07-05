<?php
// Sécurisation avancée de la session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}
session_start();
// Regénération d'ID de session après login (à faire aussi dans traitement_connexion.php)
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}

// Initialisation sécurisée du tableau de tokens
if (!isset($_SESSION['modif_tokens']) || !is_array($_SESSION['modif_tokens'])) {
    $_SESSION['modif_tokens'] = [];
}



if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location:connexion.php");
    exit();
}


include 'databaseconnect.php';
require_once 'fonctions.php'; // Ajout pour accès aux fonctions CSRF
$id = $_SESSION['utilisateur']['id'];
 // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();

// Récupération des valeurs distinctes pour les filtres
$categories = $pdo->query("SELECT DISTINCT categorie_activite FROM eleves ORDER BY categorie_activite")->fetchAll(PDO::FETCH_COLUMN);
$villes = $pdo->query("SELECT DISTINCT ville_province FROM eleves ORDER BY ville_province")->fetchAll(PDO::FETCH_COLUMN);

// Gestion des filtres
$filtre_categorie = isset($_GET['filtre_categorie']) ? htmlspecialchars(trim($_GET['filtre_categorie'])) : '';
$filtre_ville = isset($_GET['filtre_ville']) ? htmlspecialchars(trim($_GET['filtre_ville'])) : '';

// Construction dynamique de la requête SQL avec filtres
$sql = "SELECT 
    u.id, 
    u.nom, 
    u.prenom, 
    u.email, 
    u.telephone, 
    u.sexe,
    u.role, 
    e.ville_province,
    e.pays,
    e.etablissement, 
    e.section, 
    e.adresse_ecole,
    e.categorie_activite
FROM utilisateurs u
INNER JOIN eleves e ON u.id = e.utilisateur_id WHERE role='eleve'";
$params = [];
if ($filtre_categorie !== '') {
    $sql .= " AND e.categorie_activite = ?";
    $params[] = $filtre_categorie;
}
if ($filtre_ville !== '') {
    $sql .= " AND e.ville_province = ?";
    $params[] = $filtre_ville;
}
$sql .= " ORDER BY u.nom ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des élèves - Admin</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
  }
</script>
<style>
.modal-content {
  border-radius: 12px;
}
.modal-header, .modal-footer {
  border: none;
}
.list-group-item {
  background-color: transparent;
  border: none;
  padding-left: 0;
}
.profile-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card {
        border: none;
        border-radius: 8px;
    }
    
    .info-item {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .text-primary {
        color: #3490dc !important;
    }
    
    .profile-photo-container {
        border: 5px solid white;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .gap-1 > * + * {
  margin-left: 0.25rem;
}
</style>


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
          <a href="admin_eleve.php" class="nav-link active">
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
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Gestion des élèves</h1>
          </div>
          <div class="col-sm-6 text-right">
            <a href="ajouter_eleve.php" class="btn btn-success">+ Ajouter un élève</a>
          </div>
        </div>
      </div>
    </div>
  

    <section class="content">
    <div class="container-fluid">
    <?php if (isset($_GET['modification']) && $_GET['modification'] == 'ok'): ?>
    <div class="alert alert-success">Élève Modifié avec succès. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
        
    <?php endif; ?>
    <div class="container-fluid">
    <?php if (isset($_GET['ajout']) && $_GET['ajout'] == 'ok'): ?>
    <div class="alert alert-success">Élève ajouté avec succès. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
        
    <?php endif; ?>
    <?php if (isset($_GET['suppression']) && $_GET['suppression'] == 'ok'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Élève supprimé avec succès.
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
   <?php endif; ?>

   <div class="container-fluid mb-3">
  <form method="get" class="form-inline row g-2 align-items-end">
    <div class="col-auto">
      <label for="filtre_categorie" class="form-label mb-0">Catégorie&nbsp;:</label>
      <select name="filtre_categorie" id="filtre_categorie" class="form-control form-control-sm">
        <option value="">Toutes</option>
        <?php foreach ($categories as $categorie): ?>
          <option value="<?= htmlspecialchars($categorie) ?>" <?= $filtre_categorie === $categorie ? 'selected' : '' ?>><?= htmlspecialchars($categorie) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <label for="filtre_ville" class="form-label mb-0">Ville&nbsp;:</label>
      <select name="filtre_ville" id="filtre_ville" class="form-control form-control-sm">
        <option value="">Toutes</option>
        <?php foreach ($villes as $ville): ?>
          <option value="<?= htmlspecialchars($ville) ?>" <?= $filtre_ville === $ville ? 'selected' : '' ?>><?= htmlspecialchars($ville) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filtrer</button>
      <a href="admin_eleve.php" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
    </div>
  </form>
</div>

   <div class="card">
          <div class="card-body">
            <div class="table-responsive">
            <table id="tableEleves" class="table table-bordered  table-hover">

    <thead>
        <tr>
            <th>Nom complet</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Sexe</th>
            <th>Ville</th>
            <th>Pays</th>
            <th>École</th>
            <th>Section</th>
            <th>Adresse de l'école</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
   <?php   ?> 

        <?php foreach ($eleves as $row) { ?>
            <tr>
                <td><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['telephone']) ?></td>
                <td><?= htmlspecialchars($row['sexe']) ?></td>
                <td><?= htmlspecialchars($row['ville_province']) ?></td>
                <td><?= htmlspecialchars($row['pays']) ?></td>
                <td><?= htmlspecialchars($row['etablissement']) ?></td>
                <td><?= htmlspecialchars($row['section']) ?></td>
                <td><?= htmlspecialchars($row['adresse_ecole']) ?></td>
                <td>
                  <div class="d-flex flex-row gap-1 justify-content-center align-items-center">
                    <a href="#" class="btn btn-sm btn-info voir-btn mr-1" title="Voir" data-id="<?= $row['id'] ?>" data-toggle="modal" data-target="#voirModal">
                      <i class="fas fa-eye"></i>
                    </a>
                    <?php 
                      $token = bin2hex(random_bytes(32));
                      $_SESSION['modif_tokens'][$token] = $row['id'];
                    ?>
                    <a href="modifier_eleve.php?token=<?= $token ?>" class="btn btn-sm btn-warning mr-1" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php 
                      $csrf_token = generer_csrf_token('suppression_eleve'); 
                    ?>
                    <a href="supprimer_eleve.php?id=<?= $row['id'] ?>&csrf_token=<?= $csrf_token ?>"
                      onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')"
                      class="btn btn-danger btn-sm" title="Supprimer">
                      <i class="fas fa-trash"></i>
                    </a>
                  </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</div>

          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Admin Principal</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>

</div>

<!-- MODAL AMÉLIORÉ - Détails Élève -->
<div class="modal fade" id="voirModal" tabindex="-1" role="dialog" aria-labelledby="voirModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="voirModalLabel">
          <i class="fas fa-user-graduate mr-2"></i> Détails de l’élève
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" id="modalDetailsContent" style="background-color: #f8f9fa;">
        <!-- Contenu injecté dynamiquement par AJAX -->
        <div class="text-center my-3">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des informations...</p>
        </div>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times"></i> Fermer
        </button>
      </div>
    </div>
  </div>
</div>

<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
  $(function () {
    $("#tableEleves").DataTable({
      responsive: true,
      autoWidth: false,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
      }
    });
  });
</script>

<script>
$(document).ready(function(){
    $('.voir-btn').on('click', function(){
        var id = $(this).data('id');

        $.ajax({
            url: 'ajax/voir_eleve.php',
            type: 'GET',
            data: { id: id },
            success: function(response){
                $('#modalDetailsContent').html(response);
            },
            error: function(){
                $('#modalDetailsContent').html('<p class="text-danger">Erreur de chargement.</p>');
            }
        });
    });
});
</script>

</body>
</html>
