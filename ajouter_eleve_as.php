<?php
session_start();
require_once 'databaseconnect.php';

// Vérification que l'utilisateur est un admin simple
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header("Location: login.php");
    exit();
}

$admin = $_SESSION['utilisateur'];

// Récupération des infos du compte pour statut
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$admin['id']]);
$utilisateur = $stmt->fetch();

if (!$utilisateur || $utilisateur['statut'] !== 'actif') {
    // Rediriger si l'admin est inactif
    header("Location: gestion_eleve_as.php?erreur=statut_inactif");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation et récupération des champs
    $nom        = htmlspecialchars($_POST['nom']);
    $postnom    = htmlspecialchars($_POST['postnom']);
    $prenom     = htmlspecialchars($_POST['prenom']);
    $email      = htmlspecialchars($_POST['email']);
    $telephone  = htmlspecialchars($_POST['telephone']);
    $adresse    = htmlspecialchars($_POST['adresse']);
    $naissance  = $_POST['naissance'];
    $sexe       = $_POST['sexe'];
    $etablissement   = htmlspecialchars($_POST['etablissement']);
    $section    = htmlspecialchars($_POST['section']);
    $adresse_ecole = htmlspecialchars($_POST['adresse_ecole']);
    $categorie  = htmlspecialchars($_POST['categorie']);
    $statut     = htmlspecialchars($_POST['statut']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $inscription_complete = isset($_POST['inscription_complete']) ? 1 : 0;

    try {
        // 1. Insertion dans `utilisateurs`
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, postnom, prenom, email, telephone, adresse, naissance, sexe, role, statut, mot_de_passe, inscription_complete)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'eleve', ?, ?, ?)");
        $stmt->execute([$nom, $postnom, $prenom, $email, $telephone, $adresse, $naissance, $sexe, $statut, $mot_de_passe, $inscription_complete]);

        // 2. Récupérer l’ID de l’élève
        $eleve_id = $pdo->lastInsertId();

        // 3. Insertion dans `eleves`
        $stmt = $pdo->prepare("INSERT INTO eleves (utilisateur_id, etablissement, section, adresse_ecole, categorie_activite)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$eleve_id, $etablissement, $section, $adresse_ecole, $categorie]);

        enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'un élève", "Élève : $nom $postnom $prenom");

        // Redirection
        header("Location: gestion_eleve_as.php?success=ok");
        exit();

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un élève</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
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
  <!-- Icône notification -->
  <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
      <i class="far fa-bell"></i>
      <span class="badge badge-warning navbar-badge"><?= $nbNotifNonLues ?? 0 ?></span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <span class="dropdown-header"><?= $nbNotifNonLues ?? 0 ?> Notifications</span>
      <div class="dropdown-divider"></div>

      <?php foreach ($notifications as $notif): ?>
        <a href="#" class="dropdown-item">
          <i class="fas fa-info-circle mr-2"></i> <?= htmlspecialchars($notif['titre']) ?>
          <span class="float-right text-muted text-sm"><?= htmlspecialchars($notif['date']) ?></span>
        </a>
        <div class="dropdown-divider"></div>
      <?php endforeach; ?>

      <a href="notifications.php" class="dropdown-item dropdown-footer">Voir toutes les notifications</a>
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
          <a href="#" class="d-block">Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?></a>
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
            <a href="question_admin.php" class="nav-link">
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
  <div class="content-wrapper p-4">
    <h2 class="mb-4"><i class="fas fa-user-plus"></i> Ajouter un élève</h2>

    <!-- Formulaire -->
    <form method="POST">
      <div class="card card-primary">
        <div class="card-body">
          <div class="row">
            <!-- Données personnelles -->
            <div class="form-group col-md-4">
              <label>Nom</label>
              <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="form-group col-md-4">
              <label>Postnom</label>
              <input type="text" name="postnom" class="form-control" required>
            </div>
            <div class="form-group col-md-4">
              <label>Prénom</label>
              <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Téléphone</label>
              <input type="text" name="telephone" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Adresse</label>
              <input type="text" name="adresse" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Date de naissance</label>
              <input type="date" name="naissance" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Sexe</label>
              <select name="sexe" class="form-control">
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Inscription Complète</label><br>
              <input type="checkbox" name="inscription_complete" value="1"> Oui
            </div>

            <!-- Données scolaires -->
            <div class="form-group col-md-6">
              <label>Établissement</label>
              <input type="text" name="etablissement" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Section</label>
              <input type="text" name="section" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Adresse de l'école</label>
              <input type="text" name="adresse_ecole" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Catégorie</label>
              <input type="text" name="categorie" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Statut</label>
              <input type="text" name="statut" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label for="mot_de_passe" class="form-label">Mot de passe</label>
              <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="6">
            </div>
          </div>
        </div>
        <div class="card-footer text-right">
          <a href="admin_eleve.php" class="btn btn-secondary">Annuler</a>
          <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </div>
    </form>
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
<script src="adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
