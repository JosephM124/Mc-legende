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
require_once 'fonctions.php'; // Pour filtrage et CSRF si besoin

require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: login.php');
    exit;
}
require_once 'log_admin.php';
$message_succes = null;

if (isset($_GET['publier']) && is_numeric($_GET['publier'])) {
    $quizId = (int) $_GET['publier'];

    // Vérifier si les résultats sont déjà publiés
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE quiz_id = ? AND statut = 1");
    $checkStmt->execute([$quizId]);
    $dejaPublie = $checkStmt->fetchColumn();

    if ($dejaPublie > 0) {
        // Déjà publié
        header("Location: resultats_admin.php?message=deja_publie");
        exit;
    }

    // 1. Publier les résultats
    $pdo->prepare("UPDATE resultats SET statut = 1 WHERE quiz_id = ?")->execute([$quizId]);

    // 2. Récupération du titre du quiz
    $quizStmt = $pdo->prepare("SELECT titre FROM quiz WHERE id = ?");
    $quizStmt->execute([$quizId]);
    $quiz = $quizStmt->fetch();

    // 3. Récupérer les élèves concernés
    $elevesStmt = $pdo->prepare("
        SELECT DISTINCT utilisateur_id 
        FROM resultats 
        WHERE quiz_id = ?
    ");
    $elevesStmt->execute([$quizId]);
    $eleves = $elevesStmt->fetchAll(PDO::FETCH_COLUMN);

    // 4. Créer une notification par élève
    $notifStmt = $pdo->prepare("
        INSERT INTO notifications 
        (utilisateur_id, quiz_id, type, titre, message, lien, lue, date_creation, role_destinataire, est_generale, categorie)
        VALUES 
        (:utilisateur_id, :quiz_id, :type, :titre, :message, :lien, 0, NOW(), 'eleve', 1, :categorie)
    ");

    foreach ($eleves as $eleve_id) {
        $notifStmt->execute([
            'utilisateur_id' => $eleve_id,
            'quiz_id'        => $quizId,
            'type'           => 'resultat',
            'titre'          => 'Résultats disponibles',
            'message'        => 'Les résultats de l’interrogation "' . htmlspecialchars($quiz['titre']) . '" sont maintenant disponibles.',
            'lien'           => 'resultats.php',
            'categorie'      => ''
        ]);
    }
    // Appel de la fonction pour journaliser l'action
    enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Publication des résultats", "Quiz ID: $quizId");


    // Redirection avec succès
    header("Location: resultats_admin.php?message=publie&quiz_id=" . $quizId);
    exit;
}

// Gérer les messages à afficher
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'publie') {
        $message_succes = "Les résultats ont été publiés avec succès.";
    } elseif ($_GET['message'] === 'deja_publie') {
        $message_succes = "⚠️ Les résultats sont déjà publiés.";
    }
}

$categorie_filtre = isset($_GET['categorie']) ? htmlspecialchars(strip_tags($_GET['categorie'])) : '';
$ville_filtre = isset($_GET['ville']) ? htmlspecialchars(strip_tags($_GET['ville'])) : '';
$recherche_nom = isset($_GET['nom']) ? trim(htmlspecialchars(strip_tags($_GET['nom']))) : '';

// Récupération des interrogations (quiz) selon le filtre catégorie et ville
if ($categorie_filtre) {
    $interrosStmt = $pdo->prepare("SELECT * FROM quiz WHERE date_lancement < NOW() AND categorie = ? ORDER BY date_lancement DESC");
    $interrosStmt->execute([$categorie_filtre]);
    $interros = $interrosStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $interros = $pdo->query("SELECT * FROM quiz WHERE date_lancement < NOW() ORDER BY date_lancement DESC")->fetchAll(PDO::FETCH_ASSOC);
}
// Si un filtre ville est appliqué, ne garder que les quiz ayant au moins un résultat pour cette ville (et la catégorie si présente)
if ($ville_filtre) {
    $quiz_ids = [];
    $sqlQuizVille = "SELECT DISTINCT r.quiz_id FROM resultats r JOIN eleves e ON r.utilisateur_id = e.utilisateur_id WHERE e.ville_province = ?";
    $paramsQuizVille = [$ville_filtre];
    if ($categorie_filtre) {
        $sqlQuizVille .= " AND r.quiz_id IN (SELECT id FROM quiz WHERE categorie = ?)";
        $paramsQuizVille[] = $categorie_filtre;
    }
    $stmtQuizVille = $pdo->prepare($sqlQuizVille);
    $stmtQuizVille->execute($paramsQuizVille);
    $quiz_ids = $stmtQuizVille->fetchAll(PDO::FETCH_COLUMN);
    $interros = array_filter($interros, function($quiz) use ($quiz_ids) {
        return in_array($quiz['id'], $quiz_ids);
    });
    // Réindexer le tableau pour éviter des soucis dans la boucle foreach
    $interros = array_values($interros);
}

$id = $_SESSION['utilisateur']['id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();
$categories = $pdo->query("SELECT DISTINCT categorie_activite FROM eleves")->fetchAll(PDO::FETCH_COLUMN);
$villes = $pdo->query("SELECT DISTINCT ville_province FROM eleves WHERE ville_province IS NOT NULL AND ville_province != '' ORDER BY ville_province")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats des interrogations</title>
  <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
  }
</script>

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
          <a href="interro_admin.php" class="nav-link ">
            <i class="nav-icon fas fa-book"></i>
            <p>Interrogations</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="resultats_admin.php" class="nav-link active">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Résultats</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="question_admin.php" class="nav-link">
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
    <section class="content-header">
      <h1 class="mb-4 text-primary font-weight-bold"><i class="fas fa-chart-bar mr-2"></i> Résultats des interrogations</h1>
    </section>
    <section class="content">
      <?php if ($message_succes): ?>
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($message_succes) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <form method="get" class="mb-4 p-3 rounded shadow-sm bg-light">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="font-weight-bold mb-1" for="categorie">Catégorie :</label>
          <select name="categorie" id="categorie" class="form-control form-control-sm">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>" <?= ($categorie_filtre === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="font-weight-bold mb-1" for="ville">Ville :</label>
          <select name="ville" id="ville" class="form-control form-control-sm">
            <option value="">Toutes les villes</option>
            <?php foreach ($villes as $ville): ?>
              <option value="<?= htmlspecialchars($ville) ?>" <?= ($ville_filtre === $ville) ? 'selected' : '' ?>><?= htmlspecialchars($ville) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="font-weight-bold mb-1" for="nom">Nom/Prénom :</label>
          <input type="text" name="nom" id="nom" class="form-control form-control-sm" value="<?= htmlspecialchars($recherche_nom) ?>" placeholder="Recherche...">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary btn-sm mr-2"><i class="fas fa-filter"></i> Filtrer</button>
          <a href="resultats_admin.php" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
        </div>
      </div>
    </form>

    <?php foreach ($interros as $interro):
      $query = "SELECT r.*, u.nom, u.prenom, e.ville_province, e.categorie_activite FROM resultats r
                JOIN utilisateurs u ON u.id = r.utilisateur_id
                JOIN eleves e ON u.id = e.utilisateur_id
                WHERE r.quiz_id = :quiz_id";
      $params = ['quiz_id' => $interro['id']];
      // On ne filtre plus sur e.categorie_activite, car la catégorie est déjà filtrée via la table quiz
      if ($ville_filtre) {
        $query .= " AND e.ville_province = :ville";
        $params['ville'] = $ville_filtre;
      }
      if ($recherche_nom) {
        $query .= " AND (u.nom LIKE :nom OR u.prenom LIKE :nom)";
        $params['nom'] = "%$recherche_nom%";
      }
      $query .= " ORDER BY r.date_passage DESC";
      $stmt = $pdo->prepare($query);
      $stmt->execute($params);
      $resultats = $stmt->fetchAll();
      $statutStmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE quiz_id = ? AND statut = 1");
      $statutStmt->execute([$interro['id']]);
      $deja_publie = $statutStmt->fetchColumn() > 0;
    ?>
    <div class="card mb-4 shadow-sm border-0">
      <div class="card-header bg-gradient-primary text-white d-flex flex-wrap justify-content-between align-items-center">
        <div>
          <h3 class="card-title mb-0 font-weight-bold"><i class="fas fa-book mr-2"></i><?= htmlspecialchars($interro['titre']) ?> <span class="badge badge-light ml-2"><?= date('d/m/Y H:i', strtotime($interro['date_lancement'])) ?></span></h3>
          <small class="text-light">Nombre d'élèves : <span class="badge badge-info"><?= count($resultats) ?></span></small>
        </div>
        <div class="mt-2 mt-md-0">
          <?php if (!$deja_publie): ?>
            <a href="resultats_admin.php?publier=<?= $interro['id'] ?>" class="btn btn-warning btn-sm mr-1" onclick="return confirm('Publier les résultats ?');">
              <i class="fas fa-upload"></i> Publier
            </a>
          <?php else: ?>
            <button class="btn btn-secondary btn-sm mr-1" disabled>
              <i class="fas fa-check-circle"></i> Déjà publié
            </button>
          <?php endif; ?>
          <a href="export_resultats.php?quiz_id=<?= $interro['id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Exporter</a>
        </div>
      </div>
      <div class="card-body bg-white p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped table-sm mb-0 table-results" id="table-results-<?= $interro['id'] ?>">
            <thead class="thead-light">
              <tr>
                <th class="text-center">N°</th>
                <th>Élève</th>
                <th>Catégorie</th>
                <th>Ville</th>
                <th>Score</th>
                <th>Date de passage</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($resultats as $index => $res): ?>
                <tr>
                  <td class="text-center align-middle"><?= $index + 1 ?></td>
                  <td class="align-middle"><i class="fas fa-user-graduate text-primary mr-1"></i> <?= htmlspecialchars($res['prenom'] . ' ' . $res['nom']) ?></td>
                  <td class="align-middle"><span class="badge badge-info"><?= htmlspecialchars($res['categorie_activite']) ?></span></td>
                  <td class="align-middle"><span class="badge badge-secondary"><?= htmlspecialchars($res['ville_province']) ?></span></td>
                  <td class="align-middle font-weight-bold text-success"><?= htmlspecialchars($res['score']) ?> / 10</td>
                  <td class="align-middle text-muted"><?= date('d/m/Y H:i', strtotime($res['date_passage'])) ?></td>
                  <td class="align-middle"><span class="badge badge-<?= $res['statut'] ? 'success' : 'secondary' ?>"><?= $res['statut'] ? 'Publié' : 'Privé' ?></span></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($resultats)): ?>
                <tr><td colspan="7" class="text-center text-muted">Aucun résultat pour ce filtre.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </section>
</div>

  <footer class="main-footer">
    <strong>&copy; 2025 MC-LEGENDE</strong>
  </footer>
</div>

<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $('table.table-results').each(function() {
      $(this).DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": false,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 10,
        "language": {
          url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
        }
      });
    });
  });
</script>
<style>
.bg-gradient-primary {
  background: linear-gradient(90deg, #007bff 0%, #0056b3 100%) !important;
}
.card-title {
  font-size: 1.2rem;
}
.table th, .table td {
  vertical-align: middle !important;
}
.badge-info {
  background-color: #17a2b8 !important;
}
.badge-secondary {
  background-color: #6c757d !important;
}
.badge-success {
  background-color: #28a745 !important;
}
</style>
</body>
</html>
