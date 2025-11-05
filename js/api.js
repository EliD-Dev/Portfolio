// Gestion des appels API

// Fonction pour récupérer tous les projets
async function fetchProjets() {
    try {
        const response = await fetch(`${CONFIG.API_BASE_URL}/projets`);
        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des projets');
        }
        const projets = await response.json();
        
        // Trier par date décroissante
        projets.sort((a, b) => new Date(b.date) - new Date(a.date));
        
        return projets;
    } catch (error) {
        console.error('Erreur API projets:', error);
        return [];
    }
}

// Fonction pour récupérer un projet spécifique par ID
async function fetchProjetById(id) {
    try {
        const response = await fetch(`${CONFIG.API_BASE_URL}/projets/${id}`);
        if (!response.ok) {
            throw new Error('Erreur lors de la récupération du projet');
        }
        return await response.json();
    } catch (error) {
        console.error('Erreur API projet:', error);
        return null;
    }
}

// Fonction pour récupérer toutes les compétences
async function fetchCompetences() {
    try {
        const response = await fetch(`${CONFIG.API_BASE_URL}/competences`);
        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des compétences');
        }
        return await response.json();
    } catch (error) {
        console.error('Erreur API compétences:', error);
        return [];
    }
}

// Fonction pour afficher les projets dans le DOM
function displayProjets(projets) {
    const projetsList = document.querySelector('.projets-list');
    if (!projetsList) { return; }
    
    projetsList.innerHTML = '';
    
    projets.forEach(projet => {
        const projetDiv = document.createElement('div');
        projetDiv.className = 'projet';
        
        const imagePath = projet.imagePath.startsWith('http') 
            ? projet.imagePath 
            : `https://portfolio-api.eli-dev.fr${projet.imagePath}`;
        
        projetDiv.innerHTML = `
            <img src="${imagePath}" alt="${projet.titre}">
            <h4>${projet.titre}</h4>
            <p><strong>Type :</strong> ${projet.type}</p>
            <p>${projet.description}</p>
            <p><a href="./projet.html?slug=${slugify(projet.titre)}">Voir le projet</a></p>
        `;
        
        projetsList.appendChild(projetDiv);
    });
}

// Fonction pour afficher les compétences dans le DOM
function displayCompetences(competences) {
    const competencesList = document.querySelector('.competences-list');
    if (!competencesList) { return; }
    
    competencesList.innerHTML = '';
    
    // Grouper par type
    const groupes = {};
    competences.forEach(competence => {
        let {type} = competence;
        
        // Transformer BDD en "Base de donnée"
        if (type === 'BDD') {
            type = 'Base de donnée';
        }
        
        if (!groupes[type]) {
            groupes[type] = [];
        }
        groupes[type].push(competence);
    });
    
    // Afficher les groupes
    Object.keys(groupes).forEach(type => {
        const typeDiv = document.createElement('div');
        typeDiv.className = 'competence-type';
        
        const typeTitle = document.createElement('h4');
        typeTitle.textContent = type;
        typeDiv.appendChild(typeTitle);
        
        const groupeDiv = document.createElement('div');
        groupeDiv.className = 'competence-groupe';
        
        groupes[type].forEach(competence => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'competence-item';
            
            const logoPath = competence.logoPath.startsWith('http') 
                ? competence.logoPath 
                : `https://portfolio-api.eli-dev.fr${competence.logoPath}`;
            
            itemDiv.innerHTML = `
                <img src="${logoPath}" alt="${competence.nom}" class="competence-logo">
                <p>${competence.nom}</p>
            `;
            
            groupeDiv.appendChild(itemDiv);
        });
        
        typeDiv.appendChild(groupeDiv);
        competencesList.appendChild(typeDiv);
    });
}
