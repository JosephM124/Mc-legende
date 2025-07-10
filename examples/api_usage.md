# 📚 Exemples d'Utilisation des API - MC-LEGENDE

## 🔐 Authentification

### Connexion
```javascript
// POST /api/utilisateurs/login
fetch('/api/utilisateurs/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        identifiant: 'john.doe@email.com',
        mot_de_passe: 'password123'
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        localStorage.setItem('token', data.data.token);
        window.location.href = '/eleve/home';
    }
});
```

### Inscription
```javascript
// POST /api/utilisateurs
fetch('/api/utilisateurs', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        nom: 'Doe',
        postnom: 'John',
        prenom: 'Smith',
        email: 'john.doe@email.com',
        mot_de_passe: 'password123',
        role: 'eleve',
        telephone: '+243123456789',
        sexe: 'M'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 👥 Gestion des Utilisateurs

### Récupérer tous les utilisateurs
```javascript
// GET /api/utilisateurs
fetch('/api/utilisateurs')
.then(response => response.json())
.then(data => {
    if (data.success) {
        data.data.forEach(user => {
            console.log(`${user.nom} ${user.prenom} - ${user.email}`);
        });
    }
});
```

### Récupérer un utilisateur par ID
```javascript
// GET /api/utilisateurs/123
fetch('/api/utilisateurs/123')
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Utilisateur:', data.data);
    }
});
```

### Mettre à jour un utilisateur
```javascript
// PUT /api/utilisateurs/123
fetch('/api/utilisateurs/123', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        nom: 'Nouveau Nom',
        telephone: '+243987654321'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 🎓 Gestion des Élèves

### Créer un nouvel élève
```javascript
// POST /api/eleves
fetch('/api/eleves', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        utilisateur_id: 123,
        etablissement: 'Institut Supérieur de Technologie',
        section: 'Informatique',
        adresse_ecole: '123 Avenue de la Science',
        categorie: 'Université',
        pays: 'RDC',
        ville_province: 'Kinshasa'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Récupérer les élèves par établissement
```javascript
// GET /api/eleves/etablissement/Institut%20Supérieur%20de%20Technologie
fetch('/api/eleves/etablissement/Institut%20Supérieur%20de%20Technologie')
.then(response => response.json())
.then(data => {
    if (data.success) {
        data.data.forEach(eleve => {
            console.log(`${eleve.nom} ${eleve.prenom} - ${eleve.section}`);
        });
    }
});
```

## 📝 Gestion des Interrogations

### Créer une nouvelle interrogation
```javascript
// POST /api/interrogations
fetch('/api/interrogations', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        titre: 'Interrogation de Mathématiques',
        description: 'Interrogation sur les équations du second degré',
        duree: 60,
        matiere: 'Mathématiques',
        niveau: 'Terminal'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Démarrer une interrogation
```javascript
// POST /api/interrogations/456/start
fetch('/api/interrogations/456/start', {
    method: 'POST'
})
.then(response => response.json())
.then(data => console.log(data));
```

### Récupérer les interrogations actives
```javascript
// GET /api/interrogations/active
fetch('/api/interrogations/active')
.then(response => response.json())
.then(data => {
    if (data.success) {
        data.data.forEach(interro => {
            console.log(`${interro.titre} - ${interro.nombre_questions} questions`);
        });
    }
});
```

## ❓ Gestion des Questions

### Créer une question
```javascript
// POST /api/questions
fetch('/api/questions', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        interrogation_id: 456,
        question: 'Quelle est la solution de l\'équation x² + 5x + 6 = 0 ?',
        type: 'choix_unique',
        points: 2,
        options: {
            choix: ['x = -2 et x = -3', 'x = 2 et x = 3', 'x = -1 et x = -6'],
            correcte: 'x = -2 et x = -3'
        },
        temps_estime: 120
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Ajouter des questions à une interrogation
```javascript
// POST /api/interrogations/456/questions
fetch('/api/interrogations/456/questions', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        questions: [
            {
                question: 'Question 1',
                type: 'choix_unique',
                points: 1,
                options: {
                    choix: ['A', 'B', 'C'],
                    correcte: 'A'
                }
            },
            {
                question: 'Question 2',
                type: 'choix_multiple',
                points: 2,
                options: {
                    choix: ['A', 'B', 'C', 'D'],
                    correctes: ['A', 'C']
                }
            }
        ]
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 📊 Gestion des Résultats

### Soumettre un résultat
```javascript
// POST /api/resultats/submit
fetch('/api/resultats/submit', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        interrogation_id: 456,
        reponses: {
            1: 'A',
            2: ['A', 'C'],
            3: 'Réponse libre'
        },
        temps_utilise: 45
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(`Score obtenu: ${data.data.score}`);
    }
});
```

### Récupérer les résultats d'un élève
```javascript
// GET /api/resultats/eleve/789
fetch('/api/resultats/eleve/789')
.then(response => response.json())
.then(data => {
    if (data.success) {
        data.data.forEach(resultat => {
            console.log(`${resultat.interrogation_titre}: ${resultat.score}%`);
        });
    }
});
```

## 🔍 Recherche et Statistiques

### Rechercher des utilisateurs
```javascript
// GET /api/search/utilisateurs?q=john
fetch('/api/search/utilisateurs?q=john')
.then(response => response.json())
.then(data => console.log(data));
```

### Récupérer les statistiques globales
```javascript
// GET /api/stats/globales
fetch('/api/stats/globales')
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(`Total utilisateurs: ${data.data.total_utilisateurs}`);
        console.log(`Total interrogations: ${data.data.total_interrogations}`);
        console.log(`Score moyen: ${data.data.score_moyen}%`);
    }
});
```

## 📁 Gestion des Fichiers

### Upload d'avatar
```javascript
// POST /api/upload/avatar
const formData = new FormData();
formData.append('avatar', fileInput.files[0]);

fetch('/api/upload/avatar', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Avatar uploadé:', data.data.url);
    }
});
```

## 📧 Notifications et Messages

### Envoyer une notification
```javascript
// POST /api/notifications/send
fetch('/api/notifications/send', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        destinataire_id: 123,
        titre: 'Nouvelle interrogation disponible',
        message: 'Une nouvelle interrogation de mathématiques est disponible.',
        type: 'info'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 📈 Export de Données

### Exporter les résultats
```javascript
// GET /api/export/resultats
fetch('/api/export/resultats')
.then(response => response.blob())
.then(blob => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'resultats.xlsx';
    a.click();
});
```

## 🔧 Configuration

### Mettre à jour la configuration
```javascript
// PUT /api/config/system
fetch('/api/config/system', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        site_name: 'MC-LEGENDE',
        maintenance_mode: false,
        max_file_size: 5242880
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 🛡️ Gestion des Erreurs

### Exemple de gestion d'erreur
```javascript
fetch('/api/utilisateurs/999')
.then(response => {
    if (!response.ok) {
        return response.json().then(error => {
            throw new Error(error.error || 'Erreur inconnue');
        });
    }
    return response.json();
})
.then(data => {
    console.log('Succès:', data);
})
.catch(error => {
    console.error('Erreur:', error.message);
    // Afficher un message d'erreur à l'utilisateur
    showErrorMessage(error.message);
});
```

## 📱 Utilisation avec Axios

### Configuration Axios
```javascript
import axios from 'axios';

// Configuration de base
axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Intercepteur pour ajouter le token
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Intercepteur pour gérer les erreurs
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/connexion';
        }
        return Promise.reject(error);
    }
);
```

### Exemple avec Axios
```javascript
// Récupérer les élèves
const getEleves = async () => {
    try {
        const response = await axios.get('/api/eleves');
        return response.data.data;
    } catch (error) {
        console.error('Erreur:', error.response.data.error);
        throw error;
    }
};

// Créer un élève
const createEleve = async (eleveData) => {
    try {
        const response = await axios.post('/api/eleves', eleveData);
        return response.data.data;
    } catch (error) {
        console.error('Erreur:', error.response.data.error);
        throw error;
    }
};
```

---

## 📋 Codes de Statut HTTP

- **200** : Succès
- **201** : Créé avec succès
- **400** : Requête invalide
- **401** : Non autorisé
- **404** : Ressource non trouvée
- **409** : Conflit (ressource existe déjà)
- **422** : Données de validation invalides
- **500** : Erreur serveur interne

## 🔐 Sécurité

- Toutes les routes API nécessitent une authentification (sauf login/register)
- Utilisez HTTPS en production
- Validez toutes les données d'entrée
- Sanitisez les données avant stockage
- Utilisez des tokens JWT pour l'authentification 