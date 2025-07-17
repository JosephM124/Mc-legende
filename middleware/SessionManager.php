<?php
namespace Middleware;

class SessionManager
{
    private $database;
    private $sessionTimeout = 3600; // 1 heure
    private $regenerateInterval = 300; // 5 minutes

    public function __construct()
    {
        $this->database = \App\App::getMysqlDatabaseInstance();
        $this->initSecureSession();
    }

    /**
     * Initialise une session sécurisée
     */
    private function initSecureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuration sécurisée des sessions
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            session_name('MCLEGENDE_SESSION');
            session_start();
        }
        // Si la session est déjà active, ne rien changer
        $this->regenerateSessionIfNeeded();
        $this->checkSessionExpiration();
    }

    /**
     * Régénère l'ID de session si nécessaire
     */
    private function regenerateSessionIfNeeded()
    {
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        }

        if (time() - $_SESSION['last_regeneration'] > $this->regenerateInterval) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    /**
     * Vérifie l'expiration de la session
     */
    private function checkSessionExpiration()
    {
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > $this->sessionTimeout)) {
            $this->destroySession();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Crée une session utilisateur sécurisée
     */
    public function createUserSession($user)
    {
        // Nettoyer les données sensibles
        unset($user['mot_de_passe']);
        unset($user['reset_token']);
        unset($user['token_expiration']);

        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_data'] = $user;
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = $this->getClientIP();
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Enregistrer la session en base de données
        $this->logSession($user['id'], 'login');

        return true;
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     */
    public function isAuthenticated()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Vérifier l'expiration
        if (!$this->checkSessionExpiration()) {
            return false;
        }

        // Vérifier l'IP et User-Agent
        if (!$this->validateSessionIntegrity()) {
            $this->destroySession();
            return false;
        }

        return true;
    }

    /**
     * Vérifie l'intégrité de la session
     */
    private function validateSessionIntegrity()
    {
        if (!isset($_SESSION['ip_address']) || !isset($_SESSION['user_agent'])) {
            return false;
        }

        $currentIP = $this->getClientIP();
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return $_SESSION['ip_address'] === $currentIP && 
               $_SESSION['user_agent'] === $currentUserAgent;
    }

    /**
     * Récupère les données de l'utilisateur connecté
     */
    public function getCurrentUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $_SESSION['user_data'] ?? null;
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public function hasRole($role)
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return $_SESSION['user_role'] === $role;
    }

    /**
     * Vérifie si l'utilisateur a un des rôles autorisés
     */
    public function hasAnyRole($roles)
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return in_array($_SESSION['user_role'], $roles);
    }

    /**
     * Détruit la session
     */
    public function destroySession()
    {
        if (isset($_SESSION['user_id'])) {
            $this->logSession($_SESSION['user_id'], 'logout');
        }

        // Nettoyer la session
        $_SESSION = array();

        // Détruire le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Détruire la session
        session_destroy();
    }

    /**
     * Enregistre l'activité de session en base de données
     */
    private function logSession($userId, $action)
    {
        try {
            $this->database->prepare(
                "INSERT INTO sessions (user_id, token, ip_address, user_agent, date_debut, date_fin, active) 
                 VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR), ?)",
                [
                    $userId,
                    session_id(),
                    $this->getClientIP(),
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    $action === 'login' ? 1 : 0
                ]
            );
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas faire échouer l'authentification
            error_log("Erreur lors de l'enregistrement de session: " . $e->getMessage());
        }
    }

    /**
     * Récupère l'adresse IP du client
     */
    private function getClientIP()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Génère un token CSRF
     */
    public function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie un token CSRF
     */
    public function verifyCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Limite les tentatives de connexion
     */
    public function checkLoginAttempts($email)
    {
        $attempts = $this->database->select(
            "SELECT COUNT(*) as count FROM logs 
             WHERE action = 'login_failed' 
             AND description LIKE ? 
             AND date_creation > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            ["%$email%"]
        );

        return $attempts[0]['count'] < 5; // Maximum 5 tentatives en 15 minutes
    }

    /**
     * Enregistre une tentative de connexion échouée
     */
    public function logFailedLogin($email)
    {
        try {
            $this->database->prepare(
                "INSERT INTO logs (action, description, ip_address) VALUES (?, ?, ?)",
                ['login_failed', "Tentative de connexion échouée pour: $email", $this->getClientIP()]
            );
        } catch (\Exception $e) {
            error_log("Erreur lors de l'enregistrement de la tentative échouée: " . $e->getMessage());
        }
    }
}
?> 