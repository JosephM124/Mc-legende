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
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
require_once 'databaseconnect.php'; // adapte selon ton chemin
$id = $_SESSION['utilisateur']['id'];
 // Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF pour modification
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['erreur'] = "Token CSRF invalide.";
        header('Location: gestion_notifications.php?modif=echec');
        exit;
    }
    unset($_SESSION['csrf_token']);
    $id = (int)($_POST['id'] ?? 0);
    $titre = htmlspecialchars(strip_tags(trim($_POST['titre'] ?? '')));
    $message = htmlspecialchars(strip_tags(trim($_POST['message'] ?? '')));
    $type = htmlspecialchars(strip_tags(trim($_POST['type'] ?? '')));
    if ($id && $titre && $message && $type) {
        $stmt = $pdo->prepare("UPDATE notifications SET titre = ?, message = ?, type = ? WHERE id = ?");
        $stmt->execute([$titre, $message, $type, $id]);
        header('Location: gestion_notifications.php?modif=ok');
        exit;
    } else {
        header('Location: gestion_notifications.php?modif=echec');
        exit;
    }
} else {
    // Génération du token CSRF pour le formulaire de modification (à mettre dans chaque modal)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupérer toutes les notifications
$notifications = $pdo->query("
    SELECT n.*, u.nom, u.prenom 
    FROM notifications n
    LEFT JOIN utilisateurs u ON n.utilisateur_id = u.id
    ORDER BY n.date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);


function couper_texte($texte, $longueur_max = 100) {
    if (strlen($texte) <= $longueur_max) {
        return $texte;
    }
    $texte_coupe = substr($texte, 0, $longueur_max);
    $dernier_espace = strrpos($texte_coupe, ' ');
    if ($dernier_espace !== false) {
        $texte_coupe = substr($texte_coupe, 0, $dernier_espace);
    }
    return $texte_coupe . '...';
}

function format_texte_role($role) {
    switch ($role) {
        case 'eleve': return 'Élèves';
        case 'admin_simple': return 'Administrateurs Simples';
        case 'admin_principal': return 'Admin principal';
        default: return ucfirst($role);
    }
}


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
          <a href="gestion_notifications.php" class="nav-link active ">
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
    <div class="container-fluid">
      <h1 class="mb-2">Gestion des notifications</h1>
    </div>
  </section>

  <section class="content">

  <?php if (isset($_GET['ajout']) && $_GET['ajout'] == 'ok'): ?>
    <div class="alert alert-success">Notification ajoutée avec succès. !
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

    <?php if (isset($_GET['ajout']) && $_GET['ajout'] == 'echec'): ?>
    <div class="alert alert-success">Echec lors de l'ajout. !
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

    <?php if (isset($_GET['success']) && $_GET['success'] == 'ok'): ?>
    <div class="alert alert-success">Notification supprimée avec succès. !
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
    <div class="alert alert-success"> La suppression de la notification a échoué. !
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

    <?php if (isset($_GET['modif']) && $_GET['modif'] == 'ok'): ?>
    <div class="alert alert-success">Notification midifiée avec succès. !
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
    <?php if (isset($_GET['modif']) && $_GET['modif'] == 'echec'): ?>
    <div class="alert alert-success"> La modification de la notification a échoué. !
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
    <div class="card">
      <div class="card-header bg-primary">
        <h3 class="card-title text-white">Notifications envoyées</h3>
        <div class="card-tools">
          <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalNotification">
  <i class="fas fa-plus"></i> Nouvelle notification
</button>

        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Message</th>
              <th>Date d'envoi</th>
              <th>Destinataires</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($notifications as $notif): ?>
              <tr>
                <td><?= htmlspecialchars($notif['titre']) ?></td>
                <td><?= nl2br(htmlspecialchars(couper_texte($notif['message'], 100))) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($notif['date_creation'])) ?></td>
                <td>
  <?php
    if (!empty($notif['utilisateur_id']) && !empty($notif['prenom'])) {
        echo '<span class="badge badge-primary">' . htmlspecialchars($notif['prenom'] . ' ' . $notif['nom']) . '</span>';
    } elseif (!empty($notif['role_destinataire']) && $notif['est_generale']) {
        echo '<span class="badge badge-success">Tous les ' . format_texte_role($notif['role_destinataire']) . '</span>';
    } elseif (!empty($notif['role_destinataire'])) {
        echo '<span class="badge badge-warning">' . format_texte_role($notif['role_destinataire']) . '</span>';
    } else {
        echo '<span class="badge badge-secondary">Non défini</span>';
    }
  ?>
</td>
<td>
  <?php if ($notif['lue']): ?>
    <span class="badge badge-success">Lue</span>
  <?php else: ?>
    <span class="badge badge-secondary">Non lue</span>
  <?php endif; ?>
</td>




                <td>
                  <div class="d-flex flex-row gap-1 justify-content-center align-items-center">
                    <button class="btn btn-info btn-sm" title="Voir" data-toggle="modal" data-target="#voirModal<?= $notif['id'] ?>">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" title="Modifier"
                      data-toggle="modal" data-target="#modalModifierNotification<?= $notif['id'] ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" title="Supprimer"
                      data-toggle="modal" data-target="#modalSupprimerNotification<?= $notif['id'] ?>">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- ✅ Ajoute le modal juste ici, dans la boucle -->
  <div class="modal fade" id="modalSupprimerNotification<?= $notif['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="supprimerNotifLabel<?= $notif['id'] ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form action="supprimer_notif.php" method="POST">
        <input type="hidden" name="id" value="<?= $notif['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="supprimerNotifLabel<?= $notif['id'] ?>">Confirmation de suppression</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Voulez-vous vraiment supprimer la notification <strong><?= htmlspecialchars($notif['titre']) ?></strong> ?
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">Oui, supprimer</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<!-- Modal de visualisation -->
  <div class="modal fade" id="voirModal<?= $notif['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="voirModalLabel<?= $notif['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="voirModalLabel<?= $notif['id'] ?>">Détails de la notification</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <h5><strong>Titre :</strong> <?= htmlspecialchars($notif['titre']) ?></h5>
          <p><strong>Message :</strong><br><?= nl2br(htmlspecialchars($notif['message'])) ?></p>
          <p><strong>Type :</strong> <?= ucfirst($notif['type']) ?></p>
          <p><strong>Catégorie :</strong> <?= htmlspecialchars($notif['categorie'] ?? 'Tous') ?></p>
          <p><strong>Destinataire :</strong> 
            <?php
              if (!empty($notif['utilisateur_id']) && !empty($notif['prenom'])) {
                  echo htmlspecialchars($notif['prenom'] . ' ' . $notif['nom']);
              } elseif (!empty($notif['role_destinataire'])) {
                  echo format_texte_role($notif['role_destinataire']);
              } else {
                  echo "Non défini";
              }
            ?>
          </p>
          <p><strong>Date d'envoi :</strong> <?= date('d/m/Y H:i', strtotime($notif['date_creation'])) ?></p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>
  
 <?php endforeach; ?>

           

            <?php if (empty($notifications)): ?>
              <tr><td colspan="5" class="text-center text-muted">Aucune notification trouvée.</td></tr>
            <?php endif; ?>
           

          </tbody>
        </table>
        <?php foreach ($notifications as $notif): ?>
<div class="modal fade" id="modalModifierNotification<?= $notif['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modifierLabel<?= $notif['id'] ?>" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="gestion_notifications.php" method="POST">
      <input type="hidden" name="id" value="<?= $notif['id'] ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="modifierLabel<?= $notif['id'] ?>">Modifier la notification</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($notif['titre']) ?>" required>
          </div>
          <div class="form-group">
            <label>Message</label>
            <textarea name="message" class="form-control" rows="4" required><?= htmlspecialchars($notif['message']) ?></textarea>
          </div>
          <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control" required>
              <option value="info" <?= $notif['type'] == 'info' ? 'selected' : '' ?>>Information</option>
              <option value="alerte" <?= $notif['type'] == 'alerte' ? 'selected' : '' ?>>Alerte</option>
              <option value="rappel" <?= $notif['type'] == 'rappel' ? 'selected' : '' ?>>Rappel</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Enregistrer</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

 
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


<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>

    

<script>
  $(document).ready(function () {
    $('.table').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
      }
    });
  });
</script>


    <!-- Modal : Ajouter une notification -->

<div class="modal fade" id="modalNotification" tabindex="-1" role="dialog" aria-labelledby="modalNotificationLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="ajout_notifications.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalNotificationLabel">Nouvelle notification</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
          </div>

          <div class="form-group">
         <label for="type">Type de notification</label>
          <select class="form-control" name="type" id="type" required>
          <option value="">-- Choisir le type --</option>
    <option value="information">quiz</option>
    <option value="alerte">resultats</option>
    <option value="evenement">systeme</option>
  </select>
</div>

<div class="form-group">
      <label for="categorie">Catégorie</label>
         <select class="form-control" name="categorie">
    <option value="musique">Musique</option>
    <option value="danse">Danse</option>
    <option value="culture générale">Culture générale</option>
    <option value="art">Art</option>
    <!-- Autres catégories -->
    </select>
    </div>


  <div class="form-group">
  <label for="cible_type">Envoyer à</label>
  <select class="form-control" id="cible_type" name="cible_type" required>
    <option value="">-- Choisir --</option>
    <option value="role">Un rôle (élèves ou admins simples)</option>
    <option value="eleve">Un élève spécifique</option>
  </select>
</div>

<!-- Choix du rôle -->
<div class="form-group" id="groupe_role" style="display:none;">
  <label for="role_destinataire">Destinataires (rôle)</label>
  <select class="form-control" name="role_destinataire" id="role_destinataire">
    <option value="eleve">Tous les élèves d'une catégorie</option>
    <option value="admin_simple">Tous les admins simples</option>
  </select>
</div>

        <!-- Choix d'un élève -->
<div class="form-group" id="groupe_eleve" style="display:none;">
  <label for="utilisateur_id">Élève cible</label>
  <select class="form-control" name="utilisateur_id" id="utilisateur_id">
    <option value="">-- Sélectionner un élève --</option>
    <?php
    $eleves = $pdo->query("SELECT id, nom, prenom FROM utilisateurs WHERE role = 'eleve'")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($eleves as $eleve) {
      echo '<option value="'.$eleve['id'].'">'.htmlspecialchars($eleve['prenom'].' '.$eleve['nom']).'</option>';
    }
    ?>
  </select>
</div>
        

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Envoyer</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
  $('#cible_type').on('change', function () {
    const val = $(this).val();
    $('#groupe_role, #groupe_eleve').hide();
    if (val === 'role') {
      $('#groupe_role').show();
    } else if (val === 'eleve') {
      $('#groupe_eleve').show();
    }
  });
</script>

          
        
  
</body>
</html>

