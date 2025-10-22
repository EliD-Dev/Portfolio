// Fonction pour injecter les métadonnées dans le <head>
function injectHeadMetadata(pageConfig) {
    const currentUrl = window.location.href.split('?')[0];
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
    
    // Schema.org JSON-LD
    let schemaScript = document.querySelector('script[type="application/ld+json"]');
    if (!schemaScript) {
        schemaScript = document.createElement('script');
        schemaScript.type = 'application/ld+json';
        document.head.appendChild(schemaScript);
    }
    
    const schemaData = {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Portfolio EliDev",
        "url": currentUrl,
        "description": pageConfig.description,
        "image": "./images/Logo_EliDev.webp"
    };
    
    schemaScript.textContent = JSON.stringify(schemaData, null, 2);
}
