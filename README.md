# SneakersAddict

##Autres projets
J'ai créer un autre petit projet pour répondre aux compétences manquantes de ce projet ( responsive en mobile first et archi en mvc + requetes asynchrone) voici le lien du projet : https://github.com/badreddinemimouni/Todo_app 

Site e-commerce de vente de chaussures de sport.

## Petite précision concernant la réintiaisation du repo

J'ai réintialiser le dépôt car il y avait un fichier dans lequel il y avait mes accès outlook que j'avais mis pour le formulaire de contact et vu qu'on peut y avoir accès en allant dans l'historique des commits et du repo je l'ai supprimé et j'ai réinitialiser le dépôt.

## Formulaire de contact

Formulaire de contact n'est pas terminé car j'ai eu un problème avec l'hébergement donc pas de serveur mail

## Description

SneakersAddict est une plateforme e-commerce spécialisée dans la vente de chaussures de sport. Le site offre une interface moderne et intuitive permettant aux utilisateurs de parcourir les produits, de les ajouter à leur panier et de passer commande.

## Fonctionnalités existantes

### Fonctionnalités utilisateur

-   **Catalogue de produits** : Affichage des chaussures disponibles avec images et prix
-   **Fiche produit détaillée** : Page dédiée pour chaque chaussure avec description et prix
-   **Sélection de pointure** : Possibilité de choisir sa pointure avant d'ajouter au panier
-   **Vérification de stock** : Affichage de la disponibilité du produit dans la pointure sélectionnée avec indicateur visuel (vert/rouge)
-   **Système de panier d'achat** : Ajout, modification et suppression de produits dans le panier
-   **Mini-panier** : Aperçu rapide du panier sans changer de page
-   **Processus de paiement** : Système complet en 3 étapes (livraison, paiement, confirmation)
-   **Système d'authentification** : Inscription et connexion des utilisateurs
-   **Formulaire de contact** : Possibilité de contacter l'équipe du site
-   **Design responsive** : Adaptation de l'interface à différents appareils

### Fonctionnalités administrateur

-   **Gestion des stocks** : Interface d'administration pour gérer les stocks par pointure
-   **Ajout de produits** : Possibilité d'ajouter de nouveaux produits avec upload d'images
-   **Suppression de produits** : Possibilité de supprimer des produits avec confirmation
-   **Modification des prix** : Mise à jour des prix des produits existants
-   **Visualisation des stocks** : Vue en grille ou en tableau des stocks disponibles
-   **Gestion des utilisateurs** : Interface pour créer, modifier et supprimer des utilisateurs, y compris des administrateurs

### Fonctionnalités techniques

-   **Variables d'environnement** : Configuration via fichier .env pour sécuriser les informations sensibles
-   **Système de routes** : Gestion des accès aux différentes pages selon le rôle de l'utilisateur
-   **Sécurité renforcée** : Protection contre les attaques XSS, injections SQL et CSRF
-   **Hachage des mots de passe** : Stockage sécurisé des mots de passe avec la fonction password_hash()
-   **Upload d'images** : Système de téléchargement d'images pour les produits avec validation des types et tailles
-   **Simulation de paiement** : Système de paiement simulé pour tester le processus d'achat

### Features spéciales

-   **Toggles de mot de passe** : Possibilité de voir le mot de passe lors de la saisie
-   **Connexion via réseaux sociaux** : Intégration de boutons de connexion via Facebook, Google et Twitter
-   **Messages de confirmation** : Feedback visuel après les actions de l'utilisateur
-   **Système de déconnexion sécurisé** : Destruction complète de la session lors de la déconnexion
-   **Formatage automatique des champs** : Formatage des numéros de carte et dates d'expiration pendant la saisie
-   **Génération de numéro de commande** : Création automatique d'un numéro de commande unique

## Technologies utilisées

-   PHP
-   MySQL
-   HTML5
-   CSS3
-   JavaScript
-   Font Awesome pour les icônes

## Installation

1. Clonez ce dépôt sur votre serveur web local ou distant
2. Importez la base de données depuis le fichier SQL fourni
3. Copiez le fichier `.env.example` vers `.env` et configurez les paramètres de connexion à la base de données
4. Accédez au site via votre navigateur

## Structure de la base de données

Le projet utilise plusieurs tables :

-   `stock` : Informations sur les produits
-   `stock_size` : Gestion des stocks par pointure
-   `size` : Liste des pointures disponibles
-   `user_site` : Informations des utilisateurs

## Auteurs

-   Badr-Eddine Mimouni
