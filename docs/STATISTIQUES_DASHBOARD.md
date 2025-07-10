# 📊 Système de Statistiques Dashboard - MC-LEGENDE

## Vue d'ensemble

Le système de statistiques du dashboard permet de visualiser en temps réel les performances et l'activité de la plateforme MC-LEGENDE.

## 🎯 Fonctionnalités

### Widgets Principaux
- **Élèves inscrits** : Nombre total d'élèves dans le système
- **Interrogations actives** : Quiz en cours ou disponibles
- **Notifications non lues** : Messages en attente de lecture
- **Admins simples** : Nombre d'administrateurs de niveau 1

### Widgets Supplémentaires
- **Résultats soumis** : Total des quiz complétés
- **Score moyen global** : Moyenne des scores de tous les élèves
- **Élèves participants** : Nombre d'élèves ayant participé à au moins un quiz
- **Taux de participation** : Pourcentage d'élèves actifs

## 📈 Graphiques Disponibles

### 1. Répartition par Section
- **Type** : Graphique en anneau (doughnut)
- **Données** : Nombre d'élèves par section d'études
- **Utilisation** : Comprendre la distribution des élèves

### 2. Interrogations par Jour
- **Type** : Graphique linéaire
- **Données** : Nombre d'interrogations créées sur 7 jours
- **Utilisation** : Suivre l'activité de création de quiz

### 3. Distribution des Scores
- **Type** : Graphique en barres
- **Données** : Répartition des scores par tranches (0-49%, 50-59%, etc.)
- **Utilisation** : Analyser les performances des élèves

### 4. Matières Populaires
- **Type** : Graphique en barres horizontales
- **Données** : Top 5 des matières les plus populaires
- **Utilisation** : Identifier les matières préférées

## 📋 Tableaux de Données

### Performance par Établissement
- Nom de l'établissement
- Nombre d'élèves
- Nombre de résultats soumis
- Score moyen
- Score maximum

### Activité Récente (24h)
- Type d'activité (connexions, nouveaux résultats, nouvelles interrogations)
- Nombre d'occurrences
- Période de référence

## 🔧 Architecture Technique

### Backend (PHP)

#### StatsController
```php
// Méthodes principales
- getGlobales() : Statistiques générales
- getStatsEleves() : Statistiques des élèves
- getStatsInterrogations() : Statistiques des interrogations
- getStatsResultats() : Statistiques des résultats
- getStatsQuestions() : Statistiques des questions
- getStatsActivite() : Statistiques d'activité
- getDashboardStats() : Toutes les stats du dashboard (optimisé)
```

#### Routes API
```php
GET /api/stats/globales
GET /api/stats/eleves
GET /api/stats/interrogations
GET /api/stats/resultats
GET /api/stats/questions
GET /api/stats/activite
GET /api/stats/dashboard  // Nouveau endpoint optimisé
```

### Frontend (JavaScript)

#### DashboardStats Class
```javascript
class DashboardStats {
    constructor() // Initialisation
    loadStats() // Chargement des données
    updateWidgets() // Mise à jour des widgets
    updateCharts() // Mise à jour des graphiques
    updateTables() // Mise à jour des tableaux
}
```

## 🚀 Optimisations Implémentées

### 1. Requêtes Optimisées
- Utilisation de sous-requêtes pour éviter les jointures multiples
- Agrégation des données en une seule requête
- Indexation appropriée des colonnes fréquemment utilisées

### 2. Cache et Performance
- Rafraîchissement automatique toutes les 5 minutes
- Gestion des erreurs réseau
- Chargement asynchrone des données

### 3. Interface Utilisateur
- Graphiques interactifs avec Chart.js
- Responsive design
- Indicateurs de chargement
- Gestion des erreurs utilisateur

## 📊 Métriques Calculées

### Statistiques Globales
```sql
- Total élèves : COUNT(*) FROM eleves
- Interrogations actives : COUNT(*) FROM quiz WHERE statut = 'actif'
- Notifications non lues : COUNT(*) FROM notifications WHERE lue = 0
- Admins simples : COUNT(*) FROM utilisateurs WHERE role = 'admin_simple'
- Score moyen global : AVG(score) FROM resultats
```

### Répartition par Section
```sql
SELECT 
    section,
    COUNT(*) as total,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM eleves), 2) as pourcentage
FROM eleves 
WHERE section IS NOT NULL AND section != ''
GROUP BY section 
ORDER BY total DESC
```

### Distribution des Scores
```sql
SELECT 
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
GROUP BY tranche_score
```

## 🔄 Mise à Jour des Données

### Fréquence de Rafraîchissement
- **Automatique** : Toutes les 5 minutes
- **Manuel** : Rechargement de la page
- **Temps réel** : Pour les actions critiques (création de quiz, soumission de résultats)

### Triggers de Mise à Jour
- Nouvelle inscription d'élève
- Création d'une interrogation
- Soumission d'un résultat
- Modification d'un profil
- Connexion d'un administrateur

## 🛠️ Maintenance

### Logs et Monitoring
- Toutes les requêtes sont loggées
- Gestion des erreurs avec messages explicites
- Monitoring des performances des requêtes

### Sauvegarde des Données
- Les statistiques historiques sont conservées
- Possibilité d'export des données
- Archivage automatique des anciennes données

## 📝 Notes de Développement

### Bonnes Pratiques
1. **Sécurité** : Validation des entrées utilisateur
2. **Performance** : Optimisation des requêtes SQL
3. **Maintenabilité** : Code modulaire et documenté
4. **Évolutivité** : Architecture extensible

### Améliorations Futures
- [ ] Export PDF des statistiques
- [ ] Graphiques interactifs avancés
- [ ] Alertes automatiques sur seuils
- [ ] Comparaison temporelle des données
- [ ] Statistiques personnalisées par admin

---

*Documentation mise à jour le : 2025-01-03*
*Version : 1.0* 