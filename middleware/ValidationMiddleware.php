<?php
namespace Middleware;

class ValidationMiddleware
{
    private $errors = [];

    /**
     * Valider les données d'entrée selon des règles définies
     */
    public function validate($data, $rules)
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $data[$field] ?? null, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Appliquer une règle de validation
     */
    private function applyRule($field, $value, $rule)
    {
        $params = [];
        
        if (strpos($rule, ':') !== false) {
            list($rule, $paramString) = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "Le champ $field est requis");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Le champ $field doit être une adresse email valide");
                }
                break;

            case 'min':
                $min = (int) $params[0];
                if (!empty($value) && strlen($value) < $min) {
                    $this->addError($field, "Le champ $field doit contenir au moins $min caractères");
                }
                break;

            case 'max':
                $max = (int) $params[0];
                if (!empty($value) && strlen($value) > $max) {
                    $this->addError($field, "Le champ $field ne peut pas dépasser $max caractères");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "Le champ $field doit être numérique");
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, "Le champ $field doit être un entier");
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "Le champ $field doit être une URL valide");
                }
                break;

            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, "Le champ $field doit être une date valide");
                }
                break;

            case 'in':
                if (!empty($value) && !in_array($value, $params)) {
                    $this->addError($field, "Le champ $field doit être une des valeurs suivantes: " . implode(', ', $params));
                }
                break;

            case 'regex':
                $pattern = $params[0];
                if (!empty($value) && !preg_match($pattern, $value)) {
                    $this->addError($field, "Le champ $field ne respecte pas le format requis");
                }
                break;

            case 'unique':
                $table = $params[0];
                $column = $params[1] ?? $field;
                $except = $params[2] ?? null;
                
                if (!empty($value)) {
                    $database = \App\App::getMysqlDatabaseInstance();
                    $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
                    $params = [$value];
                    
                    if ($except) {
                        $sql .= " AND id != ?";
                        $params[] = $except;
                    }
                    
                    $result = $database->select($sql, $params);
                    
                    if ($result[0]['count'] > 0) {
                        $this->addError($field, "Cette valeur pour $field existe déjà");
                    }
                }
                break;

            case 'exists':
                $table = $params[0];
                $column = $params[1] ?? $field;
                
                if (!empty($value)) {
                    $database = \App\App::getMysqlDatabaseInstance();
                    $result = $database->select(
                        "SELECT COUNT(*) as count FROM $table WHERE $column = ?",
                        [$value]
                    );
                    
                    if ($result[0]['count'] == 0) {
                        $this->addError($field, "Cette valeur pour $field n'existe pas");
                    }
                }
                break;

            case 'password':
                if (!empty($value)) {
                    // Vérifier la complexité du mot de passe
                    if (strlen($value) < 8) {
                        $this->addError($field, "Le mot de passe doit contenir au moins 8 caractères");
                    }
                    
                    if (!preg_match('/[A-Z]/', $value)) {
                        $this->addError($field, "Le mot de passe doit contenir au moins une majuscule");
                    }
                    
                    if (!preg_match('/[a-z]/', $value)) {
                        $this->addError($field, "Le mot de passe doit contenir au moins une minuscule");
                    }
                    
                    if (!preg_match('/[0-9]/', $value)) {
                        $this->addError($field, "Le mot de passe doit contenir au moins un chiffre");
                    }
                    
                    if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                        $this->addError($field, "Le mot de passe doit contenir au moins un caractère spécial");
                    }
                }
                break;

            case 'phone':
                if (!empty($value) && !preg_match('/^[\+]?[0-9\s\-\(\)]{8,15}$/', $value)) {
                    $this->addError($field, "Le champ $field doit être un numéro de téléphone valide");
                }
                break;

            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-Z\s]+$/', $value)) {
                    $this->addError($field, "Le champ $field ne peut contenir que des lettres");
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
                    $this->addError($field, "Le champ $field ne peut contenir que des lettres et des chiffres");
                }
                break;
        }
    }

    /**
     * Ajouter une erreur
     */
    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Récupérer les erreurs
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Nettoyer les données d'entrée
     */
    public function sanitize($data)
    {
        $clean = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Supprimer les espaces en début et fin
                $value = trim($value);
                
                // Convertir les caractères spéciaux en entités HTML
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                
                // Supprimer les caractères de contrôle
                $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
            }
            
            $clean[$key] = $value;
        }
        
        return $clean;
    }

    /**
     * Valider un token CSRF
     */
    public function validateCSRF($token)
    {
        $sessionManager = new SessionManager();
        return $sessionManager->verifyCSRFToken($token);
    }

    /**
     * Valider une adresse IP
     */
    public function validateIP($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Valider un User-Agent
     */
    public function validateUserAgent($userAgent)
    {
        // Vérifier que le User-Agent n'est pas vide et a une longueur raisonnable
        return !empty($userAgent) && strlen($userAgent) <= 500;
    }

    /**
     * Valider un ID numérique
     */
    public function validateID($id)
    {
        return is_numeric($id) && $id > 0 && $id == (int) $id;
    }

    /**
     * Valider une date
     */
    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Valider un fichier uploadé
     */
    public function validateFile($file, $allowedTypes = [], $maxSize = 5242880) // 5MB par défaut
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valider une URL
     */
    public function validateURL($url, $allowedDomains = [])
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (!empty($allowedDomains)) {
            $parsedUrl = parse_url($url);
            if (!in_array($parsedUrl['host'], $allowedDomains)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valider un JSON
     */
    public function validateJSON($json)
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Valider une adresse email avec vérification DNS
     */
    public function validateEmailWithDNS($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
    }
}
?> 
?> 