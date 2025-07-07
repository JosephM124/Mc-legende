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

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
require_once 'databaseconnect.php'; // adapte selon ton chemin
require_once 'log_admin.php';
require_once 'fonctions.php'; // Pour la gestion CSRF
$id = $_SESSION['utilisateur']['id'];
 // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();

try {
    $pdo->exec("UPDATE quiz SET statut = 'prévu' WHERE date_lancement > NOW()");
} catch (PDOException $e) {
    echo "Erreur 1 : " . $e->getMessage();
}

// 1. Sélectionner les quiz qui doivent devenir actifs (et qui ne le sont pas encore)
try {
    $stmt = $pdo->prepare("
        SELECT * FROM quiz
        WHERE statut != 'actif'
          AND NOW() >= date_lancement 
          AND NOW() < DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE)
    ");
    $stmt->execute();
    $quizzesActifs = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur 2a : " . $e->getMessage();
}

// 2. Pour chaque quiz, activer et créer une notification
foreach ($quizzesActifs as $quiz) {
    try {
        // Activer l'interrogation
        $update = $pdo->prepare("UPDATE quiz SET statut = 'actif' WHERE id = ?");
        $update->execute([$quiz['id']]);

        // Préparer les infos de notification
        $titreNotif = "Nouvelle interrogation disponible";
        $message = "Une nouvelle interrogation est disponible dans la catégorie : " . htmlspecialchars($quiz['categorie']);
        $type = "quiz";
        $lien = "mes_interro.php";
        $categorie = $quiz['categorie'];
        $general = 1;
        // Récupérer les élèves de la même catégorie que l'interrogation
        $eleves = $pdo->prepare("
            SELECT u.id AS utilisateur_id, u.role as role, e.categorie_activite as categorie
            FROM utilisateurs u
            INNER JOIN eleves e ON u.id = e.utilisateur_id
            WHERE u.role = 'eleve' AND e.categorie_activite = ?
        ");
        $eleves->execute([$categorie]);
        $liste_eleves = $eleves->fetchAll();

        // Insérer une notification pour chaque élève individuellement
        $insertNotif = $pdo->prepare("
            INSERT INTO notifications (utilisateur_id, titre, message, type, lien,role_destinataire,est_generale,categorie, date_creation)
            VALUES (?, ?, ?, ?, ?,?,?,?, NOW())
        ");

        foreach ($liste_eleves as $eleve) {
            $insertNotif->execute([
                $eleve['utilisateur_id'],
                $titreNotif,
                $message,
                $type,
                $lien,
                $eleve['role'],
                $general,
                $eleve['categorie']
            ]);
        }

    } catch (PDOException $e) {
        echo "Erreur lors de l'activation ou notification : " . $e->getMessage();
    }
}

// 3. Désactiver les interrogations terminées
try {
    $pdo->prepare("
        UPDATE quiz 
        SET statut = 'inactif'
        WHERE statut != 'inactif' 
          AND NOW() >= DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE)
    ")->execute();
} catch (PDOException $e) {
    echo "Erreur 3 : " . $e->getMessage();
}


// Récupérer les interrogations APRÈS mise à jour
$where = [];
$params = [];

if (!empty($_GET['categorie'])) {
    $where[] = "categorie = ?";
    $params[] = $_GET['categorie'];
}

if (!empty($_GET['statut'])) {
    $where[] = "statut = ?";
    $params[] = $_GET['statut'];
}

$sql = "SELECT * FROM quiz";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY date_lancement DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$interrogations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des élèves - Admin</title>
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
          <a href="interro_admin.php" class="nav-link active">
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


<!-- Contenu principal -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Gestion des Interrogations</h1>
  </section>

  <section class="content">
    <div class="card">
        
    <div class="container-fluid">
    <?php if (isset($_GET['ajout']) && $_GET['ajout'] == 'ok'): ?>
    <div class="alert alert-success">interrogation ajoutée avec succès. !
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>    
     <script>
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('ajout');
      window.history.replaceState({}, document.title, url.toString());
    }
  </script>   
    <?php endif; ?>

    <?php if (isset($_GET['modif']) && $_GET['modif'] == 'ok'): ?>
    <div class="alert alert-success">interrogation modifiée avec succès. !
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

    <?php if (isset($_GET['success']) && $_GET['success'] == 'suppression'): ?>
    <div class="alert alert-success">interrogation supprimée avec succès. !
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
      <div class="card-header">
        <button class="btn btn-success" data-toggle="modal" data-target="#modalAjout">+ Ajouter une interrogation</button>
      </div>
      <div class="card-body">
  <form method="get" class="form-inline mb-3">
  <div class="form-group mr-2">
    <label for="filtre_categorie" class="mr-2">Catégorie :</label>
    <select name="categorie" id="filtre_categorie" class="form-control">
      <option value="">Toutes</option>
      <option value="culture générale" <?= ($_GET['categorie'] ?? '') == 'culture générale' ? 'selected' : '' ?>>Culture générale</option>
      <option value="musique" <?= ($_GET['categorie'] ?? '') == 'musique' ? 'selected' : '' ?>>Musique</option>
      <option value="danse" <?= ($_GET['categorie'] ?? '') == 'danse' ? 'selected' : '' ?>>Danse</option>
      <option value="art" <?= ($_GET['categorie'] ?? '') == 'art' ? 'selected' : '' ?>>Art</option>
    </select>
  </div>

  <div class="form-group mr-2">
    <label for="filtre_statut" class="mr-2">Statut :</label>
    <select name="statut" id="filtre_statut" class="form-control">
      <option value="">Tous</option>
      <option value="actif" <?= ($_GET['statut'] ?? '') == 'actif' ? 'selected' : '' ?>>Actif</option>
      <option value="inactif" <?= ($_GET['statut'] ?? '') == 'inactif' ? 'selected' : '' ?>>Inactif</option>
      <option value="prévu" <?= ($_GET['statut'] ?? '') == 'prévu' ? 'selected' : '' ?>>Prévu</option>
    </select>
  </div>

  <button type="submit" class="btn btn-primary">Filtrer</button>
</form>
<div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Catégorie</th>
              <th>Date lancement</th>
              <th>Durée totale (min)</th>
              <th>Durée par question</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($interrogations as $interro): ?>
  <?php $csrf_token = generer_csrf_token('suppression_interro_' . $interro['id']); ?>
  <tr>
    <td><?= htmlspecialchars($interro['titre']) ?></td>
    <td><?= htmlspecialchars($interro['categorie']) ?></td>
    <td><?= $interro['date_lancement'] ?></td>
    <td><?= $interro['duree_totale'] ?></td>
    <td><?= $interro['temps_par_question'] ?></td>
    <td>
      <?php
      $statut = $interro['statut'];
      $badgeClass = 'secondary';
      if ($statut === 'actif') $badgeClass = 'success';
      elseif ($statut === 'inactif') $badgeClass = 'danger';
      elseif ($statut === 'prévu') $badgeClass = 'warning';
      ?>
      <span class="badge badge-<?= $badgeClass ?>"><?= ucfirst($statut) ?></span>
    </td>
    <td>
      <div class="d-flex flex-row gap-1 justify-content-center align-items-center">
        <button class="btn btn-warning btn-sm btn-edit mr-1" 
          data-id="<?= $interro['id'] ?>"
          data-nom="<?= htmlspecialchars($interro['titre']) ?>"
          data-categorie="<?= htmlspecialchars($interro['categorie']) ?>"
          data-date="<?= $interro['date_lancement'] ?>"
          data-duree-total="<?= $interro['duree_totale'] ?>"
          data-duree-question="<?= $interro['temps_par_question'] ?>"
          data-toggle="modal" 
          data-target="#modalModifier"
          title="Modifier">
          <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-danger btn-sm btn-delete" 
          data-id="<?= $interro['id'] ?>"
          data-nom="<?= htmlspecialchars($interro['titre']) ?>"
          data-csrf="<?= $csrf_token ?>"
          data-toggle="modal" 
          data-target="#modalSupprimer"
          title="Supprimer">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </td>
  </tr>
<?php endforeach ?>
          </tbody>
        </table>
      </div>
      </div>
    </div>
  </section>
  <!-- Modal Ajout Interrogation -->
<div class="modal fade" id="modalAjout" tabindex="-1" role="dialog" aria-labelledby="modalAjoutLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <?php $csrf_token_ajout = generer_csrf_token('ajout_interro'); ?>
<form method="POST" action="ajouter_interros.php">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token_ajout ?>">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalAjoutLabel">Ajouter une interrogation</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nom de l'interrogation</label>
            <input type="text" class="form-control" name="nom" required>
          </div>
          <div class="form-group">
            <label>Catégorie</label>
            <select class="form-control" name="categorie" required>
              <option value="culture générale">Culture générale</option>
              <option value="musique">Musique</option>
              <option value="danse">Danse</option>
              <option value="art">Art</option>
              <!-- Ajoute plus selon tes catégories -->
            </select>
          </div>
          <div class="form-group">
            <label>Date et heure de lancement</label>
            <input type="datetime-local" class="form-control" name="date_lancement" required>
          </div>
          <div class="form-group">
            <label>Durée totale (en minutes)</label>
            <input type="number" class="form-control" name="duree_total" required>
          </div>
          <div class="form-group">
            <label>Durée par interrogation (en minutes)</label>
            <input type="number" class="form-control" name="duree_par_question" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Ajouter</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        </div>
      </div>
    </form>
  </div>
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
    
$(document).ready(function () {
  // Préremplir le formulaire de modification
  $('.btn-edit').click(function () {
    $('#mod-id').val($(this).data('id'));
    $('#mod-nom').val($(this).data('nom'));
    $('#mod-categorie').val($(this).data('categorie'));
    $('#mod-date').val($(this).data('date'));
    $('#mod-duree-total').val($(this).data('duree-total'));
    $('#mod-duree-question').val($(this).data('duree-question'));
  });

  // Préremplir le formulaire de suppression
  $('.btn-delete').click(function () {
    $('#sup-id').val($(this).data('id'));
    $('#sup-nom').text($(this).data('nom'));
    $('#sup-csrf').val($(this).data('csrf'));
  });
});



</script>


<!-- MODAL DE MODIFICATION -->
<div class="modal fade" id="modalModifier" tabindex="-1" role="dialog" aria-labelledby="modalModifierLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="modifier_interros.php" method="post" class="modal-content" value="">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Modifier l’interrogation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="mod-id">
        <div class="form-group">
          <label for="mod-nom">Nom de l’interrogation</label>
          <input type="text" name="nom" id="mod-nom" class="form-control"   required>
        </div>
        <div class="form-group">
          <label for="mod-categorie">Catégorie</label>
          <input type="text" name="categorie" id="mod-categorie" class="form-control"  required>
        </div>
        <div class="form-group">
          <label for="mod-date">Date de lancement</label>
          <input type="datetime-local" name="date_lancement" id="mod-date" class="form-control"  required>
        </div>
        <div class="form-group">
          <label for="mod-duree-total">Durée totale (en minutes)</label>
          <input type="number" name="duree_total" id="mod-duree-total" class="form-control"  required>
        </div>
        <div class="form-group">
          <label for="mod-duree-question">Durée par interrogation (en minutes)</label>
          <input type="number" name="duree_par_question" id="mod-duree-question" class="form-control"  required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
      </div>
    </form>
  </div>
</div>
<!-- MODAL DE SUPPRESSION -->
<div class="modal fade" id="modalSupprimer" tabindex="-1" role="dialog" aria-labelledby="modalSupprimerLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="supprimer_interros.php" method="post" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Supprimer l’interrogation</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="sup-id">
        <input type="hidden" name="csrf_token" id="sup-csrf">
        <p>Êtes-vous sûr de vouloir supprimer l’interrogation : <strong id="sup-nom"></strong> ?</p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script>
  $(document).ready(function () {
    $('.table').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
      }
    });
  });
</script>


  
</body>
</html>

