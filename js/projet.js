// Script pour la page de détail d'un projet

// Fonction pour obtenir le slug depuis l'URL
function getSlugFromUrl() {
    // L'URL est du type /projet/slug-du-projet
    // On extrait le slug depuis le pathname
    const pathParts = globalThis.location.pathname.split('/').filter(part => part !== '');
    
    // Si on a au moins 2 parties (projet + slug)
    if (pathParts.length >= 2 && pathParts[0] === 'projet') {
        console.log('[getSlugFromUrl] Slug extrait du pathname:', pathParts[1]);
        return pathParts[1];
    }
    
    // Fallback: chercher dans les query params (pour compatibilité)
    const urlParams = new URLSearchParams(globalThis.location.search);
    const slugFromQuery = urlParams.get('slug');
    console.log('[getSlugFromUrl] Slug depuis query params:', slugFromQuery);
    return slugFromQuery;
}

// Fonction pour trouver un projet par son slug
async function findProjetBySlug(slug) {
    console.log('[findProjetBySlug] Recherche du projet avec slug:', slug);
    const projets = await fetchProjets();
    console.log('[findProjetBySlug] Projets récupérés:', projets.length);
    
    if (projets.length > 0) {
        console.log('[findProjetBySlug] Slugs disponibles:', projets.map(p => ({
            titre: p.titre,
            slug: slugify(p.titre)
        })));
    }
    
    const projet = projets.find(projet => slugify(projet.titre) === slug);
    console.log('[findProjetBySlug] Projet trouvé:', projet ? projet.titre : 'AUCUN');
    return projet;
}

// Fonction pour afficher les détails du projet
function displayProjetDetail(projet) {
    const projetContent = document.getElementById('projet-content');
    
    if (!projet) {
        projetContent.innerHTML = `
            <div style="text-align: center; padding: 50px;">
                <h2>Projet non trouvé</h2>
                <p>Le projet que vous recherchez n'existe pas ou a été supprimé.</p>
                <p><a href="/#projets" class="btn">Retour aux projets</a></p>
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
    
    const imagePath = `${CONFIG.API_DOMAIN}${projet.imagePath}`;
    
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
                <img src="${imagePath}" alt="${projet.titre}" loading="eager" fetchpriority="high" width="800" height="600">
            </div>
            
            <div class="projet-description">
                <p>${projet.description}</p>
            </div>
            
            ${projet.url ? `
                <div class="projet-link">
                    <a href="${projet.url}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                        <svg class="icon-svg" viewBox="0 0 512 512"><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z"/></svg> Voir le site
                    </a>
                </div>
            ` : ''}
            
            ${projet.contenu ? `
                <div class="projet-contenu">
                    ${projet.contenu}
                </div>
            ` : ''}
            
            <div class="projet-navigation">
                <a href="/#projets" class="btn-secondary">
                    <svg class="icon-svg" viewBox="0 0 448 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg> Retour aux projets
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
        console.log('[initProjetPage] Démarrage...');
        
        // Charger les composants
        await loadComponent('header');
        await loadComponent('footer');
        
        // Initialiser le footer (année et lien API)
        initFooter();
        
        // Récupérer le slug depuis l'URL
        const slug = getSlugFromUrl();
        console.log('[initProjetPage] Slug récupéré depuis l\'URL:', slug);
        
        if (!slug) {
            console.warn('[initProjetPage] Aucun slug trouvé dans l\'URL');
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
