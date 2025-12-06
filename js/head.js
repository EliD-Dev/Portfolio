// Fonction pour injecter les métadonnées dans le <head>
function injectHeadMetadata(pageConfig) {
    // Normaliser l'URL canonique (sans www, sans index.html, sans query string)
    let currentUrl = globalThis.location.href.split('?')[0].split('#')[0];
    currentUrl = currentUrl.replace('://www.', '://'); // Enlever www
    currentUrl = currentUrl.replace(/\/index\.html$/, '/'); // Enlever index.html
    
    const currentYear = new Date().getFullYear();
    
    // Titre
    document.title = pageConfig.title;
    
    // Métadonnées de base
    const metaTags = {
        'description': pageConfig.description,
        'robots': 'index, follow',
        'revisit-after': '7 days',
        'language': 'fr',
        'author': 'EliDev',
        'publisher': 'EliDev',
        'copyright': `© ${currentYear} EliDev`,
        'keywords': pageConfig.keywords || 'portfolio, développeur, developer, développeur web, développeur full stack, développeur polyvalent, développeur freelance, Eliot Dubreuil, Eliot, Dubreuil, EliDev, Eli-Dev, Eli.Dev, Eli Dev, Eli, Dev, développeur JavaScript, développeur PHP, développeur Python, développeur Java, web developer, full stack developer, front-end developer, back-end developer, développeur front-end, développeur back-end, HTML, CSS, JavaScript, JS, TypeScript, React, Vue.js, Angular, Node.js, Express, PHP, Symfony, Laravel, Python, Django, Flask, Java, Spring Boot, SQL, MySQL, PostgreSQL, MongoDB, NoSQL, Git, GitHub, GitLab, Docker, Kubernetes, CI/CD, DevOps, API, REST, API REST, API RESTful, développement web, création site web, site internet, application web, web app, responsive design, mobile first, UX/UI, design responsive, développement responsive, SEO, référencement naturel, optimisation SEO, performance web, accessibilité web, WCAG, RGPD, sécurité web, Excel, VBA, Access, automatisation, scripts, portfolio professionnel, projets web, compétences techniques, freelance développeur, développeur indépendant, France, Paris, Île-de-France, développeur disponible, recrutement développeur, embauche développeur, mission freelance, projet web, création application, développement sur mesure, solution web, site vitrine, e-commerce, CMS, WordPress, Prestashop, Shopify, PWA, Progressive Web App, SPA, Single Page Application, microservices, architecture logicielle, tests unitaires, TDD, agile, scrum, Jira, VS Code, IntelliJ, développement moderne, technologies web, stack technique, GitHub Pages, hébergement web, déploiement continu, intégration continue, cloud computing, AWS, Azure, Google Cloud, Firebase, Netlify, Vercel'
    };
    
    // Open Graph
    const ogTags = {
        'og:url': currentUrl,
        'og:type': 'website',
        'og:title': pageConfig.title,
        'og:description': pageConfig.description,
        'og:image': './images/Logo_EliDev.webp'
    };
    
    // Twitter Card
    const twitterTags = {
        'twitter:card': 'summary_large_image',
        'twitter:image': './images/Logo_EliDev.webp',
        'twitter:image:alt': 'Image',
        'twitter:title': pageConfig.title,
        'twitter:description': pageConfig.description,
        'twitter:site': '@EliDev',
        'twitter:creator': '@EliDev'
    };

    // Injecter les métadonnées
    Object.entries({...metaTags, ...ogTags, ...twitterTags}).forEach(([key, value]) => {
        const isProperty = key.startsWith('og:') || key.startsWith('twitter:');
        const attribute = isProperty ? 'property' : 'name';
        
        let meta = document.querySelector(`meta[${attribute}="${key}"]`);
        if (!meta) {
            meta = document.createElement('meta');
            meta.setAttribute(attribute, key);
            document.head.appendChild(meta);
        }
        meta.setAttribute('content', value);
    });
    
    // Canonical link
    let canonical = document.querySelector('link[rel="canonical"]');
    if (!canonical) {
        canonical = document.createElement('link');
        canonical.setAttribute('rel', 'canonical');
        document.head.appendChild(canonical);
    }
    canonical.setAttribute('href', currentUrl);
    
    // Schema.org JSON-LD - WebSite
    let schemaScript = document.querySelector('script[type="application/ld+json"][data-schema="website"]');
    if (!schemaScript) {
        schemaScript = document.createElement('script');
        schemaScript.type = 'application/ld+json';
        schemaScript.setAttribute('data-schema', 'website');
        document.head.appendChild(schemaScript);
    }
    
    const websiteSchema = {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Portfolio EliDev",
        "url": "https://eli-dev.fr/",
        "description": pageConfig.description,
        "image": "https://eli-dev.fr/images/Logo_EliDev.webp",
        "author": {
            "@type": "Person",
            "name": "Eliot Dubreuil"
        }
    };
    
    schemaScript.textContent = JSON.stringify(websiteSchema, null, 2);
    
    // Schema.org JSON-LD - Person (pour SEO local/professionnel)
    let personSchemaScript = document.querySelector('script[type="application/ld+json"][data-schema="person"]');
    if (!personSchemaScript) {
        personSchemaScript = document.createElement('script');
        personSchemaScript.type = 'application/ld+json';
        personSchemaScript.setAttribute('data-schema', 'person');
        document.head.appendChild(personSchemaScript);
    }
    
    const personSchema = {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "Eliot Dubreuil",
        "alternateName": "EliDev",
        "url": "https://eli-dev.fr/",
        "image": "https://eli-dev.fr/images/Logo_EliDev.webp",
        "jobTitle": "Développeur Web Full Stack",
        "description": "Développeur web freelance spécialisé en JavaScript, PHP, Python et Java. Création de sites web et applications sur mesure.",
        "knowsAbout": [
            "JavaScript", "TypeScript", "React", "Vue.js", "Node.js",
            "PHP", "Symfony", "Laravel",
            "Python", "Django", "Flask",
            "Java", "Spring Boot",
            "HTML", "CSS", "SQL", "MongoDB",
            "Git", "Docker", "CI/CD"
        ],
        "sameAs": [
            "https://github.com/EliD-Dev"
        ],
        "worksFor": {
            "@type": "Organization",
            "name": "EliDev (Freelance)"
        }
    };
    
    personSchemaScript.textContent = JSON.stringify(personSchema, null, 2);
}

// Variable globale pour stocker l'ancre en attente
let pendingAnchorScroll = null;

// Fonction pour initialiser le système de scroll d'ancrage
function initAnchorScrollSystem() {
    const hash = window.location.hash;
    
    if (hash) {
        // Désactiver temporairement le smooth scroll
        document.documentElement.style.scrollBehavior = 'auto';
        
        // Empêcher le navigateur de faire son propre scroll
        history.scrollRestoration = 'manual';
        
        // Forcer le scroll en haut de la page immédiatement
        window.scrollTo(0, 0);
        
        // Stocker l'ancre pour la traiter après le chargement
        pendingAnchorScroll = hash;
    }
}

// Fonction pour effectuer le scroll vers l'ancre après le chargement
function performPendingAnchorScroll() {
    if (!pendingAnchorScroll) return;
    
    const targetElement = document.querySelector(pendingAnchorScroll);
    
    if (targetElement) {
        // Si c'est un H3, prendre la section parente
        let scrollTarget = targetElement;
        if (targetElement.tagName === 'H3') {
            scrollTarget = targetElement.closest('section');
        }
        
        if (scrollTarget) {
            // Calculer la position exacte (sans marge)
            const headerHeight = document.querySelector('nav')?.offsetHeight || 0;
            // Scroller plus vers le bas de 10% de la hauteur de l'écran pour compenser
            const targetPosition = scrollTarget.offsetTop - headerHeight + window.innerHeight * 0.5;
            
            // Scroller instantanément vers la position
            window.scrollTo(0, targetPosition);
            
            // Réactiver le smooth scroll après
            setTimeout(() => {
                document.documentElement.style.scrollBehavior = 'smooth';
            }, 100);
        }
    }
    
    // Nettoyer la variable
    pendingAnchorScroll = null;
}

// Initialiser au chargement
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAnchorScrollSystem);
} else {
    initAnchorScrollSystem();
}
