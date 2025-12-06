// Script pour la page d'accueil

// Initialisation de la page
async function initIndexPage() {
    try {
        // Charger les composants
        await loadComponent('header');
        await loadComponent('footer');
        
        // Mettre à jour les métadonnées
        updatePageMetadata(CONFIG.pages.index);
        
        // Initialiser le footer (année et lien API)
        initFooter();
        
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

// Navigation des projets (carousel avec support tactile)
function initProjetsNavigation() {
    const projetsList = document.querySelector('.projets-list');
    const projetsContainer = document.querySelector('.projets-container');
    if (!projetsList || !projetsContainer) return;
    
    const navLeft = document.querySelector('.projet-nav-left');
    const navRight = document.querySelector('.projet-nav-right');
    const navLeftPhone = document.querySelector('.projet-nav-left-phone');
    const navRightPhone = document.querySelector('.projet-nav-right-phone');

    let currentIndex = 0;
    let maxProjetHeight = 0;
    
    // Nombre de projets à décaler lors de la navigation
    const NAVIGATION_STEP = 1;
    
    // Variables pour le swipe tactile
    let touchStartX = 0;
    let touchEndX = 0;
    let isDragging = false;
    let startTranslate = 0;
    const SWIPE_THRESHOLD = 50; // Distance minimale pour déclencher un swipe

    function getProjets() {
        return Array.from(projetsList.querySelectorAll('.projet'));
    }
    
    function getProjetWidth() {
        const projets = getProjets();
        if (projets.length === 0) return 0;
        const style = window.getComputedStyle(projetsList);
        const gap = parseFloat(style.gap) || 24;
        return projets[0].offsetWidth + gap;
    }

    function calculateGlobalMaxHeight() {
        const projets = getProjets();
        if (projets.length === 0) return;
        
        projets.forEach(p => {
            p.style.minHeight = 'auto';
        });
        
        requestAnimationFrame(() => {
            maxProjetHeight = 0;
            projets.forEach(projet => {
                const height = projet.offsetHeight;
                if (height > maxProjetHeight) {
                    maxProjetHeight = height;
                }
            });
            
            if (maxProjetHeight > 0) {
                projets.forEach(projet => {
                    projet.style.minHeight = `${maxProjetHeight}px`;
                });
            }
            
            updateCarousel(false);
        });
    }
    
    function updateCarousel(animate = true) {
        const projets = getProjets();
        if (projets.length === 0) return;
        
        const itemsPerPage = getItemsPerPage();
        const maxIndex = Math.max(0, projets.length - itemsPerPage);
        
        // S'assurer que currentIndex est dans les limites
        currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));
        
        // Calculer le décalage
        const projetWidth = getProjetWidth();
        const translateX = -currentIndex * projetWidth;
        
        // Appliquer la transformation avec ou sans animation
        if (animate) {
            projetsList.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.1, 0.25, 1)';
        } else {
            projetsList.style.transition = 'none';
        }
        projetsList.style.transform = `translateX(${translateX}px)`;
        
        // Animation d'entrée pour les projets visibles (seulement au premier chargement)
        if (!projetsList.dataset.initialized) {
            projetsList.dataset.initialized = 'true';
        }
        
        // Gérer l'affichage des boutons
        updateNavButtons(projets.length, itemsPerPage);
    }
    
    function updateNavButtons(totalProjets, itemsPerPage) {
        const maxIndex = Math.max(0, totalProjets - itemsPerPage);
        
        // Boutons desktop - utiliser classList pour garder l'espace réservé
        if (navLeft) {
            navLeft.classList.toggle('hidden', currentIndex === 0);
        }
        if (navRight) {
            navRight.classList.toggle('hidden', currentIndex >= maxIndex);
        }
        
        // Boutons mobile
        if (navLeftPhone) navLeftPhone.style.display = currentIndex === 0 ? 'none' : 'flex';
        if (navRightPhone) navRightPhone.style.display = currentIndex >= maxIndex ? 'none' : 'flex';
    }

    function navigateLeft() {
        currentIndex -= NAVIGATION_STEP;
        currentIndex = Math.max(currentIndex, 0);
        updateCarousel(true);
    }

    function navigateRight() {
        const projets = getProjets();
        const itemsPerPage = getItemsPerPage();
        const maxIndex = Math.max(0, projets.length - itemsPerPage);
        
        currentIndex += NAVIGATION_STEP;
        currentIndex = Math.min(currentIndex, maxIndex);
        updateCarousel(true);
    }
    
    // === GESTION DU SWIPE TACTILE ===
    function handleTouchStart(e) {
        touchStartX = e.touches[0].clientX;
        isDragging = true;
        startTranslate = -currentIndex * getProjetWidth();
        projetsList.style.transition = 'none';
    }
    
    function handleTouchMove(e) {
        if (!isDragging) return;
        
        touchEndX = e.touches[0].clientX;
        const diff = touchEndX - touchStartX;
        const projets = getProjets();
        const itemsPerPage = getItemsPerPage();
        const maxIndex = Math.max(0, projets.length - itemsPerPage);
        const projetWidth = getProjetWidth();
        
        // Limiter le déplacement avec résistance aux bords
        let newTranslate = startTranslate + diff;
        const minTranslate = -maxIndex * projetWidth;
        
        // Ajouter une résistance aux bords (effet élastique)
        if (newTranslate > 0) {
            newTranslate = newTranslate * 0.3;
        } else if (newTranslate < minTranslate) {
            newTranslate = minTranslate + (newTranslate - minTranslate) * 0.3;
        }
        
        projetsList.style.transform = `translateX(${newTranslate}px)`;
    }
    
    function handleTouchEnd() {
        if (!isDragging) return;
        isDragging = false;
        
        const diff = touchEndX - touchStartX;
        
        // Déterminer la direction du swipe
        if (Math.abs(diff) > SWIPE_THRESHOLD) {
            if (diff > 0) {
                // Swipe vers la droite = aller à gauche
                navigateLeft();
            } else {
                // Swipe vers la gauche = aller à droite
                navigateRight();
            }
        } else {
            // Pas assez de mouvement, revenir à la position actuelle
            updateCarousel(true);
        }
        
        // Réinitialiser
        touchStartX = 0;
        touchEndX = 0;
    }
    
    // Événements tactiles sur le conteneur
    projetsContainer.addEventListener('touchstart', handleTouchStart, { passive: true });
    projetsContainer.addEventListener('touchmove', handleTouchMove, { passive: true });
    projetsContainer.addEventListener('touchend', handleTouchEnd, { passive: true });
    projetsContainer.addEventListener('touchcancel', handleTouchEnd, { passive: true });

    // Événements pour les boutons desktop
    if (navLeft) navLeft.addEventListener('click', navigateLeft);
    if (navRight) navRight.addEventListener('click', navigateRight);
    
    // Événements pour les boutons mobile
    if (navLeftPhone) navLeftPhone.addEventListener('click', navigateLeft);
    if (navRightPhone) navRightPhone.addEventListener('click', navigateRight);

    // Recalculer lors du resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const projets = getProjets();
            if (projets.length === 0) return;
            
            const itemsPerPage = getItemsPerPage();
            const maxIndex = Math.max(0, projets.length - itemsPerPage);
            currentIndex = Math.min(currentIndex, maxIndex);
            
            maxProjetHeight = 0;
            calculateGlobalMaxHeight();
        }, 150);
    });

    // Initialisation
    calculateGlobalMaxHeight();
}

// Gestion du formulaire de contact - Déplacée dans contact.js
// (Voir js/contact.js pour l'implémentation complète)

// Lancer l'initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', initIndexPage);
