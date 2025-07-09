<?php


// Routes PUT - Mise à jour de données
// ===================================

// API Routes - Utilisateurs
\Router\Router::put('/api/utilisateurs/[i:id]', function($id){
    (new \Controllers\UtilisateursController())->update($id);
});

\Router\Router::put('/api/utilisateurs/[i:id]/password', function($id){
    (new \Controllers\UtilisateursController())->updatePassword($id);
});

\Router\Router::put('/api/utilisateurs/[i:id]/profile', function($id){
    (new \Controllers\UtilisateursController())->updateProfile($id);
});

\Router\Router::put('/api/utilisateurs/[i:id]/status', function($id){
    (new \Controllers\UtilisateursController())->updateStatus($id);
});

// API Routes - Élèves
\Router\Router::put('/api/eleves/[i:id]', function($id){
    (new \Controllers\EleveController())->update($id);
});

\Router\Router::put('/api/eleves/[i:id]/etablissement', function($id){
    (new \Controllers\EleveController())->updateEtablissement($id);
});

\Router\Router::put('/api/eleves/[i:id]/section', function($id){
    (new \Controllers\EleveController())->updateSection($id);
});

// API Routes - Interrogations
\Router\Router::put('/api/interrogations/[i:id]', function($id){
    (new \Controllers\InterrogationController())->update($id);
});

\Router\Router::put('/api/interrogations/[i:id]/status', function($id){
    (new \Controllers\InterrogationController())->updateStatus($id);
});

\Router\Router::put('/api/interrogations/[i:id]/questions', function($id){
    (new \Controllers\InterrogationController())->updateQuestions($id);
});

// API Routes - Questions
\Router\Router::put('/api/questions/[i:id]', function($id){
    (new \Controllers\QuestionController())->update($id);
});

\Router\Router::put('/api/questions/[i:id]/reponses', function($id){
    (new \Controllers\QuestionController())->updateReponses($id);
});

// API Routes - Résultats
\Router\Router::put('/api/resultats/[i:id]', function($id){
    (new \Controllers\ResultatController())->update($id);
});

\Router\Router::put('/api/resultats/[i:id]/score', function($id){
    (new \Controllers\ResultatController())->updateScore($id);
});

// API Routes - Configuration
\Router\Router::put('/api/config/[a:key]', function($key){
    (new \Controllers\ConfigController())->update($key);
});

\Router\Router::put('/api/config/system', function(){
    (new \Controllers\ConfigController())->updateSystem();
});

// API Routes - Notifications
\Router\Router::put('/api/notifications/[i:id]', function($id){
    (new \Controllers\NotificationController())->update($id);
});

\Router\Router::put('/api/notifications/[i:id]/read', function($id){
    (new \Controllers\NotificationController())->markAsRead($id);
});

// API Routes - Messages
\Router\Router::put('/api/messages/[i:id]', function($id){
    (new \Controllers\MessageController())->update($id);
});

\Router\Router::put('/api/messages/[i:id]/status', function($id){
    (new \Controllers\MessageController())->updateStatus($id);
});

// API Routes - Sessions
\Router\Router::put('/api/sessions/[i:id]', function($id){
    (new \Controllers\SessionController())->update($id);
});

\Router\Router::put('/api/sessions/[i:id]/extend', function($id){
    (new \Controllers\SessionController())->extendSession($id);
});

// API Routes - Profils
\Router\Router::put('/api/profils/[i:id]', function($id){
    (new \Controllers\ProfilController())->update($id);
});

\Router\Router::put('/api/profils/[i:id]/avatar', function($id){
    (new \Controllers\ProfilController())->updateAvatar($id);
});

// API Routes - Paramètres
\Router\Router::put('/api/parametres/[a:param]', function($param){
    (new \Controllers\ParametreController())->update($param);
});

\Router\Router::put('/api/parametres/user/[i:user_id]', function($user_id){
    (new \Controllers\ParametreController())->updateUserParams($user_id);
});

// API Routes - Préférences
\Router\Router::put('/api/preferences/[i:user_id]', function($user_id){
    (new \Controllers\PreferenceController())->update($user_id);
});

\Router\Router::put('/api/preferences/[i:user_id]/theme', function($user_id){
    (new \Controllers\PreferenceController())->updateTheme($user_id);
});

// API Routes - Permissions
\Router\Router::put('/api/permissions/[i:user_id]', function($user_id){
    (new \Controllers\PermissionController())->update($user_id);
});

\Router\Router::put('/api/permissions/[i:user_id]/role', function($user_id){
    (new \Controllers\PermissionController())->updateRole($user_id);
});
?>