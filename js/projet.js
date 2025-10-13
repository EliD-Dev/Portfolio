// Script pour la page de détail d'un projet

// Fonction pour obtenir le slug depuis l'URL
function getSlugFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('slug');
}

// Fonction pour trouver un projet par son slug
async function findProjetBySlug(slug) {
    const projets = await fetchProjets();
    return projets.find(projet => slugify(projet.titre) === slug);
}

// Fonction pour afficher les détails du projet
function displayProjetDetail(projet) {
    const projetContent = document.getElementById('projet-content');
    
    if (!projet) {
        projetContent.innerHTML = `
            <div style="text-align: center; padding: 50px;">
                <h2>Projet non trouvé</h2>
                <p>Le projet que vous recherchez n'existe pas ou a été supprimé.</p>
                <p><a href="./index.html#projets" class="btn">Retour aux projets</a></p>
            </div>
        `;
        return;
    }
    
    // Mettre à jour les métadonnées SEO pour ce projet
    const pageConfig = {
        title: `${projet.titre} - EliDev`,
        description: projet.description
    };
    updatePageMetadata(pageConfig);
    
    const imagePath = projet.imagePath.startsWith('http') 
        ? projet.imagePath 
        : `https://portfolio-api.eliot-dubreuil.workers.dev${projet.imagePath}`;
    
    projetContent.innerHTML = `
        <article class="projet-article">
            <header class="projet-header">
                <h1>${projet.titre}</h1>
                <div class="projet-meta">
                    <span class="projet-type"><strong>Type :</strong> ${projet.type}</span>
                    <span class="projet-date"><strong>Date :</strong> ${formatDate(projet.date)}</span>
                </div>
            </header>
            
            <div class="projet-image">
                <img src="${imagePath}" alt="${projet.titre}">
            </div>
            
            <div class="projet-description">
                <p>${projet.description}</p>
            </div>
            
            ${projet.url ? `
                <div class="projet-link">
                    <a href="${projet.url}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                        <i class="fa-solid fa-external-link-alt"></i> Voir le site
                    </a>
                </div>
            ` : ''}
            
            ${projet.contenu ? `
                <div class="projet-contenu">
                    ${projet.contenu}
                </div>
            ` : ''}
            
            <div class="projet-navigation">
                <a href="./index.html#projets" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Retour aux projets
                </a>
            </div>
        </article>
    `;
}

// Fonction pour formater la date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('fr-FR', options);
}

// Initialisation de la page
async function initProjetPage() {
    try {
        // Charger les composants
        await loadComponent('header');
        await loadComponent('footer');
        
        // Récupérer le slug depuis l'URL
        const slug = getSlugFromUrl();
        
        if (!slug) {
            displayProjetDetail(null);
            hideLoader();
            return;
        }
        
        // Trouver et afficher le projet
        const projet = await findProjetBySlug(slug);
        displayProjetDetail(projet);
        
        // Masquer le loader et afficher l'animation
        hideLoader();
        showPageLoadAnimation();
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de la page:', error);
        hideLoader();
    }
}

// Lancer l'initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', initProjetPage);
