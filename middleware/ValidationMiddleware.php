<?php
namespace Middleware;
class ValidationMiddleware
{
    /**
     * Valider les données d'un utilisateur
     */
    public function validateUtilisateur($data)
    {
        $errors = [];

        // Validation du nom
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est requis';
        } elseif (strlen($data['nom']) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        }

        // Validation du postnom
        if (empty($data['postnom'])) {
            $errors['postnom'] = 'Le postnom est requis';
        } elseif (strlen($data['postnom']) < 2) {
            $errors['postnom'] = 'Le postnom doit contenir au moins 2 caractères';
        }

        // Validation du prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est requis';
        } elseif (strlen($data['prenom']) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères';
        }

        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide';
        }

        // Validation du mot de passe (seulement pour la création)
        if (isset($data['mot_de_passe'])) {
            if (empty($data['mot_de_passe'])) {
                $errors['mot_de_passe'] = 'Le mot de passe est requis';
            } elseif (strlen($data['mot_de_passe']) < 6) {
                $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères';
            }
        }

        // Validation du rôle
        if (isset($data['role'])) {
            $allowedRoles = ['admin', 'admin_principal', 'admin_simple', 'eleve', 'enseignant'];
            if (!in_array($data['role'], $allowedRoles)) {
                $errors['role'] = 'Rôle invalide';
            }
        }

        // Validation du téléphone
        if (isset($data['telephone']) && !empty($data['telephone'])) {
            if (!preg_match('/^[0-9+\-\s\(\)]{8,15}$/', $data['telephone'])) {
                $errors['telephone'] = 'Format de téléphone invalide';
            }
        }

        // Validation du sexe
        if (isset($data['sexe']) && !empty($data['sexe'])) {
            if (!in_array($data['sexe'], ['M', 'F'])) {
                $errors['sexe'] = 'Le sexe doit être M ou F';
            }
        }

        // Validation de la date de naissance
        if (isset($data['naissance']) && !empty($data['naissance'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['naissance']);
            if (!$date || $date->format('Y-m-d') !== $data['naissance']) {
                $errors['naissance'] = 'Format de date invalide (YYYY-MM-DD)';
            } else {
                $age = $date->diff(new \DateTime())->y;
                if ($age < 5 || $age > 100) {
                    $errors['naissance'] = 'L\'âge doit être entre 5 et 100 ans';
                }
            }
        }

        return $errors;
    }

    /**
     * Valider les données d'un élève
     */
    public function validateEleve($data)
    {
        $errors = [];

        // Validation de l'ID utilisateur
        if (empty($data['utilisateur_id'])) {
            $errors['utilisateur_id'] = 'L\'ID utilisateur est requis';
        } elseif (!is_numeric($data['utilisateur_id'])) {
            $errors['utilisateur_id'] = 'L\'ID utilisateur doit être un nombre';
        }

        // Validation de l'établissement
        if (empty($data['etablissement'])) {
            $errors['etablissement'] = 'L\'établissement est requis';
        } elseif (strlen($data['etablissement']) < 3) {
            $errors['etablissement'] = 'L\'établissement doit contenir au moins 3 caractères';
        }

        // Validation de la section
        if (empty($data['section'])) {
            $errors['section'] = 'La section est requise';
        } elseif (strlen($data['section']) < 2) {
            $errors['section'] = 'La section doit contenir au moins 2 caractères';
        }

        // Validation de l'adresse de l'école
        if (isset($data['adresse_ecole']) && !empty($data['adresse_ecole'])) {
            if (strlen($data['adresse_ecole']) < 10) {
                $errors['adresse_ecole'] = 'L\'adresse de l\'école doit contenir au moins 10 caractères';
            }
        }

        // Validation de la catégorie
        if (isset($data['categorie']) && !empty($data['categorie'])) {
            $allowedCategories = ['primaire', 'secondaire', 'superieur'];
            if (!in_array(strtolower($data['categorie']), $allowedCategories)) {
                $errors['categorie'] = 'Catégorie invalide (primaire, secondaire, superieur)';
            }
        }

        // Validation du pays
        if (isset($data['pays']) && !empty($data['pays'])) {
            if (strlen($data['pays']) < 2) {
                $errors['pays'] = 'Le pays doit contenir au moins 2 caractères';
            }
        }

        // Validation de la ville/province
        if (isset($data['ville_province']) && !empty($data['ville_province'])) {
            if (strlen($data['ville_province']) < 2) {
                $errors['ville_province'] = 'La ville/province doit contenir au moins 2 caractères';
            }
        }

        return $errors;
    }

    /**
     * Valider les données de connexion
     */
    public function validateLogin($data)
    {
        $errors = [];

        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide';
        }

        // Validation du mot de passe
        if (empty($data['mot_de_passe'])) {
            $errors['mot_de_passe'] = 'Le mot de passe est requis';
        }

        return $errors;
    }

    /**
     * Valider les données de récupération de mot de passe
     */
    public function validatePasswordReset($data)
    {
        $errors = [];

        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide';
        }

        return $errors;
    }

    /**
     * Valider les données de changement de mot de passe
     */
    public function validatePasswordChange($data)
    {
        $errors = [];

        // Validation du token
        if (empty($data['token'])) {
            $errors['token'] = 'Le token est requis';
        }

        // Validation du nouveau mot de passe
        if (empty($data['nouveau_mot_de_passe'])) {
            $errors['nouveau_mot_de_passe'] = 'Le nouveau mot de passe est requis';
        } elseif (strlen($data['nouveau_mot_de_passe']) < 6) {
            $errors['nouveau_mot_de_passe'] = 'Le nouveau mot de passe doit contenir au moins 6 caractères';
        }

        // Validation de la confirmation du mot de passe
        if (empty($data['confirmation_mot_de_passe'])) {
            $errors['confirmation_mot_de_passe'] = 'La confirmation du mot de passe est requise';
        } elseif ($data['nouveau_mot_de_passe'] !== $data['confirmation_mot_de_passe']) {
            $errors['confirmation_mot_de_passe'] = 'Les mots de passe ne correspondent pas';
        }

        return $errors;
    }

    /**
     * Valider les données de recherche
     */
    public function validateSearch($data)
    {
        $errors = [];

        // Validation du terme de recherche
        if (isset($data['q']) && !empty($data['q'])) {
            if (strlen($data['q']) < 2) {
                $errors['q'] = 'Le terme de recherche doit contenir au moins 2 caractères';
            }
        }

        // Validation de la page
        if (isset($data['page']) && !empty($data['page'])) {
            if (!is_numeric($data['page']) || $data['page'] < 1) {
                $errors['page'] = 'Le numéro de page doit être un nombre positif';
            }
        }

        // Validation de la limite
        if (isset($data['limit']) && !empty($data['limit'])) {
            if (!is_numeric($data['limit']) || $data['limit'] < 1 || $data['limit'] > 100) {
                $errors['limit'] = 'La limite doit être un nombre entre 1 et 100';
            }
        }

        return $errors;
    }

    /**
     * Envoyer une réponse d'erreur de validation
     */
    public function sendValidationError($errors)
    {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Erreurs de validation',
            'errors' => $errors
        ]);
        exit;
    }
}
?> 