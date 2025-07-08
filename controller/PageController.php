<?php 
   namespace Controllers;

   class PageController{

      public function index()
      {
           $this->view('home');
      }

      public function connexion()
      {
           $this->view('connexion');
      }
      
      public function inscription()
      {
           $this->view('inscription');
      }
      
      public function login(){
         $this->render('login');
      }

      public function register(){
         $this->render('register');
      }

      public function admin_home(){
         $this->view('admin_principal');
      }

      public function eleve_home(){
         $this->view('eleve');
      }

      public function eleve_interro(){
         $this->view('mes_interro');
      }

      public function eleve_profil(){
         $this->view('profil');
      }

      public function eleve_resultat(){
         $this->view('resultats');
      }
      private function provider($provide = []){}

      private function view($view)
      {
         require  dirname(__DIR__) . DIRECTORY_SEPARATOR .'views' . DIRECTORY_SEPARATOR . $view . '.php';
      }

      private function render($render)
      {
         require dirname(__DIR__) . DIRECTORY_SEPARATOR .'api' . DIRECTORY_SEPARATOR .'auth' . DIRECTORY_SEPARATOR. $render . '.php';
      }

   }
 
?>