<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

$id = $_SESSION['utilisateur']['id'];


// Traitement de la mise à jour
$erreurs = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $postnom = trim($_POST['postnom']);
    $adresse = trim($_POST['adresse']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $date_naissance = $_POST['date_naissance'];
    $sexe = $_POST['sexe'] ?? '';
    $ville = trim($_POST['ville'] ?? '');
    $pays = trim($_POST['pays'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Champs liés à la table eleves
    $ecole = trim($_POST['ecole']);
    $adresse_ecole = trim($_POST['adresse_ecole']);
    $section = trim($_POST['section']);

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide.";
    }
    if (!in_array($sexe, ['Homme', 'Femme', 'Autre'])) {
        $erreurs[] = "Sexe invalide.";
    }
    if (!empty($password) || !empty($password_confirm)) {
        if ($password !== $password_confirm) {
            $erreurs[] = "Les mots de passe ne correspondent pas.";
        } elseif (strlen($password) < 6) {
            $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }
    }

    // Gestion de la photo
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoPath = 'uploads/avatars/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    if (empty($erreurs)) {
        // Mise à jour table `utilisateurs`
        $sqlUser = "UPDATE utilisateurs SET nom=?, prenom=?, postnom=?, adresse=?, email=?, telephone=?, naissance=?, sexe=?";
        $paramsUser = [$nom, $prenom, $postnom, $adresse, $email, $telephone, $date_naissance, $sexe];
        if ($photoPath) {
            $sqlUser .= ", photo=?";
            $paramsUser[] = $photoPath;
        }
        if (!empty($password)) {
            $sqlUser .= ", mot_de_passe=?";
            $paramsUser[] = password_hash($password, PASSWORD_DEFAULT);
        }
        $sqlUser .= " WHERE id=?";
        $paramsUser[] = $id;

        $stmtUser = $pdo->prepare($sqlUser);
        $successUser = $stmtUser->execute($paramsUser);

        // Mise à jour table `eleves` (ajout ville_province et pays)
        $sqlEleve = "UPDATE eleves SET etablissement=?, adresse_ecole=?, section=?, ville_province=?, pays=? WHERE utilisateur_id=?";
        $stmtEleve = $pdo->prepare($sqlEleve);
        $successEleve = $stmtEleve->execute([$ecole, $adresse_ecole, $section, $ville, $pays, $id]);

        $success = $successUser && $successEleve;

        if ($success) {
            $_SESSION['utilisateur']['email'] = $email;
        }
    }
}

// Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();
$stmt = $pdo->prepare("SELECT * FROM eleves WHERE utilisateur_id = ?");
$stmt->execute([$id]);
$eleve = $stmt->fetch();
if (!$eleve) {
    $eleve = [
        'ecole' => 'non défini',
        'adresse_ecole' => 'non défini',
        'section' => 'non défini'
    ];
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

$photo_profil = !empty($utilisateur['photo']) ? $utilisateur['photo'] : 'uploads/avatars/default.jpeg';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon Profil - MC-LEGENDE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Barre de navigation -->
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

        <a href="notifications" class="dropdown-item dropdown-footer text-primary">
          Voir toutes les notifications
        </a>
      </div>
    </li>

    <!-- Déconnexion -->
    <li class="nav-item">
      <a class="nav-link" href="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
            <a href="home" class="nav-link ">
              <i class="nav-icon fas fa-home"></i>
              <p>Accueil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="interro" class="nav-link">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Mes Interros</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="resultats" class="nav-link ">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Mes Résultats</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profil" class="nav-link active">
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
  <div class="row justify-content-center">
    <div class="col-md-10">

      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <i class="fas fa-check-circle"></i> Profil mis à jour avec succès !
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php elseif (!empty($erreurs)): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach ($erreurs as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user-edit"></i> Modifier mon profil</h3>
        </div>

        <form method="POST" enctype="multipart/form-data">
          <div class="card-body row">

            <div class="col-md-4 text-center mb-3">
              <img id="photoPreview" src="<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="img-thumbnail rounded-circle" style="width:150px;height:150px;">
              <div class="mt-2">
                <label class="form-label">Changer la photo</label>
                <input type="file" name="photo" class="form-control" id="photoInput">
                <img id="newPhotoPreview" src="#" alt="Aperçu" style="display:none;margin-top:10px;width:120px;height:120px;object-fit:cover;border-radius:50%;border:1px solid #ccc;" />
              </div>
            </div>

            <div class="col-md-8 row">
              <div class="form-group col-md-4">
                <label>Nom</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" class="form-control" required>
              </div>

              <div class="form-group col-md-4">
                <label>Post-nom</label>
                <input type="text" name="postnom" value="<?= htmlspecialchars($utilisateur['postnom']) ?>" class="form-control">
              </div>

              <div class="form-group col-md-4">
                <label>Prénom</label>
                <input type="text" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom']) ?>" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                <label>Adresse</label>
                <input type="text" name="adresse" value="<?= htmlspecialchars($utilisateur['adresse']) ?>" class="form-control">
              </div>

              <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                <label>Téléphone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($utilisateur['telephone']) ?>" class="form-control">
              </div>

              <div class="form-group col-md-6">
                <label>Date de naissance</label>
                <input type="date" name="date_naissance" value="<?= htmlspecialchars($utilisateur['naissance']) ?>" class="form-control">
              </div>

              <div class="form-group col-md-4">
                <label>Sexe</label>
                <select name="sexe" class="form-control" required>
                  <option value="">-- Sélectionner --</option>
                  <option value="Homme" <?= ($utilisateur['sexe'] === 'Homme') ? 'selected' : '' ?>>Homme</option>
                  <option value="Femme" <?= ($utilisateur['sexe'] === 'Femme') ? 'selected' : '' ?>>Femme</option>
                  <option value="Autre" <?= ($utilisateur['sexe'] === 'Autre') ? 'selected' : '' ?>>Autre</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label>Ville</label>
                <input type="text" name="ville" value="<?= htmlspecialchars($eleve['ville_province']) ?>" class="form-control" required>
              </div>
              <div class="form-group col-md-4">
                <label>Pays</label>
                <input type="text" name="pays" value="<?= htmlspecialchars($eleve['pays']) ?>" class="form-control" required>
              </div>

              <div class="form-group col-md-6">
                <label>Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
              </div>
              <div class="form-group col-md-6">
                <label>Confirmation du mot de passe</label>
                <input type="password" name="password_confirm" class="form-control" autocomplete="new-password">
              </div>

              <div class="form-group col-md-6">
                <label>Nom de l'école</label>
                <input type="text" name="ecole" value="<?= htmlspecialchars($eleve['etablissement']) ?>" class="form-control">
              </div>

              <div class="form-group col-md-6">
                <label>Adresse de l'école</label>
                <input type="text" name="adresse_ecole" value="<?= htmlspecialchars($eleve['adresse_ecole']) ?>" class="form-control">
              </div>

              <div class="form-group col-md-6">
                <label>Section / Option</label>
                <input type="text" name="section" value="<?= htmlspecialchars($eleve['section']) ?>" class="form-control">
              </div>
            </div>

          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Pour l'excellence pédagogique</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
  </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
  document.getElementById('photoInput').addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
      const preview = document.getElementById('newPhotoPreview');
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    }
  });
</script>
</body>
</html>
