<?php 
// Routes DELETE - Suppression de données
// ======================================

// API Routes - Utilisateurs
\Router\Router::delete('/api/utilisateurs/[i:id]', function($id){
    (new \Controllers\UtilisateursController())->destroy($id);
});

\Router\Router::delete('/api/utilisateurs/[i:id]/soft', function($id){
    (new \Controllers\UtilisateursController())->softDelete($id);
});

// API Routes - Élèves
\Router\Router::delete('/api/eleves/[i:id]', function($id){
    (new \Controllers\EleveController())->destroy($id);
});

\Router\Router::delete('/api/eleves/[i:id]/soft', function($id){
    (new \Controllers\EleveController())->softDelete($id);
});

// API Routes - Interrogations
\Router\Router::delete('/api/interrogations/[i:id]', function($id){
    (new \Controllers\InterrogationController())->destroy($id);
});

\Router\Router::delete('/api/interrogations/[i:id]/questions', function($id){
    (new \Controllers\InterrogationController())->deleteQuestions($id);
});

// API Routes - Questions
\Router\Router::delete('/api/questions/[i:id]', function($id){
    (new \Controllers\QuestionController())->destroy($id);
});

\Router\Router::delete('/api/questions/[i:id]/reponses', function($id){
    (new \Controllers\QuestionController())->deleteReponses($id);
});

// API Routes - Résultats
\Router\Router::delete('/api/resultats/[i:id]', function($id){
    (new \Controllers\ResultatController())->destroy($id);
});

\Router\Router::delete('/api/resultats/eleve/[i:eleve_id]', function($eleve_id){
    (new \Controllers\ResultatController())->deleteByEleve($eleve_id);
});

// API Routes - Messages
\Router\Router::delete('/api/messages/[i:id]', function($id){
    (new \Controllers\MessageController())->destroy($id);
});

\Router\Router::delete('/api/messages/user/[i:user_id]', function($user_id){
    (new \Controllers\MessageController())->deleteByUser($user_id);
});

// API Routes - Notifications
\Router\Router::delete('/api/notifications/[i:id]', function($id){
    (new \Controllers\NotificationController())->destroy($id);
});

\Router\Router::delete('/api/notifications/user/[i:user_id]', function($user_id){
    (new \Controllers\NotificationController())->deleteByUser($user_id);
});

// API Routes - Sessions
\Router\Router::delete('/api/sessions/[i:id]', function($id){
    (new \Controllers\SessionController())->destroy($id);
});

\Router\Router::delete('/api/sessions/user/[i:user_id]', function($user_id){
    (new \Controllers\SessionController())->deleteByUser($user_id);
});

// API Routes - Fichiers
\Router\Router::delete('/api/files/[i:id]', function($id){
    (new \Controllers\FileController())->destroy($id);
});

\Router\Router::delete('/api/files/user/[i:user_id]', function($user_id){
    (new \Controllers\FileController())->deleteByUser($user_id);
});

// API Routes - Logs
\Router\Router::delete('/api/logs/[i:id]', function($id){
    (new \Controllers\LogController())->destroy($id);
});

\Router\Router::delete('/api/logs/old', function(){
    (new \Controllers\LogController())->deleteOldLogs();
});

// API Routes - Activités Admin
\Router\Router::delete('/api/admin/activites/[i:id]', function($id){
    (new \Controllers\AdminController())->deleteActivite($id);
});

\Router\Router::delete('/api/admin/activites/old', function(){
    (new \Controllers\AdminController())->deleteOldActivites();
});

// API Routes - Rapports
\Router\Router::delete('/api/rapports/[i:id]', function($id){
    (new \Controllers\RapportController())->destroy($id);
});

\Router\Router::delete('/api/rapports/old', function(){
    (new \Controllers\RapportController())->deleteOldRapports();
});

// API Routes - Cache
\Router\Router::delete('/api/cache', function(){
    (new \Controllers\CacheController())->clear();
});

\Router\Router::delete('/api/cache/[a:key]', function($key){
    (new \Controllers\CacheController())->delete($key);
});

// API Routes - Tokens
\Router\Router::delete('/api/tokens/[i:id]', function($id){
    (new \Controllers\TokenController())->destroy($id);
});

\Router\Router::delete('/api/tokens/expired', function(){
    (new \Controllers\TokenController())->deleteExpired();
});

// API Routes - Backup
\Router\Router::delete('/api/backup/[i:id]', function($id){
    (new \Controllers\BackupController())->destroy($id);
});

\Router\Router::delete('/api/backup/old', function(){
    (new \Controllers\BackupController())->deleteOldBackups();
});
?>