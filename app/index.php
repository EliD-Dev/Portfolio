<?php require 'Header.php'; ?>

<main>
    <div class="présentation">
        <div class="titres">
            <h1>Eliot Dubreuil</h1>
            <h2>Développeur Polyvalent</h2>
        </div>
        <div class="infosMailetGithub">
            <div><a href="mailto:eliotdubreuil@gmail.com"><i class="fa-solid fa-envelope animated-icon"></i></a> <a href="mailto:eliotdubreuil@gmail.com">eliotdubreuil@gmail.com</a></div>
            <div><a href="https://github.com/EliD-Dev" target="_blank"><i class="fa-brands fa-github animated-icon"></i></a> <a href="https://github.com/EliD-Dev" target="_blank">Eli-Dev</a></div>
        </div>
    </div>
    <section>
        <h3>À propos</h3>
        <p>Je suis Eliot Dubreuil, un développeur polyvalent passionné par la création de solutions web innovantes.</p>
        <p>Explorez mes projets, découvrez mes compétences et n'hésitez pas à me contacter pour toute collaboration ou question.</p>
    </section>
    <section class="projets" id="projets">
        <h3>Mes Projets</h3>
        <div class="projets-container">
            <button class="projet-nav-left" style="display: none;">&lt;</button>
            <div class="projets-list">
                <?php
                // Exemple de récupération de projets depuis la base de données
                $requete = $connexionDB->prepare("SELECT titre, imagePath, description, type FROM Projets ORDER BY date DESC");
                $requete->execute();
                $projets = $requete->fetchAll(PDO::FETCH_ASSOC);
                $requete->closeCursor();

                foreach ($projets as $projet) {
                    echo '<div class="projet">';
                    echo '  <img src="' . htmlspecialchars($projet['imagePath']) . '" alt="' . htmlspecialchars($projet['titre']) . '">';
                    echo '  <h4>' . htmlspecialchars($projet['titre']) . '</h4>';
                    echo '  <p><strong>Type :</strong> ' . htmlspecialchars($projet['type']) . '</p>';
                    echo '  <p>' . htmlspecialchars($projet['description']) . '</p>';
                    echo '  <p><a href="./projet/' . urlencode(slugify($projet['titre'])) . '">Voir le projet</a></p>';
                    echo '</div>';
                }
                ?>
            </div>
            <button class="projet-nav-right" style="display: none;">&gt;</button>
        </div>
    </section>
    <section class="compétences" id="competences">
        <h3>Mes Compétences</h3>
        <div class="competences-list">
            <?php
            $requete = $connexionDB->prepare("SELECT nom, logoPath, type FROM Competences");
            $requete->execute();
            $competences = $requete->fetchAll(PDO::FETCH_ASSOC);
            $requete->closeCursor();

            // Grouper par type
            $groupes = [];
            foreach ($competences as $competence) {
                // si le type est BDD on le transforme en Base de données
                if ($competence['type'] === 'BDD') {
                    $competence['type'] = 'Base de donnée';
                }
                $groupes[$competence['type']][] = $competence;
            }

            // Afficher les groupes
            foreach ($groupes as $type => $liste) {
                echo '<div class="competence-type">';
                echo '<h4>' . htmlspecialchars($type) . '</h4>';
                echo '<div class="competence-groupe">';
                foreach ($liste as $competence) {
                    echo '<div class="competence-item">';
                    echo '  <img src="' . htmlspecialchars($competence['logoPath']) . '" alt="' . htmlspecialchars($competence['nom']) . '" class="competence-logo">';
                    echo '  <p>' . htmlspecialchars($competence['nom']) . '</p>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </section>
    <section class="contact" id="contact">
        <h3>Contact</h3>
        <div class="contact-container">
            <div class="contact-info">
                <p>Pour toute question ou collaboration, n'hésitez pas à me contacter par ce formulaire de contact ou par email.</p>
                <div class="infosMailetGithub MailetGithubmargin0">
                    <div><a href="mailto:eliotdubreuil@gmail.com"><i class="fa-solid fa-envelope animated-icon"></i></a> <a href="mailto:eliotdubreuil@gmail.com">eliotdubreuil@gmail.com</a></div>
                    <div><a href="https://github.com/EliD-Dev" target="_blank"><i class="fa-brands fa-github animated-icon"></i></a> <a href="https://github.com/EliD-Dev" target="_blank">Eli-Dev</a></div>
                </div>
            </div>
            
        
            <form id="contact-form" action="contact" method="post" enctype="multipart/form-data" class="contact-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div id="form-message" class="form-message"></div>
                <div class="form-group">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Sujet :</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message :</label>
                    <textarea id="message" name="message" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="pièce_jointe">Pièce jointe (optionnel) :</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="314572800">
                    <input type="file" id="pièce_jointe" name="pièce_jointe">
                </div>
                <div class="form-group cacher">
                    <label for="website">Site web :</label>
                    <input type="text" id="website" name="website">
                </div>
                <button type="submit" id="submit-button">Envoyer</button>
            </form>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projetsList = document.querySelector('.projets-list');
    const projets = Array.from(projetsList.children);
    const navLeft = document.querySelector('.projet-nav-left');
    const navRight = document.querySelector('.projet-nav-right');

    let currentIndex = 0;
    const itemsPerPage = getItemsPerPage();

    function getItemsPerPage() {
        if (window.innerWidth <= 710) {
            return 1;
        } else if (window.innerWidth <= 1000) {
            return 2;
        } else {
            return 3;
        }
    }

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

    function updateNavigation() {
        const itemsPerPage = getItemsPerPage();
        navLeft.style.display = currentIndex === 0 ? 'none' : 'inline-block';
        navRight.style.display = currentIndex + itemsPerPage >= projets.length ? 'none' : 'inline-block';
    }

    navLeft.addEventListener('click', function() {
        currentIndex -= getItemsPerPage();
        if (currentIndex < 0) currentIndex = 0;
        showProjects();
    });

    navRight.addEventListener('click', function() {
        currentIndex += getItemsPerPage();
        if (currentIndex >= projets.length) currentIndex = projets.length - getItemsPerPage();
        showProjects();
    });

    window.addEventListener('resize', function() {
        currentIndex = 0; // Réinitialiser à la première page lors du redimensionnement
        showProjects();
    });

    showProjects();
});
</script>

<script>
    document.getElementById('contact-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche la soumission normale du formulaire

        const form = event.target;
        const formData = new FormData(form);
        const messageDiv = document.getElementById('form-message');
        const submitButton = document.getElementById('submit-button');

        messageDiv.innerHTML = ''; // Efface les messages précédents
        messageDiv.style.display = 'none'; // Masque le message initial

        submitButton.disabled = true; // Désactive le bouton pour éviter les soumissions multiples
        submitButton.innerHTML = "<i class='fa-solid fa-spinner fa-spin'></i> Envoi..."; // Change le texte du bouton

        fetch('contact', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Afficher le message de réponse sur la page
            messageDiv.textContent = data;
            messageDiv.style.display = 'block';

            // Ajouter une classe en fonction du message
            if (data.includes("Erreur")) {
                messageDiv.classList.add('error');
                messageDiv.classList.remove('success');
            } else if (data.includes("succès")) {
                messageDiv.classList.add('success');
                messageDiv.classList.remove('error');
            }

            // Effacer le message après quelques secondes
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 25000);

            submitButton.disabled = false; // Réactive le bouton
            submitButton.textContent = "Envoyer"; // Restaure le texte du bouton

            // Réinitialiser le formulaire si le message a été envoyé avec succès
            if (data.includes("Message envoyé avec succès")) {
                form.reset();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            messageDiv.textContent = 'Erreur lors de l\'envoi du formulaire.';
            messageDiv.classList.add('error');
            messageDiv.classList.remove('success');
            messageDiv.style.display = 'block';

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 25000);

            submitButton.disabled = false; // Réactive le bouton
            submitButton.textContent = "Envoyer"; // Restaure le texte du bouton
        });
    });
</script>

<?php require 'Footer.php'; ?>