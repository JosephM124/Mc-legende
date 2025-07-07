<?php
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

// Gestion du POST (avant toute redéfinition de $page)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $reponse = $_POST['reponses'] ?? '';
    $_SESSION['reponses'][$page - 1] = $reponse;

    // Stocker la page courante en session
    $_SESSION['quiz_page'] = $page;

    if (isset($_POST['next'])) {
        $_SESSION['quiz_page'] = $page + 1;
        header("Location: faire_quiz.php"); // plus de ?page=...
        exit();
    } elseif (isset($_POST['prev'])) {
        $_SESSION['quiz_page'] = $page - 1;
        header("Location: faire_quiz.php");
        exit();
    } elseif (isset($_POST['terminer'])) {
        header("Location: terminer_quiz.php");
        exit();
    }
}

// Vérification des données de session essentielles
if (!isset($_SESSION['quiz_id']) || !isset($_SESSION['questions']) || !is_array($_SESSION['questions']) || count($_SESSION['questions']) === 0) {
    header("Location: mes_interro.php");
    exit();
}

if (!isset($_SESSION['interrogation_en_cours']) || $_SESSION['interrogation_en_cours'] !== true) {
    header("Location: triche_detectee.php");
    exit();
}

// Vérifie si l'interrogation a déjà été enregistrée dans la table interrogation_utilisateur
$id_utilisateur = $_SESSION['utilisateur']['id'];
$id_interrogation = $_SESSION['quiz_id'];

$verif = $pdo->prepare("SELECT COUNT(*) FROM interrogation_utilisateur WHERE utilisateur_id = ? AND quiz_id = ?");
$verif->execute([$id_utilisateur, $id_interrogation]);
$existe = $verif->fetchColumn();

if ($existe == 0) {
    $insert = $pdo->prepare("INSERT INTO interrogation_utilisateur (utilisateur_id, quiz_id, etat, debut) VALUES (?, ?, 'en_cours', NOW())");
    $insert->execute([$id_utilisateur, $id_interrogation]);
}

$start_time = $_SESSION['quiz_start_time'];
$quiz_duree = $_SESSION['quiz_duree'];
$temps_restant = ($start_time + $quiz_duree) - time();

if ($temps_restant <= 0) {
    header("Location: terminer_quiz.php");
    exit();
}

// Récupération des questions
$questions = $_SESSION['questions'];
$total = count($questions);
// Nouvelle gestion de la page courante
if (isset($_SESSION['quiz_page'])) {
    $page = (int)$_SESSION['quiz_page'];
} else {
    $page = 1;
    $_SESSION['quiz_page'] = 1;
}
if ($page < 1 || $page > $total) {
    $page = 1;
    $_SESSION['quiz_page'] = 1;
}

$current_question = $questions[$page - 1];

// Récupération des infos utilisateur
$req = $pdo->prepare("SELECT nom, prenom, photo FROM utilisateurs WHERE id = ?");
$req->execute([$id_utilisateur]);
$utilisateur = $req->fetch();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <script>
    let tempsRestant = <?= $temps_restant ?>;
    function formatTime(secs) {
      let h = Math.floor(secs / 3600);
      let m = Math.floor((secs % 3600) / 60);
      let s = secs % 60;
      return [h, m, s].map(t => t.toString().padStart(2, '0')).join(':');
    }
    function decompte() {
      document.getElementById('chrono').innerText = formatTime(tempsRestant);
      if (tempsRestant-- <= 0) {
        clearInterval(timer);
        alert("Temps écoulé ! Le quiz va être soumis.");
        document.getElementById("quizForm").submit();
      }
    }
    const timer = setInterval(decompte, 1000);


  </script>

  
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4cc9f0;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background-color: #f5f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .quiz-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 2rem;
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .timer-container {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1rem;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }
    
    .timer {
      font-size: 1.5rem;
      font-weight: bold;
      font-family: 'Courier New', monospace;
      background: rgba(0, 0, 0, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 5px;
    }
    
    .progress-container {
      height: 10px;
      background: #e9ecef;
      border-radius: 5px;
      margin: 1rem 0;
      overflow: hidden;
    }
    
    .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
      transition: width 0.3s ease;
    }
    
    .question-card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
    }
    
    .question-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: 10px 10px 0 0 !important;
      padding: 1.25rem;
      font-size: 1.1rem;
    }
    
    .question-body {
      padding: 1.5rem;
    }
    
    .form-check {
      padding: 0.75rem 1.25rem;
      margin: 0.5rem 0;
      border-radius: 8px;
      transition: all 0.2s;
      border: 1px solid #e9ecef;
    }
    
    .form-check:hover {
      background-color: #f8f9fa;
      border-color: var(--accent-color);
    }
    
    .form-check-input {
      width: 1.2em;
      height: 1.2em;
      margin-top: 0.2em;
    }
    
    .form-check-label {
      margin-left: 0.5rem;
      font-size: 1rem;
    }
    
    .btn-action {
      padding: 0.6rem 1.5rem;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.2s;
    }
    
    .btn-prev {
      background-color: #6c757d;
      color: white;
    }
    
    .btn-next {
      background-color: var(--primary-color);
      color: white;
    }
    
    .btn-finish {
      background-color: #28a745;
      color: white;
    }
    
    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .question-counter {
      font-size: 1rem;
      color: #6c757d;
      font-weight: 500;
    }
    
    .option-letter {
      display: inline-block;
      width: 25px;
      height: 25px;
      line-height: 25px;
      text-align: center;
      background-color: var(--primary-color);
      color: white;
      border-radius: 50%;
      margin-right: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">

  

  

  
    <div class="container py-5">
    <div class="quiz-container">
      <!-- Timer et progression -->
      <div class="timer-container">
        <div>
          <i class="fas fa-clock me-2"></i>
          <span class="question-counter bg-white">Question <?= $page ?> sur <?= $total ?></span>
        </div>
        <div class="timer" id="chrono">00:00:00</div>
      </div>
      
      <div class="progress-container">
        <div class="progress-bar" style="width: <?= ($page / $total) * 100 ?>%"></div>
      </div>
      
      <!-- Formulaire de question -->
      <form method="POST" id="quizForm">
        <input type="hidden" name="page" value="<?= $page ?>">
        
        <div class="card question-card">
          <div class="card-header question-header">
            <?= htmlspecialchars($current_question['texte_question']) ?>
          </div>
          <div class="card-body question-body">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <?php
              $lettre = chr(64 + $i);
              $opt = $current_question['option_' . $i];
              ?>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="reponses" value="<?= $lettre ?>" id="choix<?= $i ?>"
                       <?= (isset($_SESSION['reponses'][$page - 1]) && $_SESSION['reponses'][$page - 1] === $lettre) ? 'checked' : '' ?>>
                <label class="form-check-label" for="choix<?= $i ?>">
                  <span class="option-letter"><?= $lettre ?></span>
                  <?= htmlspecialchars($opt) ?>
                </label>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Boutons de navigation -->
        <div class="d-flex justify-content-between mt-4">
          <div></div> <!-- Espace vide pour garder l'alignement -->
          <?php if ($page < $total): ?>
            <button name="next" class="btn btn-action btn-next">
              Suivant<i class="fas fa-arrow-right ms-2"></i>
            </button>
          <?php else: ?>
            <button name="terminer" class="btn btn-action btn-finish">
              <i class="fas fa-check-circle me-2"></i>Terminer
            </button>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  </div>

  


<script>
document.addEventListener('DOMContentLoaded', function () {
  let tempsRestant = <?= $temps_restant ?>;
  let submitClicked = false;
  let tricheDetected = false;
  let navigationManuelle = false;

  // 🛡 Marquer la triche en AJAX silencieux
  function marquerTriche() {
    if (window.tricheEnvoyee) return;
    window.tricheEnvoyee = true;
    navigator.sendBeacon("triche_detectee.php");
  }

  // 🕒 Formatage du temps
  function formatTime(secs) {
    let h = Math.floor(secs / 3600);
    let m = Math.floor((secs % 3600) / 60);
    let s = secs % 60;
    return [h, m, s].map(t => t.toString().padStart(2, '0')).join(':');
  }

  // 🔁 Mise à jour du chronomètre
  function updateTimer() {
    const timerElement = document.getElementById('chrono');
    if (!timerElement) return;
    timerElement.innerText = formatTime(tempsRestant);

    if (tempsRestant <= 300) {
      timerElement.style.color = '#ff6b6b';
      timerElement.style.animation = 'pulse 1s infinite';
    }

    if (tempsRestant-- <= 0) {
      clearInterval(timer);
      submitClicked = true;
      document.getElementById("quizForm").submit();
    }
  }

  const timer = setInterval(updateTimer, 1000);
  updateTimer();

  // ✅ Highlight de la réponse cochée
  document.querySelectorAll('.form-check-input').forEach(input => {
    input.addEventListener('change', function () {
      const label = this.closest('.form-check');
      if (this.checked) {
        document.querySelectorAll('.form-check').forEach(el => {
          el.style.borderColor = '#e9ecef';
          el.style.boxShadow = 'none';
        });
        label.style.borderColor = '#4361ee';
        label.style.boxShadow = '0 0 0 2px rgba(67, 97, 238, 0.2)';
      }
    });

    if (input.checked) {
      const label = input.closest('.form-check');
      label.style.borderColor = '#4361ee';
      label.style.boxShadow = '0 0 0 2px rgba(67, 97, 238, 0.2)';
    }
  });

  // ✅ Soumission : pas de triche
  document.getElementById("quizForm").addEventListener("submit", function () {
    navigationManuelle = true;
    submitClicked = true;
  });

  // 🚫 Changement d'onglet interdit
  document.addEventListener("visibilitychange", function () {
    if (document.visibilityState !== 'visible' && !tricheDetected && !navigationManuelle) {
      tricheDetected = true;
      marquerTriche();
      alert("⚠️ Vous avez quitté la page. L'interrogation est annulée.");
      window.location.href = "triche_detectee.php";
    }
  });

  // 🚫 Navigation arrière ou fermeture d'onglet
  window.addEventListener("beforeunload", function (e) {
    if (!submitClicked && !tricheDetected) {
      marquerTriche();
      e.preventDefault();
      e.returnValue = "⚠️ Êtes-vous sûr de vouloir quitter ou revenir en arrière ? Cela annulera votre interrogation.";
      return "⚠️ Êtes-vous sûr de vouloir quitter ou revenir en arrière ? Cela annulera votre interrogation.";
    }
  });

  // 🚫 Bouton retour navigateur (popstate)
  let retourBloque = false;
  window.addEventListener('popstate', function (e) {
    if (!submitClicked && !tricheDetected) {
      if (!retourBloque) {
        retourBloque = true;
        if (confirm("⚠️ Si vous revenez en arrière, votre interrogation sera annulée. Continuer ?")) {
          marquerTriche();
          window.location.href = "triche_detectee.php";
        } else {
          history.pushState(null, '', window.location.href);
          retourBloque = false;
        }
      } else {
        retourBloque = false;
      }
    }
  });
  // Empêcher le retour arrière immédiat
  history.pushState(null, '', window.location.href);

  // 🔒 Wake Lock (éviter veille écran)
  let wakeLock = null;
  async function activerWakeLock() {
    try {
      if ('wakeLock' in navigator) {
        wakeLock = await navigator.wakeLock.request('screen');
        console.log("🔒 Wake Lock activé");
      }
    } catch (err) {
      console.warn("Erreur Wake Lock :", err.message);
    }
  }
  activerWakeLock();

  // Nettoyer l'URL pour masquer les paramètres (pas de ?page=...)
  if (window.location.search.length > 0) {
    history.replaceState(null, '', window.location.pathname);
  }
});
</script>
  <script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>

</body>
</html>
