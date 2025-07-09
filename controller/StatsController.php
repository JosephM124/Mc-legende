<?php
namespace Controllers;

class StatsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupérer les statistiques globales
     */
    public function getGlobales()
    {
        try {
            $stats = [];

            // Statistiques des utilisateurs
            $utilisateurs = $this->database->select(
                "SELECT 
                    COUNT(*) as total_utilisateurs,
                    COUNT(CASE WHEN statut = 'active' THEN 1 END) as utilisateurs_actifs,
                    COUNT(CASE WHEN role = 'eleve' THEN 1 END) as eleves,
                    COUNT(CASE WHEN role = 'admin_simple' THEN 1 END) as admins_simples,
                    COUNT(CASE WHEN role = 'admin_principal' THEN 1 END) as admins_principaux
                 FROM utilisateurs"
            );
            $stats['utilisateurs'] = $utilisateurs[0];

            // Statistiques des élèves
            $eleves = $this->database->select(
                "SELECT 
                    COUNT(*) as total_eleves,
                    COUNT(DISTINCT etablissement) as nombre_etablissements,
                    COUNT(DISTINCT section) as nombre_sections
                 FROM eleves"
            );
            $stats['eleves'] = $eleves[0];

            // Statistiques des interrogations
            $interrogations = $this->database->select(
                "SELECT 
                    COUNT(*) as total_interrogations,
                    COUNT(CASE WHEN statut = 'active' THEN 1 END) as interrogations_actives,
                    COUNT(CASE WHEN statut = 'finished' THEN 1 END) as interrogations_terminees,
                    COUNT(DISTINCT matiere) as nombre_matieres
                 FROM interrogations"
            );
            $stats['interrogations'] = $interrogations[0];

            // Statistiques des questions
            $questions = $this->database->select(
                "SELECT 
                    COUNT(*) as total_questions,
                    COUNT(DISTINCT type) as nombre_types,
                    AVG(points) as points_moyens
                 FROM questions"
            );
            $stats['questions'] = $questions[0];

            // Statistiques des résultats
            $resultats = $this->database->select(
                "SELECT 
                    COUNT(*) as total_resultats,
                    AVG(score) as score_moyen,
                    MAX(score) as score_max,
                    MIN(score) as score_min,
                    COUNT(DISTINCT eleve_id) as nombre_participants
                 FROM resultats"
            );
            $stats['resultats'] = $resultats[0];

            $this->successResponse($stats, 'Statistiques globales récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les statistiques des élèves
     */
    public function getStatsEleves()
    {
        try {
            $stats = [];

            // Statistiques par établissement
            $etablissements = $this->database->select(
                "SELECT 
                    etablissement,
                    COUNT(*) as nombre_eleves,
                    COUNT(DISTINCT section) as nombre_sections
                 FROM eleves 
                 GROUP BY etablissement 
                 ORDER BY nombre_eleves DESC"
            );
            $stats['par_etablissement'] = $etablissements;

            // Statistiques par section
            $sections = $this->database->select(
                "SELECT 
                    section,
                    COUNT(*) as nombre_eleves,
                    COUNT(DISTINCT etablissement) as nombre_etablissements
                 FROM eleves 
                 GROUP BY section 
                 ORDER BY nombre_eleves DESC"
            );
            $stats['par_section'] = $sections;

            // Statistiques par pays
            $pays = $this->database->select(
                "SELECT 
                    pays,
                    COUNT(*) as nombre_eleves
                 FROM eleves 
                 WHERE pays IS NOT NULL AND pays != ''
                 GROUP BY pays 
                 ORDER BY nombre_eleves DESC"
            );
            $stats['par_pays'] = $pays;

            // Statistiques par ville
            $villes = $this->database->select(
                "SELECT 
                    ville_province,
                    COUNT(*) as nombre_eleves
                 FROM eleves 
                 WHERE ville_province IS NOT NULL AND ville_province != ''
                 GROUP BY ville_province 
                 ORDER BY nombre_eleves DESC"
            );
            $stats['par_ville'] = $villes;

            $this->successResponse($stats, 'Statistiques des élèves récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les statistiques des interrogations
     */
    public function getStatsInterrogations()
    {
        try {
            $stats = [];

            // Statistiques par matière
            $matieres = $this->database->select(
                "SELECT 
                    matiere,
                    COUNT(*) as nombre_interrogations,
                    COUNT(CASE WHEN statut = 'active' THEN 1 END) as actives,
                    COUNT(CASE WHEN statut = 'finished' THEN 1 END) as terminees,
                    AVG(duree) as duree_moyenne
                 FROM interrogations 
                 GROUP BY matiere 
                 ORDER BY nombre_interrogations DESC"
            );
            $stats['par_matiere'] = $matieres;

            // Statistiques par niveau
            $niveaux = $this->database->select(
                "SELECT 
                    niveau,
                    COUNT(*) as nombre_interrogations
                 FROM interrogations 
                 GROUP BY niveau 
                 ORDER BY nombre_interrogations DESC"
            );
            $stats['par_niveau'] = $niveaux;

            // Statistiques par statut
            $statuts = $this->database->select(
                "SELECT 
                    statut,
                    COUNT(*) as nombre_interrogations
                 FROM interrogations 
                 GROUP BY statut 
                 ORDER BY nombre_interrogations DESC"
            );
            $stats['par_statut'] = $statuts;

            // Statistiques par créateur
            $createurs = $this->database->select(
                "SELECT 
                    u.nom, u.prenom,
                    COUNT(i.id) as nombre_interrogations
                 FROM interrogations i 
                 JOIN utilisateurs u ON i.created_by = u.id 
                 GROUP BY i.created_by 
                 ORDER BY nombre_interrogations DESC"
            );
            $stats['par_createur'] = $createurs;

            $this->successResponse($stats, 'Statistiques des interrogations récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les statistiques des résultats
     */
    public function getStatsResultats()
    {
        try {
            $stats = [];

            // Statistiques par interrogation
            $interrogations = $this->database->select(
                "SELECT 
                    i.titre,
                    i.matiere,
                    COUNT(r.id) as nombre_participants,
                    AVG(r.score) as score_moyen,
                    MAX(r.score) as score_max,
                    MIN(r.score) as score_min,
                    COUNT(CASE WHEN r.score >= 80 THEN 1 END) as excellents,
                    COUNT(CASE WHEN r.score >= 60 AND r.score < 80 THEN 1 END) as bons,
                    COUNT(CASE WHEN r.score >= 40 AND r.score < 60 THEN 1 END) as moyens,
                    COUNT(CASE WHEN r.score < 40 THEN 1 END) as faibles
                 FROM resultats r 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 GROUP BY r.interrogation_id 
                 ORDER BY score_moyen DESC"
            );
            $stats['par_interrogation'] = $interrogations;

            // Statistiques par élève
            $eleves = $this->database->select(
                "SELECT 
                    e.nom, e.prenom, e.etablissement,
                    COUNT(r.id) as nombre_interrogations,
                    AVG(r.score) as score_moyen,
                    MAX(r.score) as score_max,
                    MIN(r.score) as score_min,
                    SUM(r.score) as score_total
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 GROUP BY r.eleve_id 
                 ORDER BY score_moyen DESC"
            );
            $stats['par_eleve'] = $eleves;

            // Statistiques par établissement
            $etablissements = $this->database->select(
                "SELECT 
                    e.etablissement,
                    COUNT(DISTINCT r.eleve_id) as nombre_eleves,
                    COUNT(r.id) as nombre_resultats,
                    AVG(r.score) as score_moyen
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 GROUP BY e.etablissement 
                 ORDER BY score_moyen DESC"
            );
            $stats['par_etablissement'] = $etablissements;

            // Statistiques par matière
            $matieres = $this->database->select(
                "SELECT 
                    i.matiere,
                    COUNT(r.id) as nombre_resultats,
                    AVG(r.score) as score_moyen,
                    COUNT(DISTINCT r.eleve_id) as nombre_eleves
                 FROM resultats r 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 GROUP BY i.matiere 
                 ORDER BY score_moyen DESC"
            );
            $stats['par_matiere'] = $matieres;

            $this->successResponse($stats, 'Statistiques des résultats récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les statistiques des questions
     */
    public function getStatsQuestions()
    {
        try {
            $stats = [];

            // Statistiques par type
            $types = $this->database->select(
                "SELECT 
                    type,
                    COUNT(*) as nombre_questions,
                    AVG(points) as points_moyens,
                    SUM(points) as points_totaux
                 FROM questions 
                 GROUP BY type"
            );
            $stats['par_type'] = $types;

            // Statistiques par interrogation
            $interrogations = $this->database->select(
                "SELECT 
                    i.titre,
                    i.matiere,
                    COUNT(q.id) as nombre_questions,
                    AVG(q.points) as points_moyens,
                    COUNT(DISTINCT q.type) as nombre_types
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 GROUP BY q.interrogation_id 
                 ORDER BY nombre_questions DESC"
            );
            $stats['par_interrogation'] = $interrogations;

            // Statistiques par matière
            $matieres = $this->database->select(
                "SELECT 
                    i.matiere,
                    COUNT(q.id) as nombre_questions,
                    AVG(q.points) as points_moyens,
                    COUNT(DISTINCT q.type) as nombre_types
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 GROUP BY i.matiere 
                 ORDER BY nombre_questions DESC"
            );
            $stats['par_matiere'] = $matieres;

            $this->successResponse($stats, 'Statistiques des questions récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les statistiques d'activité
     */
    public function getStatsActivite()
    {
        try {
            $stats = [];

            // Activité par jour (7 derniers jours)
            $activite_jour = $this->database->select(
                "SELECT 
                    DATE(date_soumission) as date,
                    COUNT(*) as nombre_resultats,
                    COUNT(DISTINCT eleve_id) as nombre_eleves
                 FROM resultats 
                 WHERE date_soumission >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY DATE(date_soumission) 
                 ORDER BY date DESC"
            );
            $stats['activite_jour'] = $activite_jour;

            // Activité par mois (12 derniers mois)
            $activite_mois = $this->database->select(
                "SELECT 
                    DATE_FORMAT(date_soumission, '%Y-%m') as mois,
                    COUNT(*) as nombre_resultats,
                    COUNT(DISTINCT eleve_id) as nombre_eleves
                 FROM resultats 
                 WHERE date_soumission >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY DATE_FORMAT(date_soumission, '%Y-%m') 
                 ORDER BY mois DESC"
            );
            $stats['activite_mois'] = $activite_mois;

            // Activité des admins
            $activite_admin = $this->database->select(
                "SELECT 
                    u.nom, u.prenom,
                    COUNT(a.id) as nombre_activites
                 FROM activites_admin a 
                 JOIN utilisateurs u ON a.admin_id = u.id 
                 WHERE a.date_activite >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 GROUP BY a.admin_id 
                 ORDER BY nombre_activites DESC"
            );
            $stats['activite_admin'] = $activite_admin;

            $this->successResponse($stats, 'Statistiques d\'activité récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer toutes les statistiques du dashboard en une seule requête optimisée
     */
    public function getDashboardStats()
    {
        try {
            $stats = [];

            // Statistiques globales (widgets)
            $globales = $this->database->select(
                "SELECT 
                    (SELECT COUNT(*) FROM eleves) as total_eleves,
                    (SELECT COUNT(*) FROM quiz WHERE statut = 'actif') as total_interros_actives,
                    (SELECT COUNT(*) FROM notifications WHERE lue = 0) as total_notifications_non_lues,
                    (SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin_simple') as total_admins_simples,
                    (SELECT COUNT(*) FROM quiz) as total_interros,
                    (SELECT COUNT(*) FROM resultats) as total_resultats,
                    (SELECT AVG(score) FROM resultats WHERE score IS NOT NULL) as score_moyen_global,
                    (SELECT COUNT(DISTINCT eleve_id) FROM resultats) as eleves_ayant_participe"
            );
            $stats['widgets'] = $globales[0];

            // Répartition des élèves par section
            $sections = $this->database->select(
                "SELECT 
                    section,
                    COUNT(*) as total,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM eleves), 2) as pourcentage
                 FROM eleves 
                 WHERE section IS NOT NULL AND section != ''
                 GROUP BY section 
                 ORDER BY total DESC"
            );
            $stats['sections'] = $sections;

            // Interrogations créées par jour (7 derniers jours)
            $interro_stats = $this->database->select(
                "SELECT 
                    DATE(date_creation) as jour,
                    COUNT(*) as total,
                    COUNT(CASE WHEN statut = 'actif' THEN 1 END) as actives,
                    COUNT(CASE WHEN statut = 'termine' THEN 1 END) as terminees
                 FROM quiz 
                 WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY DATE(date_creation) 
                 ORDER BY jour DESC"
            );
            $stats['interro_stats'] = $interro_stats;

            // Distribution des scores (par tranches)
            $score_stats = $this->database->select(
                "SELECT 
                    CASE 
                        WHEN score >= 90 THEN '90-100'
                        WHEN score >= 80 THEN '80-89'
                        WHEN score >= 70 THEN '70-79'
                        WHEN score >= 60 THEN '60-69'
                        WHEN score >= 50 THEN '50-59'
                        ELSE '0-49'
                    END as tranche_score,
                    COUNT(*) as total
                 FROM resultats 
                 WHERE score IS NOT NULL
                 GROUP BY 
                    CASE 
                        WHEN score >= 90 THEN '90-100'
                        WHEN score >= 80 THEN '80-89'
                        WHEN score >= 70 THEN '70-79'
                        WHEN score >= 60 THEN '60-69'
                        WHEN score >= 50 THEN '50-59'
                        ELSE '0-49'
                    END
                 ORDER BY 
                    CASE tranche_score
                        WHEN '90-100' THEN 1
                        WHEN '80-89' THEN 2
                        WHEN '70-79' THEN 3
                        WHEN '60-69' THEN 4
                        WHEN '50-59' THEN 5
                        ELSE 6
                    END"
            );
            $stats['score_stats'] = $score_stats;

            // Top 5 des matières les plus populaires
            $matieres_populaires = $this->database->select(
                "SELECT 
                    q.categorie as matiere,
                    COUNT(DISTINCT q.id) as nombre_quiz,
                    COUNT(r.id) as nombre_participations,
                    AVG(r.score) as score_moyen
                 FROM quiz q
                 LEFT JOIN resultats r ON q.id = r.quiz_id
                 GROUP BY q.categorie
                 ORDER BY nombre_participations DESC
                 LIMIT 5"
            );
            $stats['matieres_populaires'] = $matieres_populaires;

            // Activité récente (dernières 24h)
            $activite_recente = $this->database->select(
                "SELECT 
                    'Connexions' as type_activite,
                    COUNT(*) as total
                 FROM activites_admin 
                 WHERE action LIKE '%Connexion%' 
                 AND date_activite >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 UNION ALL
                 SELECT 
                    'Nouveaux résultats' as type_activite,
                    COUNT(*) as total
                 FROM resultats 
                 WHERE date_soumission >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 UNION ALL
                 SELECT 
                    'Nouvelles interrogations' as type_activite,
                    COUNT(*) as total
                 FROM quiz 
                 WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
            );
            $stats['activite_recente'] = $activite_recente;

            // Performance par établissement
            $performance_etablissements = $this->database->select(
                "SELECT 
                    e.etablissement,
                    COUNT(DISTINCT r.eleve_id) as nombre_eleves,
                    COUNT(r.id) as nombre_resultats,
                    AVG(r.score) as score_moyen,
                    MAX(r.score) as score_max
                 FROM resultats r
                 JOIN eleves e ON r.eleve_id = e.id
                 WHERE e.etablissement IS NOT NULL AND e.etablissement != ''
                 GROUP BY e.etablissement
                 HAVING nombre_resultats >= 5
                 ORDER BY score_moyen DESC
                 LIMIT 10"
            );
            $stats['performance_etablissements'] = $performance_etablissements;

            $this->successResponse($stats, 'Statistiques dashboard récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques dashboard: ' . $e->getMessage());
        }
    }
}
?> 