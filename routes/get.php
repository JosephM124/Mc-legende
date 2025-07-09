<?php
// harry's ajoute juste les routes en fonction des fichiers 
// ne les supprimes pas encore et laisse moi recuperer les vues de son projet 
// pour la logique on garde sa logique aussi on ne supprim rien on va se basser sur ca pour faire 
// nos controllers , nos middlewares et les models
// je n ai pas encore fini ca et ca me soule donc si tu as le temps 
// rajoutes justes les attribut en fonction de chaque model 
// et n oublie de prendre chaque class avec le namespace 

// Routes GET - Pages et récupération de données
// ==============================================

// Routes principales
\Router\Router::get('/', function(){
    (new \Controllers\PageController())->index();
  });

\Router\Router::get('/connexion', function(){
    (new \Controllers\PageController())->connexion();
  });

\Router\Router::get('/inscription', function(){
    (new \Controllers\PageController())->inscription();
  });

\Router\Router::get('/logout', function(){
     (new \Controllers\UtilisateursController())->logout();
  });

// Routes Élève
\Router\Router::get('/eleve/home', function(){
    (new \Controllers\PageController())->eleve_home();
  });

\Router\Router::get('/eleve/interro', function(){
        (new \Controllers\PageController())->eleve_interro();
  });

\Router\Router::get('/eleve/resultats', function(){
   (new \Controllers\PageController())->eleve_resultat();
  });

\Router\Router::get('/eleve/profil', function(){
      (new \Controllers\PageController())->eleve_profil();
  });
  
// Routes Admin
  \Router\Router::get('/admin/home', function(){
    (new \Controllers\PageController())->admin_home();
  });

\Router\Router::get('/admin/profil', function(){
    (new \Controllers\PageController())->admin_profil();
});

// API Routes - Utilisateurs
\Router\Router::get('/api/utilisateurs', function(){
    (new \Controllers\UtilisateursController())->index();
});

\Router\Router::get('/api/utilisateurs/[i:id]', function($id){
    (new \Controllers\UtilisateursController())->show($id);
});

\Router\Router::get('/api/utilisateurs/role/[a:role]', function($role){
    (new \Controllers\UtilisateursController())->getByRole($role);
});

\Router\Router::get('/api/utilisateurs/email/[a:email]', function($email){
    (new \Controllers\UtilisateursController())->getByEmail($email);
});

// API Routes - Élèves
\Router\Router::get('/api/eleves', function(){
    (new \Controllers\EleveController())->index();
});

\Router\Router::get('/api/eleves/[i:id]', function($id){
    (new \Controllers\EleveController())->show($id);
});

\Router\Router::get('/api/eleves/etablissement/[a:etablissement]', function($etablissement){
    (new \Controllers\EleveController())->getByEtablissement($etablissement);
});

\Router\Router::get('/api/eleves/section/[a:section]', function($section){
    (new \Controllers\EleveController())->getBySection($section);
});

\Router\Router::get('/api/eleves/utilisateur/[i:utilisateur_id]', function($utilisateur_id){
    (new \Controllers\EleveController())->getByUtilisateurId($utilisateur_id);
});

// API Routes - Admin
\Router\Router::get('/api/admin/activites', function(){
    (new \Controllers\AdminController())->getActivites();
});

\Router\Router::get('/api/admin/activites/[i:admin_id]', function($admin_id){
    (new \Controllers\AdminController())->getActivitesByAdmin($admin_id);
});

\Router\Router::get('/api/admin/statistiques', function(){
    (new \Controllers\AdminController())->getStatistiques();
});

// API Routes - Interrogations
\Router\Router::get('/api/interrogations', function(){
    (new \Controllers\InterrogationController())->index();
});

\Router\Router::get('/api/interrogations/[i:id]', function($id){
    (new \Controllers\InterrogationController())->show($id);
});

\Router\Router::get('/api/interrogations/eleve/[i:eleve_id]', function($eleve_id){
    (new \Controllers\InterrogationController())->getByEleve($eleve_id);
});

\Router\Router::get('/api/interrogations/active', function(){
    (new \Controllers\InterrogationController())->getActive();
});

// API Routes - Résultats
\Router\Router::get('/api/resultats', function(){
    (new \Controllers\ResultatController())->index();
});

\Router\Router::get('/api/resultats/[i:id]', function($id){
    (new \Controllers\ResultatController())->show($id);
});

\Router\Router::get('/api/resultats/eleve/[i:eleve_id]', function($eleve_id){
    (new \Controllers\ResultatController())->getByEleve($eleve_id);
});

\Router\Router::get('/api/resultats/interrogation/[i:interrogation_id]', function($interrogation_id){
    (new \Controllers\ResultatController())->getByInterrogation($interrogation_id);
});

// Routes de recherche
\Router\Router::get('/api/search/utilisateurs', function(){
    (new \Controllers\SearchController())->searchUtilisateurs();
});

\Router\Router::get('/api/search/eleves', function(){
    (new \Controllers\SearchController())->searchEleves();
});

// Routes de statistiques
\Router\Router::get('/api/stats/globales', function(){
    (new \Controllers\StatsController())->getGlobales();
});

\Router\Router::get('/api/stats/eleves', function(){
    (new \Controllers\StatsController())->getStatsEleves();
});

\Router\Router::get('/api/stats/interrogations', function(){
    (new \Controllers\StatsController())->getStatsInterrogations();
});

// Routes d'export
\Router\Router::get('/api/export/eleves', function(){
    (new \Controllers\ExportController())->exportEleves();
});

\Router\Router::get('/api/export/resultats', function(){
    (new \Controllers\ExportController())->exportResultats();
});

\Router\Router::get('/api/export/activites', function(){
    (new \Controllers\ExportController())->exportActivites();
});

// API Route - Dashboard stats (tout-en-un)
\Router\Router::get('/api/admin/dashboard_stats', function(){
    (new \Controllers\AdminController())->getDashboardStats();
});

// API Route - Dashboard stats optimisées
\Router\Router::get('/api/stats/dashboard', function(){
    (new \Controllers\StatsController())->getDashboardStats();
});
?>