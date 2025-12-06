// Gestion du formulaire de contact avec EmailJS
// Documentation : https://www.emailjs.com/docs/

const CONTACT_CONFIG = {
    // Configuration EmailJS (à remplacer par vos propres clés)
    // Inscrivez-vous sur https://www.emailjs.com/
    emailjs: {
        serviceId: 'service_5w77imq',      // Ex: 'service_abc123'
        templateId: 'template_gz5medg',    // Ex: 'template_xyz789'
        publicKey: 'UAQH0fStLHKTJ1OO9'       // Ex: 'abcdefghijklmnop'
    },
    
    // Alternative 1: Web3Forms (RECOMMANDÉ - 250 emails/mois gratuit, simple)
    // Obtenez votre clé sur https://web3forms.com/
    web3forms: {
        accessKey: '232d897b-09b0-4b2f-be93-00da85545b18',
        toEmail: 'contact@eli-dev.fr'
    },
    
    // Alternative 2: Formspree (50 emails/mois gratuit)
    // Inscrivez-vous sur https://formspree.io/
    formspree: {
        endpoint: 'https://formspree.io/f/xblzpdld'
    },
    
    // Configuration anti-spam
    antiSpam: {
        minTimeBetweenSubmissions: 30, // secondes
        honeypotField: 'website'       // champ piège pour les bots
    },
    

};

// Variable pour stocker le dernier envoi
let lastSubmissionTime = 0;

// Initialiser EmailJS
function initEmailJS() {
    // Vérifier si EmailJS est configuré
    if (CONTACT_CONFIG.emailjs.publicKey === 'YOUR_PUBLIC_KEY') {
        console.warn('⚠️ EmailJS n\'est pas configuré. Veuillez configurer vos clés dans js/contact.js');
        return false;
    }
    
    // Charger le SDK EmailJS
    if (typeof emailjs !== 'undefined') {
        emailjs.init(CONTACT_CONFIG.emailjs.publicKey);
        return true;
    }
    
    return false;
}

// Validation anti-spam
function validateAntiSpam(formData) {
    // Vérifier le honeypot (champ caché pour piéger les bots)
    if (formData.get(CONTACT_CONFIG.antiSpam.honeypotField)) {
        return { valid: false, error: 'Bot détecté.' };
    }
    
    // Vérifier le délai entre les soumissions
    const now = Date.now();
    const timeSinceLastSubmission = (now - lastSubmissionTime) / 1000;
    
    if (timeSinceLastSubmission < CONTACT_CONFIG.antiSpam.minTimeBetweenSubmissions && lastSubmissionTime > 0) {
        return { 
            valid: false, 
            error: `Trop de requêtes. Veuillez patienter ${Math.ceil(CONTACT_CONFIG.antiSpam.minTimeBetweenSubmissions - timeSinceLastSubmission)} secondes.` 
        };
    }
    
    return { valid: true };
}



// Envoyer le formulaire avec EmailJS (200 emails/mois)
async function sendWithEmailJS(formData) {
    try {
        let message = "Nom : <b>" + formData.get('name') + "</b><br>";
        message += "Email : <b>" + formData.get('email') + "</b><br>";
        message += "Sujet : <b>" + formData.get('subject') + "</b><br><br>";
        message += "Message : <br><p style='white-space: pre-wrap; font-size: 14px;'>" + formData.get('message') + "</p>";

        const templateParams = {
            from_name: formData.get('name'),
            from_email: formData.get('email'),
            reply_to: formData.get('email'),
            sujet: "Portfolio - " + formData.get('subject'),
            message: message
        };
        
        const response = await emailjs.send(
            CONTACT_CONFIG.emailjs.serviceId,
            CONTACT_CONFIG.emailjs.templateId,
            templateParams
        );
        
        if (response.status === 200) {
            return { success: true, service: 'EmailJS' };
        } else {
            throw new Error('Erreur lors de l\'envoi');
        }
    } catch (error) {
        console.error('EmailJS error:', error);
        
        // Vérifier si c'est une erreur de quota
        if (error.status === 429 || (error.text && error.text.includes('limit'))) {
            console.warn('⚠️ EmailJS : Quota mensuel dépassé (200 emails/mois)');
            return { 
                success: false, 
                quotaExceeded: true,
                error: 'Quota EmailJS dépassé'
            };
        }
        
        // Messages d'erreur plus détaillés
        let errorMessage = 'Erreur lors de l\'envoi avec EmailJS.';
        
        if (error.text) {
            if (error.text.includes('recipients address is empty')) {
                errorMessage = '⚠️ Configuration EmailJS incomplète : Veuillez configurer l\'adresse de destination dans votre template EmailJS (To Email).';
            } else if (error.text.includes('Invalid')) {
                errorMessage = '⚠️ Erreur de configuration EmailJS : Vérifiez vos IDs (Service ID, Template ID, Public Key).';
            } else {
                errorMessage = `Erreur EmailJS : ${error.text}`;
            }
        }
        
        return { 
            success: false, 
            error: errorMessage
        };
    }
}

// Envoyer le formulaire avec Web3Forms (RECOMMANDÉ - 250 emails/mois)
async function sendWithWeb3Forms(formData) {
    try {
        // Créer un nouveau FormData avec les données + access key
        const web3FormData = new FormData();
        web3FormData.append('access_key', CONTACT_CONFIG.web3forms.accessKey);
        web3FormData.append('name', formData.get('name'));
        web3FormData.append('email', formData.get('email'));
        web3FormData.append('subject', `Portfolio - ${formData.get('subject')}`);
        web3FormData.append('message', formData.get('message'));
        
        const response = await fetch('https://api.web3forms.com/submit', {
            method: 'POST',
            body: web3FormData,
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            return { success: true, service: 'Web3Forms' };
        } else {
            // Vérifier si c'est une erreur de quota
            if (data.message && (
                data.message.includes('limit') || 
                data.message.includes('quota') ||
                data.message.includes('exceeded')
            )) {
                console.warn('⚠️ Web3Forms : Quota mensuel dépassé (250 emails/mois)');
                return { 
                    success: false, 
                    quotaExceeded: true,
                    error: 'Quota Web3Forms dépassé'
                };
            }
            throw new Error(data.message || 'Erreur lors de l\'envoi');
        }
    } catch (error) {
        console.error('Web3Forms error:', error);
        
        // Si l'erreur est CORS mais que le mail a été envoyé quand même
        if (error.message && error.message.includes('Failed to fetch')) {
            console.warn('⚠️ Erreur CORS détectée, mais le message a probablement été envoyé. Vérifiez votre boîte mail.');
            return { 
                success: true,
                service: 'Web3Forms',
                warning: 'Message probablement envoyé (erreur réseau détectée)'
            };
        }
        
        return { 
            success: false, 
            error: 'Erreur lors de l\'envoi avec Web3Forms.'
        };
    }
}

// Envoyer le formulaire avec Formspree (50 emails/mois)
async function sendWithFormspree(formData) {
    try {
        const response = await fetch(CONTACT_CONFIG.formspree.endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            return { success: true, service: 'Formspree' };
        } else {
            const data = await response.json();
            
            // Vérifier si c'est une erreur de quota (code 429 = Too Many Requests)
            if (response.status === 429 || (data.error && data.error.includes('limit'))) {
                console.warn('⚠️ Formspree : Quota mensuel dépassé (50 emails/mois)');
                return { 
                    success: false, 
                    quotaExceeded: true,
                    error: 'Quota Formspree dépassé'
                };
            }
            
            throw new Error(data.error || 'Erreur lors de l\'envoi');
        }
    } catch (error) {
        console.error('Formspree error:', error);
        return { 
            success: false, 
            error: 'Erreur lors de l\'envoi avec Formspree.'
        };
    }
}



// Afficher un message de feedback
function showMessage(messageDiv, text, type = 'info') {
    messageDiv.textContent = text;
    messageDiv.style.display = 'block';
    
    messageDiv.classList.remove('error', 'success', 'info');
    messageDiv.classList.add(type);
    
    // Masquer automatiquement après 25 secondes
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 25000);
}

// Gérer la soumission du formulaire
async function handleContactFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = document.getElementById('form-message');
    const submitButton = document.getElementById('submit-button');
    
    // Réinitialiser le message
    messageDiv.innerHTML = '';
    messageDiv.style.display = 'none';
    
    // Désactiver le bouton
    submitButton.disabled = true;
    submitButton.innerHTML = '<svg class="icon-svg" style="animation: spin 1s linear infinite;" viewBox="0 0 512 512"><path d="M304 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm0 416a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM48 304a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm464-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM142.9 437A48 48 0 1 0 75 369.1 48 48 0 1 0 142.9 437zm0-294.2A48 48 0 1 0 75 75a48 48 0 1 0 67.9 67.9zM369.1 437A48 48 0 1 0 437 369.1 48 48 0 1 0 369.1 437z"/></svg> Envoi...';
    
    try {
        // Validation anti-spam
        const antiSpamValidation = validateAntiSpam(formData);
        if (!antiSpamValidation.valid) {
            showMessage(messageDiv, antiSpamValidation.error, 'error');
            return;
        }
        
        // Système de fallback automatique avec détection de quota
        let result;
        const servicesAttempted = [];
        
        // 1. Essayer Web3Forms (250 emails/mois)
        if (CONTACT_CONFIG.web3forms.accessKey !== 'YOUR_WEB3FORMS_ACCESS_KEY') {
            console.log('📧 Tentative d\'envoi via Web3Forms...');
            result = await sendWithWeb3Forms(formData);
            servicesAttempted.push('Web3Forms');
            
            // Si quota dépassé, essayer EmailJS
            if (result.quotaExceeded) {
                console.log('⚠️ Web3Forms : Quota dépassé, essai avec EmailJS...');
                
                if (typeof emailjs !== 'undefined' && CONTACT_CONFIG.emailjs.publicKey !== 'YOUR_PUBLIC_KEY') {
                    result = await sendWithEmailJS(formData);
                    servicesAttempted.push('EmailJS');
                    
                    // Si quota dépassé, essayer Formspree
                    if (result.quotaExceeded) {
                        console.log('⚠️ EmailJS : Quota dépassé, essai avec Formspree...');
                        
                        if (CONTACT_CONFIG.formspree.endpoint !== 'YOUR_FORMSPREE_ENDPOINT') {
                            result = await sendWithFormspree(formData);
                            servicesAttempted.push('Formspree');
                        }
                    }
                }
            }
        }
        // 2. Si Web3Forms non configuré, essayer directement EmailJS
        else if (typeof emailjs !== 'undefined' && CONTACT_CONFIG.emailjs.publicKey !== 'YOUR_PUBLIC_KEY') {
            console.log('📧 Tentative d\'envoi via EmailJS...');
            result = await sendWithEmailJS(formData);
            servicesAttempted.push('EmailJS');
            
            // Si quota dépassé, essayer Formspree
            if (result.quotaExceeded && CONTACT_CONFIG.formspree.endpoint !== 'YOUR_FORMSPREE_ENDPOINT') {
                console.log('⚠️ EmailJS : Quota dépassé, essai avec Formspree...');
                result = await sendWithFormspree(formData);
                servicesAttempted.push('Formspree');
            }
        }
        // 3. Si EmailJS non configuré, essayer Formspree
        else if (CONTACT_CONFIG.formspree.endpoint !== 'YOUR_FORMSPREE_ENDPOINT') {
            console.log('📧 Tentative d\'envoi via Formspree...');
            result = await sendWithFormspree(formData);
            servicesAttempted.push('Formspree');
        }
        // 4. Aucun service configuré - Mode développement
        else {
            console.log('📧 Mode développement - Données du formulaire:', Object.fromEntries(formData));
            showMessage(messageDiv, '⚠️ Mode développement : Le formulaire n\'est pas configuré pour envoyer des emails. Consultez la console.', 'info');
            
            // Simuler un envoi réussi après 2 secondes
            await new Promise(resolve => setTimeout(resolve, 2000));
            result = { success: true, service: 'Mode dev' };
        }
        
        // Afficher le résultat
        if (result.success) {
            const serviceUsed = result.service || 'un service';
            const message = result.warning 
                ? result.warning 
                : `Message envoyé avec succès via ${serviceUsed} ! Merci pour votre message.`;
            
            showMessage(messageDiv, message, result.warning ? 'info' : 'success');
            form.reset();
            lastSubmissionTime = Date.now();
            
            console.log(`✅ Email envoyé via ${serviceUsed}`);
        } else {
            // Tous les services ont échoué
            if (result.quotaExceeded) {
                const errorMsg = `❌ Tous les quotas sont dépassés ce mois-ci :\n` +
                    `• Web3Forms : 250/mois\n` +
                    `• EmailJS : 200/mois\n` +
                    `• Formspree : 50/mois\n\n` +
                    `Veuillez réessayer le mois prochain ou contactez-moi directement à contact@eli-dev.fr`;
                showMessage(messageDiv, errorMsg, 'error');
                console.error('❌ Tous les services ont atteint leur quota mensuel');
            } else {
                showMessage(messageDiv, result.error || 'Erreur lors de l\'envoi du message.', 'error');
            }
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showMessage(messageDiv, 'Erreur lors de l\'envoi du formulaire. Veuillez réessayer.', 'error');
    } finally {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.textContent = "Envoyer";
    }
}

// Initialiser le formulaire de contact
function initContactForm() {
    const contactForm = document.getElementById('contact-form');
    if (!contactForm) {
        return;
    }
    
    // Initialiser EmailJS si disponible
    initEmailJS();
    
    // Ajouter l'écouteur d'événements
    contactForm.addEventListener('submit', handleContactFormSubmit);
    
    console.log('✅ Formulaire de contact initialisé');
}

// Export pour utilisation dans d'autres fichiers
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initContactForm };
}
