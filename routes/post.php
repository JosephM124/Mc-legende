<?php
// Routes POST - Création de données
// =================================

// Authentification
\Router\Router::post('/login', function(){
    (new \Controllers\PageController())->login();
});

\Router\Router::post('/register', function(){
    (new \Controllers\PageController())->register();
});

// API Routes - Utilisateurs
\Router\Router::post('/api/utilisateurs', function(){
    (new \Controllers\UtilisateursController())->store();
});

\Router\Router::post('/api/utilisateurs/login', function(){
    (new \Controllers\UtilisateursController())->login($_POST);
});

\Router\Router::post('/api/utilisateurs/reset-password', function(){
    (new \Controllers\UtilisateursController())->resetPassword();
});

\Router\Router::post('/api/utilisateurs/verify-email', function(){
    (new \Controllers\UtilisateursController())->verifyEmail();
});

// API Routes - Élèves
\Router\Router::post('/api/eleves', function(){
    (new \Controllers\EleveController())->store();
});

\Router\Router::post('/api/eleves/import', function(){
    (new \Controllers\EleveController())->importFromExcel();
});

// API Routes - Interrogations
\Router\Router::post('/api/interrogations', function(){
    (new \Controllers\InterrogationController())->store();
});

\Router\Router::post('/api/interrogations/[i:id]/questions', function($id){
    (new \Controllers\InterrogationController())->addQuestions($id);
});

\Router\Router::post('/api/interrogations/[i:id]/start', function($id){
    (new \Controllers\InterrogationController())->startInterrogation($id);
});

\Router\Router::post('/api/interrogations/[i:id]/stop', function($id){
    (new \Controllers\InterrogationController())->stopInterrogation($id);
});

// API Routes - Résultats
\Router\Router::post('/api/resultats', function(){
    (new \Controllers\ResultatController())->store();
});

\Router\Router::post('/api/resultats/submit', function(){
    (new \Controllers\ResultatController())->submitResult();
});

// API Routes - Questions
\Router\Router::post('/api/questions', function(){
    (new \Controllers\QuestionController())->store();
});

\Router\Router::post('/api/questions/import', function(){
    (new \Controllers\QuestionController())->importQuestions();
});

// API Routes - Réponses
\Router\Router::post('/api/reponses', function(){
    (new \Controllers\ReponseController())->store();
});

\Router\Router::post('/api/reponses/validate', function(){
    (new \Controllers\ReponseController())->validateReponse();
});

// API Routes - Fichiers
\Router\Router::post('/api/upload/avatar', function(){
    (new \Controllers\FileController())->uploadAvatar();
});

\Router\Router::post('/api/upload/document', function(){
    (new \Controllers\FileController())->uploadDocument();
});

\Router\Router::post('/api/upload/image', function(){
    (new \Controllers\FileController())->uploadImage();
});

// API Routes - Notifications
\Router\Router::post('/api/notifications', function(){
    (new \Controllers\NotificationController())->store();
});

\Router\Router::post('/api/notifications/send', function(){
    (new \Controllers\NotificationController())->sendNotification();
});

// API Routes - Messages
\Router\Router::post('/api/messages', function(){
    (new \Controllers\MessageController())->store();
});

\Router\Router::post('/api/messages/send', function(){
    (new \Controllers\MessageController())->sendMessage();
});

// API Routes - Rapports
\Router\Router::post('/api/rapports', function(){
    (new \Controllers\RapportController())->store();
});

\Router\Router::post('/api/rapports/generate', function(){
    (new \Controllers\RapportController())->generateRapport();
});

// API Routes - Configuration
\Router\Router::post('/api/config', function(){
    (new \Controllers\ConfigController())->store();
});

\Router\Router::post('/api/config/backup', function(){
    (new \Controllers\ConfigController())->createBackup();
});

// API Routes - Logs
\Router\Router::post('/api/logs', function(){
    (new \Controllers\LogController())->store();
});

// API Routes - Sessions
\Router\Router::post('/api/sessions/start', function(){
    (new \Controllers\SessionController())->startSession();
});

\Router\Router::post('/api/sessions/validate', function(){
    (new \Controllers\SessionController())->validateSession();
});

// API Routes - Validation
\Router\Router::post('/api/validate/email', function(){
    (new \Controllers\ValidationController())->validateEmail();
});

\Router\Router::post('/api/validate/phone', function(){
    (new \Controllers\ValidationController())->validatePhone();
});

// API Routes - Import/Export
\Router\Router::post('/api/import/eleves', function(){
    (new \Controllers\ImportController())->importEleves();
});

\Router\Router::post('/api/import/questions', function(){
    (new \Controllers\ImportController())->importQuestions();
});

\Router\Router::post('/api/import/resultats', function(){
    (new \Controllers\ImportController())->importResultats();
});
?>