-- Script de mise à jour de sécurité pour MC-LEGENDE
-- À exécuter sur la base de données mc-legende

USE `mc-legende`;

-- 1. Ajouter des index pour améliorer les performances et la sécurité
ALTER TABLE `utilisateurs` ADD INDEX `idx_email` (`email`);
ALTER TABLE `utilisateurs` ADD INDEX `idx_role` (`role`);
ALTER TABLE `utilisateurs` ADD INDEX `idx_statut` (`statut`);
ALTER TABLE `utilisateurs` ADD INDEX `idx_date_inscription` (`date_inscription`);

-- 2. Ajouter des contraintes de sécurité
ALTER TABLE `utilisateurs` 
ADD CONSTRAINT `chk_role` CHECK (`role` IN ('admin', 'admin_principal', 'admin_simple', 'eleve', 'enseignant')),
ADD CONSTRAINT `chk_statut` CHECK (`statut` IN ('active', 'inactive', 'suspended', 'deleted')),
ADD CONSTRAINT `chk_sexe` CHECK (`sexe` IN ('M', 'F', ''));

-- 3. Améliorer la table sessions
ALTER TABLE `sessions` 
ADD INDEX `idx_token` (`token`),
ADD INDEX `idx_user_id` (`user_id`),
ADD INDEX `idx_date_fin` (`date_fin`),
ADD INDEX `idx_active` (`active`);

-- 4. Améliorer la table logs
ALTER TABLE `logs` 
ADD INDEX `idx_action` (`action`),
ADD INDEX `idx_date_creation` (`date_creation`),
ADD INDEX `idx_user_id` (`user_id`),
ADD INDEX `idx_ip_address` (`ip_address`);

-- 5. Ajouter des colonnes de sécurité à la table utilisateurs
ALTER TABLE `utilisateurs` 
ADD COLUMN `last_login` DATETIME NULL AFTER `date_inscription`,
ADD COLUMN `failed_login_attempts` INT DEFAULT 0 AFTER `last_login`,
ADD COLUMN `locked_until` DATETIME NULL AFTER `failed_login_attempts`,
ADD COLUMN `password_changed_at` DATETIME NULL AFTER `locked_until`,
ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `password_changed_at`,
ADD COLUMN `email_verification_token` VARCHAR(255) NULL AFTER `email_verified`,
ADD COLUMN `email_verification_expires` DATETIME NULL AFTER `email_verification_token`;

-- 6. Créer une table pour les tentatives de connexion échouées
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `attempted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Créer une table pour les tokens de réinitialisation de mot de passe
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Créer une table pour les sessions actives (alternative à la table sessions existante)
CREATE TABLE IF NOT EXISTS `active_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_activity` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `active_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Ajouter des triggers pour la sécurité
DELIMITER //

-- Trigger pour nettoyer les sessions expirées
CREATE TRIGGER `cleanup_expired_sessions` 
BEFORE INSERT ON `active_sessions`
FOR EACH ROW
BEGIN
    DELETE FROM `active_sessions` WHERE `expires_at` < NOW();
END//

-- Trigger pour nettoyer les tokens expirés
CREATE TRIGGER `cleanup_expired_tokens` 
BEFORE INSERT ON `password_reset_tokens`
FOR EACH ROW
BEGIN
    DELETE FROM `password_reset_tokens` WHERE `expires_at` < NOW();
END//

-- Trigger pour nettoyer les tentatives de connexion anciennes
CREATE TRIGGER `cleanup_old_login_attempts` 
BEFORE INSERT ON `login_attempts`
FOR EACH ROW
BEGIN
    DELETE FROM `login_attempts` WHERE `attempted_at` < DATE_SUB(NOW(), INTERVAL 24 HOUR);
END//

DELIMITER ;

-- 10. Créer des vues pour la sécurité
CREATE OR REPLACE VIEW `v_users_security` AS
SELECT 
    u.id,
    u.email,
    u.role,
    u.statut,
    u.last_login,
    u.failed_login_attempts,
    u.locked_until,
    u.email_verified,
    COUNT(s.id) as active_sessions_count,
    MAX(s.last_activity) as last_session_activity
FROM `utilisateurs` u
LEFT JOIN `active_sessions` s ON u.id = s.user_id AND s.is_active = 1
GROUP BY u.id;

-- 11. Créer des procédures stockées pour la sécurité
DELIMITER //

-- Procédure pour verrouiller un compte après trop de tentatives
CREATE PROCEDURE `lock_account_after_failed_attempts`(IN user_email VARCHAR(255))
BEGIN
    DECLARE user_id INT;
    DECLARE failed_count INT;
    
    SELECT id, failed_login_attempts INTO user_id, failed_count
    FROM utilisateurs 
    WHERE email = user_email;
    
    IF user_id IS NOT NULL AND failed_count >= 5 THEN
        UPDATE utilisateurs 
        SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
        WHERE id = user_id;
    END IF;
END//

-- Procédure pour réinitialiser les tentatives de connexion
CREATE PROCEDURE `reset_failed_login_attempts`(IN user_email VARCHAR(255))
BEGIN
    UPDATE utilisateurs 
    SET failed_login_attempts = 0, locked_until = NULL
    WHERE email = user_email;
END//

-- Procédure pour nettoyer les sessions expirées
CREATE PROCEDURE `cleanup_expired_sessions`()
BEGIN
    DELETE FROM active_sessions WHERE expires_at < NOW();
    DELETE FROM password_reset_tokens WHERE expires_at < NOW();
    DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);
END//

DELIMITER ;

-- 12. Créer un utilisateur pour les tâches de maintenance (optionnel)
-- INSERT INTO `utilisateurs` (`nom`, `email`, `mot_de_passe`, `role`, `statut`) 
-- VALUES ('System', 'system@mc-legende.local', '$2y$10$' || SUBSTRING(SHA2(RAND(), 256), 1, 22), 'admin', 'active');

-- Message de confirmation
SELECT 'Mise à jour de sécurité terminée avec succès!' as message; 