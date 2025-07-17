<?php
namespace Models;

class EleveModel extends \Models\Database
{
    public function getUtilisateur($id) {
        return $this->selectOne("SELECT nom, prenom, photo, role FROM utilisateurs WHERE id = ?", [$id]);
    }
    public function getCategorieActivite($id) {
        $res = $this->selectOne("SELECT e.categorie_activite FROM eleves e INNER JOIN utilisateurs u ON e.utilisateur_id = u.id WHERE u.id = ?", [$id]);
        return $res ? $res['categorie_activite'] : null;
    }
    public function getQuizActif($categorie) {
        return $this->selectOne("SELECT id, titre FROM quiz WHERE statut = 'actif' AND categorie = ? LIMIT 1", [$categorie]);
    }
    public function getNotifications($userId, $categorie) {
        return $this->select("SELECT * FROM notifications WHERE ((est_generale = 1 AND role_destinataire = 'eleve' AND ((categorie IS NULL) OR (categorie = ?))) AND (utilisateur_id = ?)) ORDER BY date_creation DESC LIMIT 5", [$categorie, $userId]);
    }
    public function getInterrosAVenir($categorie) {
        return $this->select("SELECT id, titre, date_lancement, duree_totale FROM quiz WHERE categorie = ? AND date_lancement > NOW() ORDER BY date_lancement ASC", [$categorie]);
    }
} 