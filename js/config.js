// Configuration globale du site
const CONFIG = {
    VERSION: '7.0000000005',
    API_DOMAIN: 'https://portfolio-api.eli-dev.fr',
    API_BASE_URL: 'https://portfolio-api.eli-dev.fr/api',
    
    // Configuration des pages pour le SEO
    pages: {
        index: {
            title: "Page d'accueil - Portfolio développeur polyvalent | EliDev",
            description: "Portfolio de développeur polyvalent, spécialisé dans la création d'outils en ligne pour l'édition d'images, la conversion de fichiers et le téléchargement de vidéos."
        },
        mentionsLegales: {
            title: "Mentions Légales - EliDev",
            description: "Mentions légales du site portfolio d'Eliot Dubreuil, développeur polyvalent."
        },
        projet: {
            title: "Projet - EliDev",
            description: "Détails du projet réalisé par Eliot Dubreuil, développeur polyvalent."
        },
        404: {
            title: "404 - Page non trouvée | EliDev",
            description: "La page que vous recherchez n'existe pas ou a été supprimée. Retournez à l'accueil du portfolio d'EliDev."
        }
    }
};

// Fonction pour slugifier un texte
function slugify(text) {
    return text
        .toString()
        .normalize('NFD')
        .replaceAll(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replaceAll(/[^a-z0-9 -]/g, '')
        .replaceAll(/\s+/g, '-')
        .replaceAll(/-+/g, '-');
}

// Fonction pour charger les composants (header et footer)
async function loadComponent(componentName) {
    try {
        const response = await fetch(`/components/${componentName}.html`);
        const html = await response.text();
        
        const placeholder = document.getElementById(`${componentName}-placeholder`);
        if (placeholder) {
            placeholder.outerHTML = html;
        }
    } catch (error) {
        console.error(`Erreur lors du chargement du composant ${componentName}:`, error);
    }
}

// Fonction pour mettre à jour les métadonnées SEO
function updatePageMetadata(pageConfig) {
    if (!pageConfig) {
        return;
    }
    
    // Injecter les métadonnées du head
    injectHeadMetadata(pageConfig);
}

// Fonction pour initialiser le footer (année et lien API)
function initFooter() {
    // Mettre à jour l'année dans le footer
    const footerYear = document.getElementById('footer-year');
    if (footerYear) {
        if (new Date().getFullYear() === 2025) {
            footerYear.textContent = '2025';
        }
        else {
            footerYear.textContent = '2025 - ' + new Date().getFullYear();
        }
    }

    // Mettre à jour le lien de l'API
    const apiLink = document.getElementById('api-link');
    if (apiLink) {
        apiLink.href = CONFIG.API_DOMAIN;
    }
}

// Fonction pour obtenir le nombre d'items par page (responsive)
// Cache la valeur de innerWidth pour éviter les forced reflows
let cachedInnerWidth = window.innerWidth;
let resizeTimeout;

// Utiliser requestAnimationFrame pour éviter les forced reflows
window.addEventListener('resize', () => {
    if (resizeTimeout) {
        cancelAnimationFrame(resizeTimeout);
    }
    resizeTimeout = requestAnimationFrame(() => {
        cachedInnerWidth = window.innerWidth;
    });
}, { passive: true });

function getItemsPerPage() {
    if (cachedInnerWidth <= 710) {
        return 1;
    } else if (cachedInnerWidth <= 1000) {
        return 2;
    } else {
        return 3;
    }
}

// Fonction pour masquer le loader
function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        // Attendre que le contenu soit bien rendu dans le DOM
        setTimeout(() => {
            // Effectuer le scroll d'ancrage en attente
            if (typeof performPendingAnchorScroll === 'function') {
                performPendingAnchorScroll();
            }
            
            // Masquer le loader
            loader.style.display = 'none';
        }, 500);
    }
}

// Fonction pour afficher l'animation de chargement de la page
function showPageLoadAnimation() {
    const main = document.querySelector('.index-main, main');
    if (main) {
        setTimeout(() => {
            main.classList.add('loaded');
        }, 200);
    }
}