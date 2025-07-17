<?php
/**
 * Script de test pour les fonctionnalités de sécurité
 * À exécuter pour vérifier que toutes les améliorations fonctionnent
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Middleware\SessionManager;
use Middleware\AuthMiddleware;
use Middleware\ValidationMiddleware;
use Config\SecurityConfig;

class SecurityTest
{
    private $sessionManager;
    private $authMiddleware;
    private $validationMiddleware;
    private $database;

    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        $this->authMiddleware = new AuthMiddleware();
        $this->validationMiddleware = new ValidationMiddleware();
        $this->database = \App\App::getMysqlDatabaseInstance();
    }

    /**
     * Exécuter tous les tests
     */
    public function runAllTests()
    {
        echo "🔐 Tests de Sécurité MC-LEGENDE\n";
        echo "================================\n\n";

        $tests = [
            'testSessionManager' => 'Test du gestionnaire de sessions',
            'testAuthMiddleware' => 'Test du middleware d\'authentification',
            'testValidationMiddleware' => 'Test du middleware de validation',
            'testSecurityConfig' => 'Test de la configuration de sécurité',
            'testDatabaseSecurity' => 'Test de la sécurité de la base de données',
            'testPasswordSecurity' => 'Test de la sécurité des mots de passe',
            'testCSRFProtection' => 'Test de la protection CSRF'
        ];

        $passed = 0;
        $failed = 0;

        foreach ($tests as $method => $description) {
            echo "🧪 $description...\n";
            
            try {
                $result = $this->$method();
                if ($result) {
                    echo "✅ Succès\n";
                    $passed++;
                } else {
                    echo "❌ Échec\n";
                    $failed++;
                }
            } catch (Exception $e) {
                echo "❌ Erreur: " . $e->getMessage() . "\n";
                $failed++;
            }
            
            echo "\n";
        }

        echo "📊 Résultats:\n";
        echo "Tests réussis: $passed\n";
        echo "Tests échoués: $failed\n";
        echo "Total: " . ($passed + $failed) . "\n\n";

        if ($failed === 0) {
            echo "🎉 Tous les tests de sécurité sont passés avec succès!\n";
        } else {
            echo "⚠️  Certains tests ont échoué. Vérifiez la configuration.\n";
        }
    }

    /**
     * Test du gestionnaire de sessions
     */
    private function testSessionManager()
    {
        // Test 1: Création de session
        $user = [
            'id' => 1,
            'email' => 'test@example.com',
            'role' => 'admin',
            'nom' => 'Test User',
            'mot_de_passe' => 'hashed_password'
        ];

        $result = $this->sessionManager->createUserSession($user);
        if (!$result) {
            throw new Exception("Échec de création de session");
        }

        // Test 2: Vérification d'authentification
        if (!$this->sessionManager->isAuthenticated()) {
            throw new Exception("Session non authentifiée après création");
        }

        // Test 3: Récupération des données utilisateur
        $currentUser = $this->sessionManager->getCurrentUser();
        if (!$currentUser || $currentUser['id'] != 1) {
            throw new Exception("Données utilisateur incorrectes");
        }

        // Test 4: Vérification des rôles
        if (!$this->sessionManager->hasRole('admin')) {
            throw new Exception("Vérification de rôle échouée");
        }

        // Test 5: Génération de token CSRF
        $csrfToken = $this->sessionManager->generateCSRFToken();
        if (empty($csrfToken)) {
            throw new Exception("Génération de token CSRF échouée");
        }

        // Test 6: Vérification de token CSRF
        if (!$this->sessionManager->verifyCSRFToken($csrfToken)) {
            throw new Exception("Vérification de token CSRF échouée");
        }

        // Test 7: Destruction de session
        $this->sessionManager->destroySession();
        if ($this->sessionManager->isAuthenticated()) {
            throw new Exception("Session toujours active après destruction");
        }

        return true;
    }

    /**
     * Test du middleware d'authentification
     */
    private function testAuthMiddleware()
    {
        // Test 1: Vérification de la structure de la classe
        if (!method_exists($this->authMiddleware, 'authenticate')) {
            throw new Exception("Méthode authenticate manquante");
        }

        if (!method_exists($this->authMiddleware, 'requireRole')) {
            throw new Exception("Méthode requireRole manquante");
        }

        if (!method_exists($this->authMiddleware, 'requireCSRFToken')) {
            throw new Exception("Méthode requireCSRFToken manquante");
        }

        return true;
    }

    /**
     * Test du middleware de validation
     */
    private function testValidationMiddleware()
    {
        // Test 1: Validation d'email
        $data = ['email' => 'test@example.com'];
        $rules = ['email' => 'required|email'];
        
        if (!$this->validationMiddleware->validate($data, $rules)) {
            throw new Exception("Validation d'email échouée");
        }

        // Test 2: Validation d'email invalide
        $data = ['email' => 'invalid-email'];
        if ($this->validationMiddleware->validate($data, $rules)) {
            throw new Exception("Validation d'email invalide acceptée");
        }

        // Test 3: Validation de mot de passe
        $data = ['password' => 'SecurePass123!'];
        $rules = ['password' => 'required|password'];
        
        if (!$this->validationMiddleware->validate($data, $rules)) {
            throw new Exception("Validation de mot de passe échouée");
        }

        // Test 4: Validation de mot de passe faible
        $data = ['password' => 'weak'];
        if ($this->validationMiddleware->validate($data, $rules)) {
            throw new Exception("Mot de passe faible accepté");
        }

        // Test 5: Nettoyage des données
        $dirtyData = ['name' => '<script>alert("xss")</script>'];
        $cleanData = $this->validationMiddleware->sanitize($dirtyData);
        
        if (strpos($cleanData['name'], '<script>') !== false) {
            throw new Exception("Nettoyage des données échoué");
        }

        return true;
    }

    /**
     * Test de la configuration de sécurité
     */
    private function testSecurityConfig()
    {
        // Test 1: Vérification des constantes
        if (!defined('Config\SecurityConfig::SESSION_TIMEOUT')) {
            throw new Exception("Constante SESSION_TIMEOUT manquante");
        }

        if (!defined('Config\SecurityConfig::MAX_LOGIN_ATTEMPTS')) {
            throw new Exception("Constante MAX_LOGIN_ATTEMPTS manquante");
        }

        // Test 2: Vérification des en-têtes de sécurité
        $headers = SecurityConfig::SECURITY_HEADERS;
        if (empty($headers)) {
            throw new Exception("En-têtes de sécurité manquants");
        }

        // Test 3: Vérification des rôles
        $roles = SecurityConfig::ROLES;
        if (empty($roles) || !isset($roles['admin'])) {
            throw new Exception("Configuration des rôles manquante");
        }

        // Test 4: Test de vérification d'IP
        $result = SecurityConfig::isIPAllowed('127.0.0.1');
        if ($result === null) {
            throw new Exception("Méthode isIPAllowed échouée");
        }

        // Test 5: Test de vérification de domaine
        $result = SecurityConfig::isDomainAllowed('localhost');
        if (!$result) {
            throw new Exception("Vérification de domaine échouée");
        }

        return true;
    }

    /**
     * Test de la sécurité de la base de données
     */
    private function testDatabaseSecurity()
    {
        // Test 1: Vérification de la table sessions
        $result = $this->database->select("SHOW TABLES LIKE 'sessions'");
        if (empty($result)) {
            throw new Exception("Table sessions manquante");
        }

        // Test 2: Vérification de la table logs
        $result = $this->database->select("SHOW TABLES LIKE 'logs'");
        if (empty($result)) {
            throw new Exception("Table logs manquante");
        }

        // Test 3: Vérification de la table login_attempts
        $result = $this->database->select("SHOW TABLES LIKE 'login_attempts'");
        if (empty($result)) {
            throw new Exception("Table login_attempts manquante");
        }

        // Test 4: Vérification des colonnes de sécurité dans utilisateurs
        $result = $this->database->select("SHOW COLUMNS FROM utilisateurs LIKE 'failed_login_attempts'");
        if (empty($result)) {
            throw new Exception("Colonne failed_login_attempts manquante");
        }

        // Test 5: Vérification des index de sécurité
        $result = $this->database->select("SHOW INDEX FROM utilisateurs WHERE Key_name = 'idx_email'");
        if (empty($result)) {
            throw new Exception("Index idx_email manquant");
        }

        return true;
    }

    /**
     * Test de la sécurité des mots de passe
     */
    private function testPasswordSecurity()
    {
        // Test 1: Hachage de mot de passe
        $password = 'SecurePass123!';
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        if (empty($hashed)) {
            throw new Exception("Hachage de mot de passe échoué");
        }

        // Test 2: Vérification de mot de passe
        if (!password_verify($password, $hashed)) {
            throw new Exception("Vérification de mot de passe échouée");
        }

        // Test 3: Vérification de mot de passe incorrect
        if (password_verify('wrongpassword', $hashed)) {
            throw new Exception("Vérification de mot de passe incorrect acceptée");
        }

        // Test 4: Test de complexité du mot de passe
        $validator = new ValidationMiddleware();
        $data = ['password' => 'SecurePass123!'];
        $rules = ['password' => 'password'];
        
        if (!$validator->validate($data, $rules)) {
            throw new Exception("Validation de complexité de mot de passe échouée");
        }

        return true;
    }

    /**
     * Test de la protection CSRF
     */
    private function testCSRFProtection()
    {
        // Test 1: Génération de token
        $token1 = $this->sessionManager->generateCSRFToken();
        $token2 = $this->sessionManager->generateCSRFToken();
        
        if (empty($token1) || empty($token2)) {
            throw new Exception("Génération de tokens CSRF échouée");
        }

        // Test 2: Tokens identiques (même session)
        if ($token1 !== $token2) {
            throw new Exception("Tokens CSRF différents dans la même session");
        }

        // Test 3: Vérification de token valide
        if (!$this->sessionManager->verifyCSRFToken($token1)) {
            throw new Exception("Vérification de token CSRF valide échouée");
        }

        // Test 4: Vérification de token invalide
        if ($this->sessionManager->verifyCSRFToken('invalid_token')) {
            throw new Exception("Vérification de token CSRF invalide acceptée");
        }

        return true;
    }
}

// Exécution des tests
if (php_sapi_name() === 'cli') {
    $test = new SecurityTest();
    $test->runAllTests();
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
    echo "Usage: php tests/security_test.php\n";
}
?> 