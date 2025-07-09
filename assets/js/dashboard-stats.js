/**
 * Dashboard Statistics Manager
 * Gère le chargement et l'affichage des statistiques du dashboard
 */
class DashboardStats {
    constructor() {
        this.charts = {};
        this.statsData = null;
        this.init();
    }

    /**
     * Initialisation du dashboard
     */
    init() {
        this.loadStats();
        this.setupRefreshInterval();
    }

    /**
     * Charger les statistiques depuis l'API
     */
    async loadStats() {
        try {
            const response = await fetch('/api/stats/dashboard');
            const data = await response.json();

            if (data.success) {
                this.statsData = data.data;
                this.updateWidgets();
                this.updateCharts();
                this.updateTables();
            } else {
                console.error('Erreur lors du chargement des stats:', data.message);
            }
        } catch (error) {
            console.error('Erreur réseau:', error);
            this.showError('Erreur de connexion au serveur');
        }
    }

    /**
     * Mettre à jour les widgets (petites boîtes)
     */
    updateWidgets() {
        const widgets = this.statsData.widgets;

        // Widgets principaux
        this.updateWidget('total-eleves', widgets.total_eleves || 0);
        this.updateWidget('total-interros', widgets.total_interros_actives || 0);
        this.updateWidget('total-notifications', widgets.total_notifications_non_lues || 0);
        this.updateWidget('total-admins', widgets.total_admins_simples || 0);

        // Widgets supplémentaires
        this.updateWidget('total-resultats', widgets.total_resultats || 0);
        this.updateWidget('score-moyen', (widgets.score_moyen_global || 0).toFixed(1) + '%');
        this.updateWidget('eleves-participants', widgets.eleves_ayant_participe || 0);
    }

    /**
     * Mettre à jour un widget spécifique
     */
    updateWidget(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value;
        }
    }

    /**
     * Mettre à jour tous les graphiques
     */
    updateCharts() {
        this.updateSectionChart();
        this.updateInterroChart();
        this.updateScoreChart();
        this.updateMatiereChart();
        this.updateActiviteChart();
    }

    /**
     * Graphique de répartition par section
     */
    updateSectionChart() {
        const canvas = document.getElementById('sectionChart');
        if (!canvas || !this.statsData.sections) return;

        const data = this.statsData.sections;
        const labels = data.map(s => s.section);
        const values = data.map(s => s.total);
        const colors = this.generateColors(labels.length);

        this.createChart(canvas, 'doughnut', {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const data = context.dataset.data;
                            const total = data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        });
    }

    /**
     * Graphique des interrogations par jour
     */
    updateInterroChart() {
        const canvas = document.getElementById('interroChart');
        if (!canvas || !this.statsData.interro_stats) return;

        const data = this.statsData.interro_stats.reverse(); // Inverser pour ordre chronologique
        const labels = data.map(d => this.formatDate(d.jour));
        const values = data.map(d => d.total);

        this.createChart(canvas, 'line', {
            labels: labels,
            datasets: [{
                label: 'Interrogations créées',
                data: values,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.1)',
                fill: true,
                tension: 0.4
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        });
    }

    /**
     * Graphique de distribution des scores
     */
    updateScoreChart() {
        const canvas = document.getElementById('scoreChart');
        if (!canvas || !this.statsData.score_stats) return;

        const data = this.statsData.score_stats;
        const labels = data.map(s => s.tranche_score);
        const values = data.map(s => s.total);

        this.createChart(canvas, 'bar', {
            labels: labels,
            datasets: [{
                label: "Nombre d'élèves",
                data: values,
                backgroundColor: '#36b9cc',
                borderColor: '#2a96a8',
                borderWidth: 1
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        });
    }

    /**
     * Graphique des matières populaires
     */
    updateMatiereChart() {
        const canvas = document.getElementById('matiereChart');
        if (!canvas || !this.statsData.matieres_populaires) return;

        const data = this.statsData.matieres_populaires;
        const labels = data.map(m => m.matiere);
        const values = data.map(m => m.nombre_participations);

        this.createChart(canvas, 'horizontalBar', {
            labels: labels,
            datasets: [{
                label: 'Participations',
                data: values,
                backgroundColor: '#28a745',
                borderColor: '#1e7e34',
                borderWidth: 1
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        });
    }

    /**
     * Graphique d'activité récente
     */
    updateActiviteChart() {
        const canvas = document.getElementById('activiteChart');
        if (!canvas || !this.statsData.activite_recente) return;

        const data = this.statsData.activite_recente;
        const labels = data.map(a => a.type_activite);
        const values = data.map(a => a.total);

        this.createChart(canvas, 'pie', {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#ffc107', '#28a745', '#17a2b8']
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false
        });
    }

    /**
     * Créer un graphique Chart.js
     */
    createChart(canvas, type, data, options = {}) {
        const ctx = canvas.getContext('2d');

        // Détruire le graphique existant s'il y en a un
        if (this.charts[canvas.id]) {
            this.charts[canvas.id].destroy();
        }

        this.charts[canvas.id] = new Chart(ctx, {
            type: type,
            data: data,
            options: options
        });
    }

    /**
     * Mettre à jour les tableaux de données
     */
    updateTables() {
        this.updatePerformanceTable();
        this.updateActiviteTable();
    }

    /**
     * Tableau de performance par établissement
     */
    updatePerformanceTable() {
        const tableBody = document.getElementById('performanceTableBody');
        if (!tableBody || !this.statsData.performance_etablissements) return;

        tableBody.innerHTML = '';

        this.statsData.performance_etablissements.forEach(etab => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${etab.etablissement}</td>
                <td>${etab.nombre_eleves}</td>
                <td>${etab.nombre_resultats}</td>
                <td>${(etab.score_moyen || 0).toFixed(1)}%</td>
                <td>${etab.score_max || 0}%</td>
            `;
            tableBody.appendChild(row);
        });
    }

    /**
     * Tableau d'activité récente
     */
    updateActiviteTable() {
        const tableBody = document.getElementById('activiteTableBody');
        if (!tableBody || !this.statsData.activite_recente) return;

        tableBody.innerHTML = '';

        this.statsData.activite_recente.forEach(activite => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${activite.type_activite}</td>
                <td>${activite.total}</td>
                <td>${this.getTimeAgo()}</td>
            `;
            tableBody.appendChild(row);
        });
    }

    /**
     * Générer des couleurs pour les graphiques
     */
    generateColors(count) {
        const colors = [
            '#007bff', '#28a745', '#ffc107', '#dc3545', '#6610f2',
            '#fd7e14', '#20c997', '#e83e8c', '#6f42c1', '#17a2b8'
        ];

        const result = [];
        for (let i = 0; i < count; i++) {
            result.push(colors[i % colors.length]);
        }
        return result;
    }

    /**
     * Formater une date
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit'
        });
    }

    /**
     * Obtenir le temps écoulé
     */
    getTimeAgo() {
        return '24h';
    }

    /**
     * Configurer l'intervalle de rafraîchissement
     */
    setupRefreshInterval() {
        // Rafraîchir les stats toutes les 5 minutes
        setInterval(() => {
            this.loadStats();
        }, 5 * 60 * 1000);
    }

    /**
     * Afficher une erreur
     */
    showError(message) {
        // Créer une notification d'erreur
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

        const container = document.querySelector('.content-wrapper');
        if (container) {
            container.insertBefore(alert, container.firstChild);
        }
    }
}

// Initialiser le dashboard quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    new DashboardStats();
});