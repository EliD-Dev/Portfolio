// Script pour la page d'accueil

// Initialisation de la page
async function initIndexPage() {
    try {
        // Charger les composants
        await loadComponent('header');
        await loadComponent('footer');
        
        // Mettre à jour les métadonnées
        updatePageMetadata(CONFIG.pages.index);
        
        // Charger les projets et compétences en parallèle
        const [projets, competences] = await Promise.all([
            fetchProjets(),
            fetchCompetences()
        ]);
        
        displayProjets(projets);
        displayCompetences(competences);
        
        // Initialiser la navigation des projets
        initProjetsNavigation();
        
        // Masquer le loader et afficher l'animation
        hideLoader();
        showPageLoadAnimation();
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de la page:', error);
        hideLoader();
    }
}

// Navigation des projets (carousel)
function initProjetsNavigation() {
    const projetsList = document.querySelector('.projets-list');
    const projets = Array.from(projetsList.children);
    const navLeft = document.querySelector('.projet-nav-left');
    const navRight = document.querySelector('.projet-nav-right');

    let currentIndex = 0;

    function showProjects() {
        const itemsPerPage = getItemsPerPage();
        projets.forEach((projet, index) => {
            if (index >= currentIndex && index < currentIndex + itemsPerPage) {
                projet.style.display = 'block';
            } else {
                projet.style.display = 'none';
            }
        });

        navLeft.style.display = currentIndex === 0 ? 'none' : 'inline-block';
        navRight.style.display = currentIndex + itemsPerPage >= projets.length ? 'none' : 'inline-block';
    }

    navLeft.addEventListener('click', function() {
        currentIndex -= getItemsPerPage();
        currentIndex = Math.max(currentIndex, 0);
        showProjects();
    });

    navRight.addEventListener('click', function() {
        currentIndex += getItemsPerPage();
        if (currentIndex >= projets.length) { currentIndex = projets.length - getItemsPerPage(); }
        showProjects();
    });

    window.addEventListener('resize', function() {
        currentIndex = 0;
        showProjects();
    });

    showProjects();
}

// Gestion du formulaire de contact - Déplacée dans contact.js
// (Voir js/contact.js pour l'implémentation complète)

// Lancer l'initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', initIndexPage);
