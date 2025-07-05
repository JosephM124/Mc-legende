<?php
// Sécurisation avancée de la session et headers HTTP
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
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
require_once 'fonctions.php'; // Pour CSRF et filtrage

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: connexion.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Génération du token CSRF pour la suppression de toutes les questions
if (!isset($_SESSION['csrf_token_supp_toutes'])) {
    $_SESSION['csrf_token_supp_toutes'] = generer_csrf_token('suppression_toutes_questions');
}
$csrf_token_supp_toutes = $_SESSION['csrf_token_supp_toutes'];

// Génération du token CSRF pour l'import Excel
if (!isset($_SESSION['csrf_token_import'])) {
    $_SESSION['csrf_token_import'] = generer_csrf_token('import_questions_excel');
}
$csrf_token_import = $_SESSION['csrf_token_import'];

// Génération des tokens CSRF pour chaque question (modif/supp) une seule fois par session
if (!isset($_SESSION['csrf_token_question_modif'])) {
    $_SESSION['csrf_token_question_modif'] = [];
}
if (!isset($_SESSION['csrf_token_question_supp'])) {
    $_SESSION['csrf_token_question_supp'] = [];
}

require_once 'databaseconnect.php'; // ta connexion BDD
require_once 'log_admin.php';

// Ton code ici...

$message = '';
$id = $_SESSION['utilisateur']['id'];
 // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();


// Modification de question
if (isset($_POST['update_question'])) {
    if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'], 'modification_question_' . intval($_POST['question_id']))) {
        header('Location: question_admin.php?modif=echec');
        exit();
    }
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

    enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Modification d'une question", "Détails : $texte,  $cat");
    supprimer_csrf_token($_POST['csrf_token'], 'modification_question_' . $id_q);
    // Suppression du token de session après usage
    unset($_SESSION['csrf_token_question_modif'][$id_q]);
    header("Location: question_admin.php?modif=ok"); // rafraîchir pour éviter le re-post
    exit();
}

if (isset($_POST['supprimer_toutes'])) {
    if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'], 'suppression_toutes_questions')) {
        header('Location: question_admin.php?success=echec');
        exit();
    }
    // Supprimer d'abord les liaisons dans reponses
    $stmt = $pdo->prepare("DELETE FROM reponses");
    $stmt->execute();
    // Puis supprimer les liaisons dans interrogation_questions
    $stmt = $pdo->prepare("DELETE FROM interrogation_questions");
    $stmt->execute();
    // Puis supprimer les liaisons dans quiz_questions
    $stmt = $pdo->prepare("DELETE FROM quiz_questions");
    $stmt->execute();
    // Puis supprimer toutes les questions
    $stmt = $pdo->prepare("DELETE FROM questions");
    $stmt->execute();

    // 3. Enregistrer l'action dans l'historique
    enregistrer_activite_admin(
        $_SESSION['utilisateur']['id'],
        "Suppression de toutes les questions ",
        " "
    );
    supprimer_csrf_token($_POST['csrf_token'], 'suppression_toutes_questions');
    // Suppression du token de session après usage
    unset($_SESSION['csrf_token_supp_toutes']);
    header("Location: question_admin.php?success=ok");
    exit();
}




// Filtrage de la catégorie reçue en GET
$categorie = isset($_GET['categorie']) ? htmlspecialchars(strip_tags($_GET['categorie'])) : '';

if ($categorie != '') {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE categorie = ?");
    $stmt->execute([$categorie]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM questions");
    $stmt->execute();
}
$questions = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Questions</title>
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
          <a href="question_admin.php" class="nav-link active">
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

    

    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalImport">+ Importer des questions</button>
 <div class="table-responsive">
    <table class="table table-bordered" id="questionsTable">
      <thead>
        <tr>
          <th>Question</th>
          <th>Catégorie</th>
          <th>Réponses</th>
          <th>Bonne réponse</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($questions as $q): ?>
    <?php
      // Générer une seule fois par session les tokens pour chaque question
      if (!isset($_SESSION['csrf_token_question_supp'][$q['id']])) {
        $_SESSION['csrf_token_question_supp'][$q['id']] = generer_csrf_token('suppression_question_' . $q['id']);
      }
      $csrf_token_supp = $_SESSION['csrf_token_question_supp'][$q['id']];
      if (!isset($_SESSION['csrf_token_question_modif'][$q['id']])) {
        $_SESSION['csrf_token_question_modif'][$q['id']] = generer_csrf_token('modification_question_' . $q['id']);
      }
      $csrf_token_modif = $_SESSION['csrf_token_question_modif'][$q['id']];
    ?>
    <tr>
      <td><?= htmlspecialchars($q['texte_question']) ?></td>
      <td><?= htmlspecialchars($q['categorie']) ?></td>
      <td>
        <ol type="A">
          <li><?= htmlspecialchars($q['option_1']) ?></li>
          <li><?= htmlspecialchars($q['option_2']) ?></li>
          <li><?= htmlspecialchars($q['option_3']) ?></li>
          <li><?= htmlspecialchars($q['option_4']) ?></li>
        </ol>
      </td>
      <td><?= htmlspecialchars($q['bonne_reponse']) ?></td>
      <td>
        <div class="d-flex flex-row gap-1 justify-content-center align-items-center">
          <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?= $q['id'] ?>" title="Modifier">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalSupprimer<?= $q['id'] ?>" title="Supprimer">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </td>
      <!-- Modal de modification pour la question ID <?= $q['id'] ?> -->
      <div class="modal fade" id="modalEdit<?= $q['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel<?= $q['id'] ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <form method="POST">
            <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token_modif ?>">
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
          <form method="POST" action="supprimer_question.php">
            <input type="hidden" name="supprimer_id" value="<?= $q['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token_supp ?>">
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
    <!-- Bouton pour ouvrir la modale -->
<button type="button" class="btn btn-danger mb-3" data-toggle="modal" data-target="#modalSuppression">
  🗑 Supprimer toutes les questions
</button>

  

  </div>
  
  

  <!-- Modal Import Excel -->
  <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" enctype="multipart/form-data" action="importer_question.php">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token_import ?>">
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
    <div class="float-right d-none d-sm-inline">Admin Principal</div>
    <strong>&copy; 2025 MC-LEGENDE</strong>. Tous droits réservés.
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
<!-- Modal de confirmation -->
<!-- Modal de confirmation -->
<div class="modal fade" id="modalSuppression" tabindex="-1" role="dialog" aria-labelledby="modalSuppressionLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token_supp_toutes ?>">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalSuppressionLabel">Confirmation de suppression</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ⚠️ Êtes-vous sûr de vouloir <strong>supprimer toutes les questions</strong> ? Cette action est irréversible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          <button type="submit" name="supprimer_toutes" class="btn btn-danger">Oui, supprimer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.table {
  background-color: #fff;
}
.table thead th {
  background-color: #f8f9fa;
  color: #343a40;
  font-weight: bold;
  text-align: center;
}
.table-bordered td, .table-bordered th {
  border: 1px solid #dee2e6;
}
.table tbody td {
  vertical-align: middle;
  text-align: center;
}
.gap-1 > * + * {
  margin-left: 0.25rem;
}
</style>

</body>
</html>
