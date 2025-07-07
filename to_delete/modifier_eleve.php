<?php
session_start();

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: connexion.php");
    exit();
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';

$token = $_GET['token'] ?? '';
if (!isset($_SESSION['modif_tokens'][$token])) {
    exit("Lien invalide ou expiré.");
}
$id_e = intval($_SESSION['modif_tokens'][$token]);



// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $postnom = $_POST['postnom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $pays = $_POST['pays'];
    $date_naissance = $_POST['naissance'];
    $sexe = $_POST['sexe'];
    $etablissement = $_POST['etablissement'];
    $section = $_POST['section'];
    $adresse_ecole = $_POST['adresse_ecole'];
    $statut = $_POST['statut'];
    $categorie = $_POST['categorie'];
    $role = $_POST['role'];

    // Mise à jour des tables
    $pdo->prepare("UPDATE utilisateurs SET nom=?, postnom=?, prenom=?, email=?, telephone=?, adresse=?, naissance=?, sexe=?, statut=?, role=? WHERE id=?")
        ->execute([$nom, $postnom, $prenom, $email, $telephone, $adresse, $date_naissance, $sexe, $statut, $role, $id_e]);

    $pdo->prepare("UPDATE eleves SET etablissement=?, section=?, adresse_ecole=?, categorie_activite=?, ville_province=?, pays=? WHERE utilisateur_id=?")
        ->execute([$etablissement, $section, $adresse_ecole, $categorie, $ville, $pays, $id_e]);

    enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Modification d'un élève", "Élève : $nom $postnom $prenom");

    unset($_SESSION['modif_tokens'][$token]); // Seulement ici
    

    header("Location: admin_eleve.php?modification=ok");
    exit();
}

// Récupération des infos de l'élève
$stmt = $pdo->prepare("SELECT u.nom, u.postnom, u.prenom, u.email, u.telephone, u.adresse, e.ville_province, e.pays, u.naissance, u.sexe, u.role,
                              e.etablissement, e.section, e.adresse_ecole, u.statut, e.categorie_activite
                       FROM utilisateurs u
                       JOIN eleves e ON u.id = e.utilisateur_id
                       WHERE u.id = ?");
$stmt->execute([$id_e]);
$eleve = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$eleve) {
    echo "Élève non trouvé.";
    exit();
}

// Infos de l'admin connecté pour l'affichage
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['utilisateur']['id']]);
$utilisateur = $stmt->fetch();
?>

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un élève</title>
    <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  
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
  <a href="admin_principal.php" class="brand-link text-center">
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
            <h1 class="m-3">Modifier un élève</h1>
        </div>
        <section class="content">
            <div class="container-fluid">
                <form method="POST">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="row">
                                <!-- Infos personnelles -->
                                <div class="form-group col-md-4">
                                    <label>Nom</label>
                                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($eleve['nom']) ?>" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Postnom</label>
                                    <input type="text" name="postnom" class="form-control" value="<?= htmlspecialchars($eleve['postnom']) ?>" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Prénom</label>
                                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($eleve['prenom']) ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($eleve['email']) ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Téléphone</label>
                                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($eleve['telephone']) ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Adresse</label>
                                    <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($eleve['adresse']) ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Ville</label>
                                    <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($eleve['ville_province']) ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Pays</label>
                                    <input type="text" name="pays" class="form-control" value="<?= htmlspecialchars($eleve['pays']) ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Date de naissance</label>
                                    <input type="date" name="naissance" class="form-control" value="<?= htmlspecialchars($eleve['naissance']) ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Sexe</label>
                                    <select name="sexe" class="form-control">
                                        <option value="M" <?= $eleve['sexe'] === 'M' ? 'selected' : '' ?>>Masculin</option>
                                        <option value="F" <?= $eleve['sexe'] === 'F' ? 'selected' : '' ?>>Féminin</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Rôle</label>
                                    <input type="text" class="form-control"  name="role" value="<?= htmlspecialchars($eleve['role']) ?>" >
                                </div>
                                <div class="form-group col-md-6">
                                <label>Statut</label>
                                <select name="statut" class="form-control" required>
                                <option value="Actif" <?= $eleve['statut'] === 'Actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="Inactif" <?= $eleve['statut'] === 'Inactif' ? 'selected' : '' ?>>Inactif</option>
                               </select>
                               </div>
                               <div class="form-group col-md-6">
                              <label>Catégorie d'activité</label>
                              <select name="categorie" class="form-control" required>
                              <option value="Musique" <?= $eleve['categorie_activite'] === 'Musique' ? 'selected' : '' ?>>Musique</option>
                              <option value="Danse" <?= $eleve['categorie_activite'] === 'Danse' ? 'selected' : '' ?>>Danse</option>
                              <option value="Art" <?= $eleve['categorie_activite'] === 'Art' ? 'selected' : '' ?>>Art</option>
                             <option value="Culture générale" <?= $eleve['categorie_activite'] === 'Culture générale' ? 'selected' : '' ?>>Culture générale</option>
                             </select>
                             </div>


                                <!-- Infos scolaires -->
                                <div class="form-group col-md-6">
                                    <label>Établissement</label>
                                    <input type="text" name="etablissement" class="form-control" value="<?= htmlspecialchars($eleve['etablissement']) ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Section</label>
                                    <input type="text" name="section" class="form-control" value="<?= htmlspecialchars($eleve['section']) ?>" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Adresse de l'école</label>
                                    <input type="text" name="adresse_ecole" class="form-control" value="<?= htmlspecialchars($eleve['adresse_ecole']) ?>" required>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="admin_eleve.php" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
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
<script src="adminlte/dist/js/adminlte.min.js"></script>


</body>
</html>
