<?php 
   namespace Controllers;

   class PageController{

      public function index()
      {
           $this->view('home', 'default');
      }

      public function connexion()
      {
           $this->view('connexion', 'auth');
      }
      
      public function inscription()
      {
           $this->view('inscription', 'auth');
      }
      
      public function login(){
         $this->render('login');
      }

      public function register(){
         $this->render('register');
      }

      public function admin_home(){
         $this->view('admin_principal', 'admin');
      }
      public function admin_profil(){
         
      }

      public function eleve_home(){
         $this->view('eleve', 'eleve');
      }

      public function eleve_interro(){
         $this->view('mes_interro', 'eleve');
      }

      public function eleve_profil(){
         $this->view('profil', 'eleve');
      }

      public function eleve_resultat(){
         $this->view('resultats', 'eleve');
      }
      private function provider($provide = []){}

      private function view($view, $pageType = 'default')
      {
         // Initialiser le helper d'assets avec le type de page
         if (class_exists('\Helpers\AssetsHelper')) {
            \Helpers\AssetsHelper::init();
         }
         require  dirname(__DIR__) . DIRECTORY_SEPARATOR .'views' . DIRECTORY_SEPARATOR . $view . '.php';
      }

      private function render($render)
      {
         require dirname(__DIR__) . DIRECTORY_SEPARATOR .'api' . DIRECTORY_SEPARATOR .'auth' . DIRECTORY_SEPARATOR. $render . '.php';
      }

   }
 
?>