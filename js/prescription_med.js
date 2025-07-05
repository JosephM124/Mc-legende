document.addEventListener('DOMContentLoaded', function() {
    // Filtrer les dossiers en fonction du patient sélectionné
    const patientSelect = document.getElementById('patient_id');
    const dossierSelect = document.getElementById('dossier_id');
    
    patientSelect.addEventListener('change', function() {
        const patientId = this.value;
        
        // Activer/désactiver les options du select des dossiers
        for (let option of dossierSelect.options) {
            if (option.value === '') continue;
            
            if (option.dataset.patient === patientId) {
                option.hidden = false;
            } else {
                option.hidden = true;
            }
        }
        
        // Réinitialiser la sélection
        dossierSelect.value = '';
    });
    
    // Gestion des médicaments dynamiques
    const medicamentsContainer = document.getElementById('medicaments-container');
    const addMedicamentBtn = document.getElementById('addMedicament');
    
    addMedicamentBtn.addEventListener('click', function() {
        const newMedicament = document.createElement('div');
        newMedicament.className = 'medicament-item';
        newMedicament.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Médicament</label>
                    <input type="text" class="form-control" name="medicament[]" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Posologie</label>
                    <input type="text" class="form-control" name="posologie[]" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Durée</label>
                    <input type="text" class="form-control" name="duree[]" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Commentaire</label>
                    <input type="text" class="form-control" name="commentaire[]">
                </div>
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-medicament">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        medicamentsContainer.appendChild(newMedicament);
        
        // Afficher les boutons de suppression pour tous les médicaments sauf le premier
        const removeButtons = document.querySelectorAll('.btn-remove-medicament');
        removeButtons.forEach(btn => btn.style.display = 'block');
        removeButtons[0].style.display = 'none';
    });
    
    // Suppression d'un médicament
    medicamentsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-medicament') || 
            e.target.closest('.btn-remove-medicament')) {
            const btn = e.target.classList.contains('btn-remove-medicament') 
                      ? e.target 
                      : e.target.closest('.btn-remove-medicament');
            btn.closest('.medicament-item').remove();
            
            // Cacher le bouton de suppression s'il ne reste qu'un seul médicament
            const medicamentItems = document.querySelectorAll('.medicament-item');
            if (medicamentItems.length === 1) {
                document.querySelector('.btn-remove-medicament').style.display = 'none';
            }
        }
    });
    
    // Gestion des messages d'alerte
    const alertList = document.querySelectorAll('.alert');
    alertList.forEach(function (alert) {
        setTimeout(function() {
            alert.classList.add('fade');
            alert.classList.remove('show');
        }, 5000);
    });
});
// Script pour filtrer les rendez-vous par patient
document.addEventListener('DOMContentLoaded', function() {
const patientSelect = document.getElementById('patient_id');
const rendezvousSelect = document.getElementById('rendezvous_id');

patientSelect.addEventListener('change', function() {
const patientId = this.value;

for (let option of rendezvousSelect.options) {
    if (option.value === '') continue;
    
    if (option.dataset.patient === patientId) {
        option.hidden = false;
    } else {
        option.hidden = true;
    }
}

rendezvousSelect.value = '';
});
});
