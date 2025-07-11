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
    <section class="projets">
        <h3>Mes Projets</h3>
        <div>
            <?php
            // Exemple de récupération de projets depuis la base de données
            $stmt = $connexionDB->prepare("SELECT * FROM Projets LIMIT 6");
            $stmt->execute();
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($projets as $projet) {
                echo '<div class="projet">';
                echo '  <img src="' . htmlspecialchars($projet['imagePath']) . '" alt="' . htmlspecialchars($projet['titre']) . '">';
                echo '  <h4>' . htmlspecialchars($projet['titre']) . '</h4>';
                echo '  <p>' . htmlspecialchars($projet['description']) . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </section>
    <section class="compétences">
        <h3>Mes Compétences</h3>
        <ul>
            <li>JavaScript</li>
            <li>PHP</li>
            <li>HTML/CSS</li>
            <li>Python</li>
            <li>SQL</li>
            <li>Symfony</li>
            <li>Docker</li>
            <li>Git/GitHub</li>
        </ul>
    </section>
    <section class="contact">
        <h3>Contact</h3>
        <p>Pour toute question ou collaboration, n'hésitez pas à me contacter par email ou via mes réseaux sociaux.</p>
        <div class="contact-links">
            <a href="mailto:eliotdubreuil@gmail.com">eliotdubreuil@gmail.com</a>
        </div>
    </section>
</main>

<?php require 'Footer.php'; ?>