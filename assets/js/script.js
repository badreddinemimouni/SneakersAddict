document.addEventListener("DOMContentLoaded", function () {
    // menu hamburger pr tel
    const menuToggle = document.querySelector(".menu-toggle");
    const navMenu = document.querySelector("#petit_boutons");

    if (menuToggle) {
        menuToggle.addEventListener("click", () => navMenu.classList.toggle("active"));
    }

    // mini panier qui se deplie quand on clique
    const miniCartIcon = document.querySelector("#panier-icon");
    const miniPanier = document.getElementById("mini-panier");

    if (miniCartIcon && miniPanier) {
        miniCartIcon.addEventListener("click", (e) => {
            e.preventDefault();
            miniPanier.style.display = miniPanier.style.display === "block" ? "none" : "block";
        });

        // ferme le panier quand on clique ailleurs (important pr pas gener)
        document.addEventListener("click", (e) => {
            if (!miniCartIcon.contains(e.target) && !miniPanier.contains(e.target)) {
                miniPanier.style.display = "none";
            }
        });

        // affiche panier apres ajout produit
        if (window.location.search.includes("ajout=1")) {
            afficherMiniPanier();
        }
    }

    // fonction qui affiche panier et le cache apres 5s
    function afficherMiniPanier() {
        if (miniPanier) {
            miniPanier.style.display = "block";
            setTimeout(() => {
                miniPanier.style.display = "none";
            }, 5000);
        }
    }

    // effet animation sur boutons
    document
        .querySelectorAll(".submit-btn, .add-to-cart-btn, .check-btn, .btn-primary, .btn-secondary")
        .forEach((button) => {
            button.addEventListener("mousedown", () => (button.style.transform = "scale(0.95)"));
            button.addEventListener("mouseup", () => (button.style.transform = "scale(1)"));
            button.addEventListener("mouseleave", () => (button.style.transform = "scale(1)"));
        });

    // verif formulaire de contact (important pr pas envoyer des trucs vides)
    const contactForm = document.querySelector(".contact-form form");

    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            const fields = [
                {
                    input: document.querySelector("#name"),
                    rule: (val) => val !== "",
                    message: "Veuillez entrer votre nom",
                },
                {
                    input: document.querySelector("#email"),
                    rule: (val) => val !== "" && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
                    message: (val) =>
                        val === ""
                            ? "Veuillez entrer votre email"
                            : "Veuillez entrer un email valide",
                },
                {
                    input: document.querySelector("#subject"),
                    rule: (val) => val !== "",
                    message: "Veuillez entrer un sujet",
                },
                {
                    input: document.querySelector("#message"),
                    rule: (val) => val !== "",
                    message: "Veuillez entrer votre message",
                },
            ];

            let isValid = true;

            // on verifie chaque champ (obligatoir sinon ca va planter)
            fields.forEach((field) => {
                if (!field.input) return;

                const value = field.input.value.trim();
                const isFieldValid = field.rule(value);

                if (!isFieldValid) {
                    const msg =
                        typeof field.message === "function" ? field.message(value) : field.message;
                    highlightField(field.input, msg);
                    isValid = false;
                } else {
                    resetField(field.input);
                }
            });

            // empeche envoi si pas valide
            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // fonction qui cree les msg d'erreur (rouge pour voir que c pas bon)
    function highlightField(field, message) {
        field.style.borderColor = "#dc3545";

        let errorMessage = field.nextElementSibling;
        if (!errorMessage || !errorMessage.classList.contains("error-message")) {
            errorMessage = document.createElement("div");
            errorMessage.className = "error-message";

            // style pour msg erreur
            errorMessage.style.color = "#dc3545";
            errorMessage.style.fontSize = "14px";
            errorMessage.style.marginTop = "5px";

            field.parentNode.insertBefore(errorMessage, field.nextSibling);
        }

        errorMessage.textContent = message;
    }

    // enleve l'erreur qd c corrigé
    function resetField(field) {
        field.style.borderColor = "";

        const errorMessage = field.nextElementSibling;
        if (errorMessage && errorMessage.classList.contains("error-message")) {
            errorMessage.remove();
        }
    }

    // gestion des onglets login/register (pour la page de connexion)
    const tabLogin = document.getElementById("tab-login");
    const tabRegister = document.getElementById("tab-register");
    const loginFormContainer = document.getElementById("login-form-container");
    const registerFormContainer = document.getElementById("register-form-container");

    if (tabLogin && tabRegister && loginFormContainer && registerFormContainer) {
        // switch entre login et création compte (important)
        tabLogin.addEventListener("click", function () {
            tabLogin.classList.add("active");
            tabRegister.classList.remove("active");
            loginFormContainer.style.display = "block";
            registerFormContainer.style.display = "none";
        });

        tabRegister.addEventListener("click", function () {
            tabRegister.classList.add("active");
            tabLogin.classList.remove("active");
            registerFormContainer.style.display = "block";
            loginFormContainer.style.display = "none";
        });
    }

    // fonction pour gérer les toggle mdp (afficher/cacher)
    function setupPasswordToggle(toggleId, passwordId) {
        const toggle = document.getElementById(toggleId);
        const pwd = document.getElementById(passwordId);

        if (toggle && pwd) {
            toggle.addEventListener("click", function () {
                // change type entre password et text (pr voir mdp)
                const type = pwd.getAttribute("type") === "password" ? "text" : "password";
                pwd.setAttribute("type", type);
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            });
        }
    }

    // setup les toggle de mdp (login et inscription)
    setupPasswordToggle("toggle-password", "password");
    setupPasswordToggle("toggle-register-password", "register-password");
    setupPasswordToggle("toggle-confirm-password", "register-confirm");

    // formatage CB pour page paiement
    const cardNumber = document.getElementById("card_number");
    const cardExpiry = document.getElementById("card_expiry");

    if (cardNumber) {
        // ajoute des espaces tous les 4 chiffres (pr lisibilité)
        cardNumber.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            let formatted = "";

            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += " ";
                }
                formatted += value[i];
            }

            e.target.value = formatted;
        });
    }

    if (cardExpiry) {
        // format MM/YY (important pr pas se tromper)
        cardExpiry.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            let formatted = "";

            if (value.length > 0) {
                formatted = value.substring(0, 2);
                if (value.length > 2) {
                    formatted += "/" + value.substring(2, 4);
                }
            }

            e.target.value = formatted;
        });
    }
});
