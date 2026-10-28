# zTCG

Application web de gestion de collection de produits scellés de jeux de cartes à collectionner (TCG) : ETB, displays, boosters, coffrets…

## Fonctionnement

Les utilisateurs ajoutent à leur collection des produits issus d'un catalogue commun.

### Catalogue

Les administrateurs référencent les produits qui existent réellement. Chaque produit n'est donc créé qu'une seule fois, puis partagé par tous les utilisateurs : on évite les doublons et les produits inventés.

Le catalogue est organisé sur trois niveaux :

1. **TCG** : le jeu (nom, logo, description)
2. **Sets** : les extensions d'un TCG (code, date de sortie)
3. **Produits** : les produits d'un set, avec un type (ETB, display, booster, bundle, tin, mini tin, coffret, autre), une langue (FR, EN, JP) et un prix de base

### Ma collection

Chaque utilisateur ajoute à sa collection les produits du catalogue qu'il possède, en un ou plusieurs exemplaires, en indiquant :

- la quantité
- le prix et la date d'achat
- l'état : scellé ou ouvert
- des notes libres

Une collection n'est visible et modifiable que par son propriétaire.

### Compte

Inscription, connexion, vérification d'email, réinitialisation du mot de passe, double authentification (2FA) et thème clair/sombre.

## Stack

- **Backend** : Laravel 12, Fortify, MariaDB
- **Frontend** : Inertia.js v3, React 19, TypeScript, Tailwind CSS v4, shadcn/ui
- **Routes typées** : Laravel Wayfinder
- **Tests** : Pest
- **Outillage** : pnpm, Vite, ESLint, Prettier, Pint

## Installation

Prérequis : PHP 8.4+, Composer, Node.js 22+, pnpm et MariaDB.

```bash
cp .env.example .env
```

Renseignez les accès à la base de données dans `.env`, puis :

```bash
composer setup
```

Cette commande installe les dépendances PHP et JS, génère la clé de l'application, lance les migrations et compile les assets.

## Lancement

```bash
composer dev
```

Démarre en parallèle le serveur Laravel, la file d'attente, les logs (Pail) et Vite. L'application est accessible sur http://localhost:8000.

Avec Docker (Laravel Sail), qui fournit aussi MariaDB et Mailpit :

```bash
./vendor/bin/sail up -d
./vendor/bin/sail pnpm run dev
```

Avec Sail, définissez `DB_HOST=mariadb` dans `.env`. L'application est alors accessible sur http://localhost.

## Intégration et déploiement

GitHub Actions vérifie chaque pull request vers `dev` et `main` : tests Pest, style PHP (Pint), lint, formatage et types côté frontend.

À chaque push, le déploiement n'est lancé que si ces vérifications réussissent :

- `main` → production
- `dev` → préproduction

## Licence

© Cyril Fischer — tous droits réservés. Le code est publié à titre de démonstration ; toute réutilisation nécessite une autorisation.
