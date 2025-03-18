/**
 * SneakersAddict - Script principal
 * Fonctionnalités pour le menu, le panier et les interactions utilisateur
 */

document.addEventListener("DOMContentLoaded", function () {
    // Fonctionnalité pour le menu mobile
    const menuToggle = document.querySelector(".menu-toggle");
    const navMenu = document.querySelector("#petit_boutons");

    if (menuToggle) {
        menuToggle.addEventListener("click", function () {
            navMenu.classList.toggle("active");
        });
    }

    // Fonctionnalité pour le mini-panier
    const miniCartIcon = document.querySelector("#petit_panier");
    const miniCart = document.querySelector(".mini-cart");

    if (miniCartIcon && miniCart) {
        miniCartIcon.addEventListener("click", function (e) {
            e.preventDefault();
            miniCart.classList.toggle("show");
        });

        // Fermer le mini-panier en cliquant ailleurs
        document.addEventListener("click", function (e) {
            if (!miniCartIcon.contains(e.target) && !miniCart.contains(e.target)) {
                miniCart.classList.remove("show");
            }
        });
    }

    // Animation pour les boutons
    const buttons = document.querySelectorAll(".submit-btn, .add-to-cart-btn, .check-btn");

    buttons.forEach((button) => {
        button.addEventListener("mousedown", function () {
            this.style.transform = "scale(0.95)";
        });

        button.addEventListener("mouseup", function () {
            this.style.transform = "scale(1)";
        });

        button.addEventListener("mouseleave", function () {
            this.style.transform = "scale(1)";
        });
    });

    // Validation du formulaire de contact
    const contactForm = document.querySelector(".contact-form form");

    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            const nameInput = document.querySelector("#name");
            const emailInput = document.querySelector("#email");
            const subjectInput = document.querySelector("#subject");
            const messageInput = document.querySelector("#message");

            let isValid = true;

            // Validation simple côté client
            if (nameInput.value.trim() === "") {
                highlightField(nameInput, "Veuillez entrer votre nom");
                isValid = false;
            } else {
                resetField(nameInput);
            }

            if (emailInput.value.trim() === "") {
                highlightField(emailInput, "Veuillez entrer votre email");
                isValid = false;
            } else if (!isValidEmail(emailInput.value)) {
                highlightField(emailInput, "Veuillez entrer un email valide");
                isValid = false;
            } else {
                resetField(emailInput);
            }

            if (subjectInput.value.trim() === "") {
                highlightField(subjectInput, "Veuillez entrer un sujet");
                isValid = false;
            } else {
                resetField(subjectInput);
            }

            if (messageInput.value.trim() === "") {
                highlightField(messageInput, "Veuillez entrer votre message");
                isValid = false;
            } else {
                resetField(messageInput);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Fonctions utilitaires
    function highlightField(field, message) {
        field.style.borderColor = "#dc3545";

        // Créer ou mettre à jour le message d'erreur
        let errorMessage = field.nextElementSibling;
        if (!errorMessage || !errorMessage.classList.contains("error-message")) {
            errorMessage = document.createElement("div");
            errorMessage.className = "error-message";
            errorMessage.style.color = "#dc3545";
            errorMessage.style.fontSize = "14px";
            errorMessage.style.marginTop = "5px";
            field.parentNode.insertBefore(errorMessage, field.nextSibling);
        }

        errorMessage.textContent = message;
    }

    function resetField(field) {
        field.style.borderColor = "";

        // Supprimer le message d'erreur s'il existe
        const errorMessage = field.nextElementSibling;
        if (errorMessage && errorMessage.classList.contains("error-message")) {
            errorMessage.remove();
        }
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
