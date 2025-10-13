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
        'keywords': pageConfig.keywords || 'portfolio, développeur, polyvalent, développeur polyvalent, full stack, web developer, JS, JavaScript, PHP, HTML, CSS, Java, Python, Excel, VBA, SQL, Access, Symfony, Docker, Git, GitHub, Eliot Dubreuil, Eliot, Dubreuil, Eli, Dev, EliDev'
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
