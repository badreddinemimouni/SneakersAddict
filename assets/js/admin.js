document.addEventListener("DOMContentLoaded", function () {
    // fonction pour mettre en place les fenetres modales (clic sur btn -> afficher modale)
    function setupModal(modalId, buttonId, closeSelector) {
        const modal = document.getElementById(modalId);
        const btn = document.getElementById(buttonId);
        const closeBtn = document.querySelector(closeSelector);

        if (btn && modal) {
            // evenement pour ouvrir la modal qd on click
            btn.addEventListener("click", () => (modal.style.display = "block"));

            if (closeBtn) {
                // fermeture quand on click la croix
                closeBtn.addEventListener("click", () => (modal.style.display = "none"));

                // fermeture qd on click en dehors de la modale (bg)
                window.addEventListener("click", (event) => {
                    if (event.target == modal) modal.style.display = "none";
                });
            }
        }
    }

    // on config les modales avec notre fonction
    setupModal("add-user-modal", "btn-add-user", "#add-user-modal .close");
    setupModal("add-product-modal", "btn-add-product", "#add-product-modal .close");

    // gestion des boutons mdp (changement oeil barré <-> oeil ouvert)
    document.querySelectorAll(".toggle-password").forEach((toggle) => {
        toggle.addEventListener("click", function () {
            const input = this.previousElementSibling;
            const isPassword = input.type === "password";

            // on switch entre text et password pour afficher/cacher
            input.type = isPassword ? "text" : "password";
            this.classList.toggle("fa-eye", !isPassword);
            this.classList.toggle("fa-eye-slash", isPassword);
        });
    });

    // gestion des onglets (important pour admin)
    const tabs = document.querySelectorAll(".tab");
    const tabContents = document.querySelectorAll(".tab-content");

    if (tabs.length > 0 && tabContents.length > 0) {
        tabs.forEach((tab) => {
            tab.addEventListener("click", function () {
                // retire la class active sur tous les onglets
                tabs.forEach((t) => t.classList.remove("active"));
                // met actif l'onglet cliqué
                this.classList.add("active");

                // masque tous les contenus
                tabContents.forEach((content) => content.classList.remove("active"));

                // recup l'id de l'onglet actif et affiche le contenu correspondant
                const tabId = this.getAttribute("data-tab");
                document.getElementById(tabId + "-view").classList.add("active");
            });
        });
    }

    // gestion des choix de pointures et qté en stock (ptie importante)
    document.querySelectorAll('[id^="pointure-"]').forEach((selector) => {
        // extraire l'id du produit depuis l'id du selecteur
        const produitId = selector.id.replace("pointure-", "");
        const inputQuantite = document.getElementById(`quantite-${produitId}`);

        if (selector && inputQuantite) {
            selector.addEventListener("change", function () {
                const pointureId = this.value;

                if (pointureId) {
                    // fetch = requete ajax pour recup la qté
                    fetch(`get_stock.php?produit_id=${produitId}&pointure_id=${pointureId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            // si success on affiche qté sinon 0
                            inputQuantite.value = data.success ? data.amount : 0;
                        })
                        .catch((error) => {
                            console.error("Erreur:", error);
                            inputQuantite.value = 0;
                        });
                }
            });
        }
    });
});
