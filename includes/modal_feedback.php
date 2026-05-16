<!-- Modale de feedback -->
<div id="customFeedbackModal" class="custom-modal">
    <div class="custom-modal-content feedback-content">
        <span class="custom-close-btn" id="closeFeedbackBtn">&times;</span>
        <div class="feedback-icon">&#128172;</div>
        <h2>Partagez votre avis !</h2>
        <p>Merci d'avoir utilis&eacute; ClimateWatch. Votre retour nous aide &agrave; am&eacute;liorer l'application.</p>

        <div class="rating-stars">
            <p>Comment &eacute;valuez-vous votre exp&eacute;rience ?</p>
            <div class="custom-stars">
                <span class="custom-star" data-rating="1">&#9733;</span>
                <span class="custom-star" data-rating="2">&#9733;</span>
                <span class="custom-star" data-rating="3">&#9733;</span>
                <span class="custom-star" data-rating="4">&#9733;</span>
                <span class="custom-star" data-rating="5">&#9733;</span>
            </div>
        </div>

        <textarea id="customCommentText" placeholder="Votre commentaire (optionnel)..." rows="4"></textarea>

        <div class="feedback-options">
            <label><input type="checkbox" id="customAllowContact"> Je souhaite &ecirc;tre recontact&eacute;</label>
            <label><input type="checkbox" id="customAllowStats"> Autoriser l'utilisation anonyme des donn&eacute;es</label>
        </div>

        <div class="custom-modal-buttons">
            <button id="submitFeedbackBtn" class="custom-btn-primary">&#128228; Envoyer mon avis</button>
            <button id="skipFeedbackBtn" class="custom-btn-secondary">&#11017;&#65039; Non merci</button>
        </div>
        <p class="custom-privacy-note">&#128274; Vos donn&eacute;es restent confidentielles</p>
    </div>
</div>

<style>
    /* Styles spécifiques à la modale feedback */
    .feedback-content {
        max-width: 500px;
        text-align: center;
    }

    .feedback-icon {
        font-size: 64px;
        margin-bottom: 10px;
    }

    .rating-stars {
        margin: 20px 0;
    }

    .custom-stars {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .custom-star {
        font-size: 32px;
        cursor: pointer;
        transition: all 0.2s;
        color: #64748b;
    }

    .custom-star:hover,
    .custom-star.active {
        color: #fbbf24;
        text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
        transform: scale(1.1);
        display: inline-block;
    }

    #customCommentText {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #e2e8f0;
        font-family: inherit;
        resize: vertical;
        margin: 15px 0;
    }

    #customCommentText:focus {
        outline: none;
        border-color: #3b82f6;
    }

    .feedback-options {
        text-align: left;
        margin: 15px 0;
        font-size: 0.85rem;
    }

    .feedback-options label {
        display: block;
        margin: 8px 0;
        cursor: pointer;
        color: #94a3b8;
    }

    .feedback-options input {
        margin-right: 8px;
        cursor: pointer;
    }

    .custom-privacy-note {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 15px;
    }
</style>

<script>
    // JavaScript spécifique à la modale feedback
    (function() {
        console.log('Modale feedback charg&eacute;e');
        
        // Initialisation sp&eacute;cifique pour le feedback
        const stars = document.querySelectorAll('.custom-star');
        let selectedRating = 0;
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                stars.forEach(s => s.classList.remove('active'));
                for(let i = 0; i < selectedRating; i++) {
                    stars[i].classList.add('active');
                }
            });
        });
    })();
</script>
