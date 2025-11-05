// Configuration globale du site
const CONFIG = {
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
        }
    }
};

// Fonction pour slugifier un texte
function slugify(text) {
    return text
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

// Fonction pour charger les composants (header et footer)
async function loadComponent(componentName) {
    try {
        const response = await fetch(`./components/${componentName}.html`);
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
    
    // Mettre à jour l'année dans le footer
    const footerYear = document.getElementById('footer-year');
    if (footerYear) {
        if (new Date().getFullYear() !== 2025) {
            footerYear.textContent = '2025 - ' + new Date().getFullYear();
        }
        else {
            footerYear.textContent = '2025';
        }
    }
}

// Fonction pour obtenir le nombre d'items par page (responsive)
function getItemsPerPage() {
    if (window.innerWidth <= 710) {
        return 1;
    } else if (window.innerWidth <= 1000) {
        return 2;
    } else {
        return 3;
    }
}

// Fonction pour masquer le loader
function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'none';
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

    const projets = document.querySelectorAll('.projet');
    projets.forEach((projet, index) => {
        projet.classList.add('card-flip-in');
        projet.style.setProperty('--index', index % getItemsPerPage());
    });
}