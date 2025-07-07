<?php
require_once '../databaseconnect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT u.nom, u.prenom,postnom, u.email, u.telephone, u.sexe, u.naissance, 
                   e.ville_province, e.pays,
                   e.etablissement, e.section,e.categorie_activite, e.adresse_ecole, u.photo,u.adresse
            FROM utilisateurs u
            JOIN eleves e ON u.id = e.utilisateur_id
            WHERE u.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $eleve = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eleve) {
        ?>
       <div class="row profile-card">
    <div class="col-md-4 text-center d-flex flex-column align-items-center p-4">
        <div class="profile-photo-container mb-3">
            <img src="<?= htmlspecialchars($eleve['photo']) ?>" class="img-fluid rounded-circle shadow-sm" style="width: 180px; height: 180px; object-fit: cover;" alt="Photo de profil">
        </div>
        <h3 class="h5 font-weight-bold text-primary"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></h3>
        <p class="text-muted small"><?= htmlspecialchars($eleve['section']) ?></p>
    </div>
    
    <div class="col-md-8 p-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title text-primary mb-4">Informations personnelles</h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-id-card mr-2 text-secondary"></i> Nom complet :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom'] . ' ' . $eleve['postnom']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-envelope mr-2 text-secondary"></i> Email :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['email']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-phone mr-2 text-secondary"></i> Téléphone :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['telephone']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-map-marker-alt mr-2 text-secondary"></i> Adresse :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['adresse']) ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-venus-mars mr-2 text-secondary"></i> Sexe :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['sexe']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-birthday-cake mr-2 text-secondary"></i> Date de naissance :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['naissance']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-school mr-2 text-secondary"></i> Établissement :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['etablissement']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-map-marked-alt mr-2 text-secondary"></i> Adresse école :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['adresse_ecole']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-tag mr-2 text-secondary"></i> Catégorie :</strong></p>
                            <p class="text-muted "><?= htmlspecialchars($eleve['categorie_activite']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-city mr-2 text-secondary"></i> Ville :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['ville_province']) ?></p>
                        </div>
                        
                        <div class="info-item mb-3">
                            <p class="mb-1"><strong><i class="fas fa-flag mr-2 text-secondary"></i> Pays :</strong></p>
                            <p class="text-muted"><?= htmlspecialchars($eleve['pays']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <?php
    } else {
        echo "<p class='text-danger'>Aucun élève trouvé.</p>";
    }
} else {
    echo "<p class='text-danger'>ID invalide.</p>";
}
?>
