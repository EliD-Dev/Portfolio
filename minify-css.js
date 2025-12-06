// Script simple pour minifier le CSS
const fs = require('fs');
const path = require('path');

function minifyCSS(css) {
    return css
        // Supprimer les commentaires
        .replace(/\/\*[\s\S]*?\*\//g, '')
        // Supprimer les espaces multiples
        .replace(/\s+/g, ' ')
        // Supprimer les espaces avant et après les symboles
        .replace(/\s*([{}:;,>+~])\s*/g, '$1')
        // Supprimer les points-virgules avant les accolades
        .replace(/;}/g, '}')
        // Supprimer les espaces en début et fin
        .trim();
}

// Lire le fichier CSS
const cssPath = path.join(__dirname, 'styles', 'global.css');
const css = fs.readFileSync(cssPath, 'utf8');

// Minifier
const minified = minifyCSS(css);

// Sauvegarder
const minifiedPath = path.join(__dirname, 'styles', 'global.min.css');
fs.writeFileSync(minifiedPath, minified, 'utf8');

console.log(`✅ CSS minifié : ${css.length} bytes → ${minified.length} bytes`);
console.log(`💾 Économie : ${((1 - minified.length / css.length) * 100).toFixed(1)}%`);
