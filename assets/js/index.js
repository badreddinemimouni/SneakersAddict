// Sélectionner les chaussures du DOM
const increaseButton = document.getElementById('increase'); // Bouton pour augmenter le stock
const decreaseButton = document.getElementById('decrease'); // Bouton pour diminuer le stock
const stockE = document.getElementById('input'); // Champ d'entrée pour afficher le stock

const increaseButton2 = document.getElementById('increase2'); // Deuxième bouton pour augmenter le stock
const decreaseButton2 = document.getElementById('decrease2'); // Deuxième bouton pour diminuer le stock
const stockE2 = document.getElementById('input2'); // Deuxième champ d'entrée pour afficher le stock

const increaseButton3 = document.getElementById('increase3'); // Troisième bouton pour augmenter le stock
const decreaseButton3 = document.getElementById('decrease3'); // Troisième bouton pour diminuer le stock
const stockE3 = document.getElementById('input6'); // Troisième champ d'entrée pour afficher le stock

// Définir le stock initial
let stock = 0; // Stock initial pour la première chaussure
let stock2 = 0; // Stock initial pour la deuxième chaussure
let stock3 = 0; // Stock initial pour la troisième chaussure

// Mettre à jour le stock affiché
function updateStock() {
  stockE.value = stock; // Mettre à jour la valeur affichée dans le champ d'entrée pour la première chaussure
}
// Augmenter le stock
function increaseStock() {
  stock++; // Augmenter le stock pour la première chaussure
  updateStock(); // Mettre à jour le stock affiché
}

// Diminuer le stock (avec vérification pour éviter un stock négatif)
function decreaseStock() {
  // if (stock == 0) {
  stock--; // Diminuer le stock pour la première chaussure
  updateStock(); // Mettre à jour le stock affiché
  // }
}
increaseButton2.addEventListener('click', increaseStock2); // Ajouter un écouteur d'événement pour le deuxième bouton d'augmentation
decreaseButton2.addEventListener('click', decreaseStock2); // Ajouter un écouteur d'événement pour le deuxième bouton de diminution



function updateStock2() {
  stockE2.value = stock2; // Mettre à jour la valeur affichée dans le champ d'entrée pour la deuxième chaussure
}

function updateStock3() {
  stockE3.value = stock3; // Mettre à jour la valeur affichée dans le champ d'entrée pour la troisième chaussure
}



function increaseStock2() {
  stock2++; // Augmenter le stock pour la deuxième chaussure
  updateStock2(); // Mettre à jour le stock affiché
}

function increaseStock3() {
  stock3++; // Augmenter le stock pour la troisième chaussure
  updateStock3(); // Mettre à jour le stock affiché
}



function decreaseStock2() {
  // if (stock2 == 0) {
  stock2--; // Diminuer le stock pour la deuxième chaussure
  updateStock2(); // Mettre à jour le stock affiché
  // }
}

function decreaseStock3() {
  // if (stock2 == 0) {
  stock3--; // Diminuer le stock pour la troisième chaussure
  updateStock3(); // Mettre à jour le stock affiché
  // }
}

// Ajouter des écouteurs d'événements aux boutons
increaseButton.addEventListener('click', increaseStock); // Ajouter un écouteur d'événement pour le premier bouton d'augmentation
decreaseButton.addEventListener('click', decreaseStock); // Ajouter un écouteur d'événement pour le premier bouton de diminution



increaseButton3.addEventListener('click', increaseStock3); // Ajouter un écouteur d'événement pour le troisième bouton d'augmentation
decreaseButton3.addEventListener('click', decreaseStock3); // Ajouter un écouteur d'événement pour le troisième bouton de diminution

// Mettre à jour le stock initial

updateStock(); // Mettre à jour le stock initial de la première chaussure affichée
updateStock2(); // Mettre à jour le stock initial de la deuxième chaussure affichée
updateStock3(); // Mettre à jour le stock initial de la troisième chaussure affichée

