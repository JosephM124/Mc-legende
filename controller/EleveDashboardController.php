<?php
namespace Controllers;

class EleveDashboardController extends BaseController
{
    public function showDashboard()
    {
        $this->requireRole('eleve');
        $eleveModel = new \Models\EleveModel(\App\App::getConfigInstance());

        $userId = $this->sessionManager->getCurrentUser()['id'];
        $utilisateur = $eleveModel->getUtilisateur($userId);
        $categorie = $eleveModel->getCategorieActivite($userId);
        $quizActif = $eleveModel->getQuizActif($categorie);
        $notifications = $eleveModel->getNotifications($userId, $categorie);
        $interrosAVenir = $eleveModel->getInterrosAVenir($categorie);

        require __DIR__ . '/../views/eleve.php';
    }
} 