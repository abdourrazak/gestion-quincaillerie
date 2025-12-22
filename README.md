# 🏗️ Gestion de Quincaillerie - Matériaux de Construction

Application complète de gestion de quincaillerie spécialisée dans les matériaux de construction, développée avec Laravel (Backend) et React (Frontend).

## 🚀 Technologies

### Backend
- **Laravel 11** - Framework PHP
- **PostgreSQL** - Base de données
- **Laravel Sanctum** - Authentification API
- **Inertia.js** - Pont Backend/Frontend

### Frontend
- **React 19** - Bibliothèque UI
- **TypeScript** - Typage statique
- **TailwindCSS** - Framework CSS
- **shadcn/ui** - Composants UI
- **Vite** - Build tool

## ✨ Fonctionnalités

### 📦 Gestion des Produits
- CRUD complet des matériaux de construction
- 10 catégories principales (Ciment, Bois, Quincaillerie, etc.)
- Gestion des images produits
- Scanner de codes-barres
- Suivi du stock en temps réel

### 🏪 Point de Vente (POS)
- Interface de caisse rapide
- Calcul automatique des totaux
- Modes de paiement multiples
- Impression de factures

### 📊 Gestion du Stock
- Entrées et sorties de stock
- Alertes de stock faible
- Inventaire en temps réel
- Historique des mouvements

### 🧾 Facturation
- Système de facturation complet
- Numérotation automatique
- Gestion de la TVA
- Export PDF

### 👥 Gestion des Utilisateurs
- **Admin** - Accès complet
- **Gérant** - Gestion produits, ventes, rapports
- **Vendeur** - Ventes uniquement
- **Magasinier** - Gestion du stock

### 📈 Tableau de Bord
- Statistiques en temps réel
- Chiffre d'affaires
- Produits populaires
- Alertes de stock
- Graphiques interactifs

### 🏢 Gestion des Fournisseurs
- CRUD fournisseurs
- Commandes fournisseurs
- Historique des commandes
- Suivi des livraisons

### 👤 Gestion des Clients
- Informations clients
- Historique d'achats
- Recherche rapide

## 📋 Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL 14+

### Installation Backend

```bash
# Cloner le dépôt
git clone https://github.com/abdourrazak/gestion-quincaillerie.git
cd gestion-quincaillerie

# Installer les dépendances PHP
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=quincaillerie
# DB_USERNAME=votre_user
# DB_PASSWORD=votre_password

# Créer la base de données
createdb quincaillerie

# Exécuter les migrations
php artisan migrate

# Générer les données de test (optionnel)
php artisan db:seed
```

### Installation Frontend

```bash
# Installer les dépendances Node
npm install

# Lancer le serveur de développement
npm run dev
```

### Lancer l'application

```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend
npm run dev
```

L'application sera accessible sur `http://localhost:8000`

## 📁 Structure du Projet

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── StockController.php
│   │   │   │   ├── SaleController.php
│   │   │   │   └── ...
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Stock.php
│   │   ├── Sale.php
│   │   └── ...
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── layouts/
│   │   └── app.tsx
│   └── css/
├── routes/
│   ├── api.php
│   └── web.php
└── ...
```

## 🧪 Tests

```bash
# Exécuter les tests
php artisan test

# Tests avec couverture
php artisan test --coverage
```

## 📝 API Documentation

L'API REST est accessible via `/api/v1/`

### Endpoints principaux

- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion
- `GET /api/products` - Liste des produits
- `POST /api/products` - Créer un produit
- `GET /api/sales` - Liste des ventes
- `POST /api/sales` - Créer une vente
- ... (voir documentation complète)

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📄 Licence

Ce projet est sous licence MIT.

## 👨‍💻 Auteur

**Abd Razak**
- GitHub: [@abdourrazak](https://github.com/abdourrazak)

---

Développé avec ❤️ pour l'apprentissage de Laravel et React
