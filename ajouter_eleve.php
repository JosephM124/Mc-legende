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
// Headers HTTP de sécurité (CSP adaptée)
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=()");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: connexion.php");
    exit();
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';

$id = $_SESSION['utilisateur']['id'];
 // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();


$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['erreur'] = "Token CSRF invalide.";
        header('Location: admin_eleve.php?ajout=echec');
        exit();
    }
    unset($_SESSION['csrf_token']);
    // Filtrage
    function filtrer_input($data) { return htmlspecialchars(strip_tags(trim($data))); }
    $nom = filtrer_input($_POST['nom'] ?? '');
    $postnom = filtrer_input($_POST['postnom'] ?? '');
    $prenom = filtrer_input($_POST['prenom'] ?? '');
    $email = filtrer_input($_POST['email'] ?? '');
    $mot_de_passe = filtrer_input($_POST['mot_de_passe'] ?? '');
    $telephone = filtrer_input($_POST['telephone'] ?? '');
    $adresse = filtrer_input($_POST['adresse'] ?? '');
    $naissance = filtrer_input($_POST['naissance'] ?? '');
    $sexe = filtrer_input($_POST['sexe'] ?? '');
    $role = 'eleve';
    $inscription_complete = isset($_POST['inscription_complete']) ? 1 : 0;
    $etablissement = filtrer_input($_POST['etablissement'] ?? '');
    $section = filtrer_input($_POST['section'] ?? '');
    $adresse_ecole = filtrer_input($_POST['adresse_ecole'] ?? '');
    $categorie = filtrer_input($_POST['categorie'] ?? '');
    $statut = filtrer_input($_POST['statut'] ?? '');
    // Hachage du mot de passe
    $mdp_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    // Insertion utilisateur
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, postnom, prenom, email, telephone, adresse, naissance, sexe, role, mot_de_passe, inscription_complete,statut)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->execute([$nom, $postnom, $prenom, $email, $telephone, $adresse, $naissance, $sexe, $role, $mdp_hash, $inscription_complete,$statut]);
    $utilisateur_id = $pdo->lastInsertId();
    // Insertion infos scolaires
    $stmt2 = $pdo->prepare("INSERT INTO eleves (utilisateur_id, etablissement, section, adresse_ecole, categorie_activite)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt2->execute([$utilisateur_id, $etablissement, $section, $adresse_ecole, $categorie ]);
    enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'un élève", "Élève : $nom $postnom $prenom");
    header("Location: admin_eleve.php?ajout=ok");
    exit();
} else {
    // Génération du token CSRF pour le formulaire (à mettre dans le form HTML)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
      <h1 class="m-3">Ajouter un élève</h1>
    </div>
    <section class="content">
      <div class="container-fluid">

        
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
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
              <a href="gestion_eleves.php" class="btn btn-secondary">Annuler</a>
              <button type="submit" class="btn btn-primary">Ajouter</button>
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
<script src="adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
