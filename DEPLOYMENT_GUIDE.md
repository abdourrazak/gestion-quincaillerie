# 🚀 Guide de Déploiement - Gestion Quincaillerie

## ⚠️ **IMPORTANT : Architecture Laravel + React**

Cette application utilise **Laravel (backend)** et **React (frontend)** avec **Inertia.js**. 

**Vercel ne supporte PAS PHP nativement**, donc vous avez 2 options :

---

## 🎯 **Option 1 : Architecture Séparée (RECOMMANDÉ)**

### **Pourquoi cette approche ?**
- ✅ Meilleure pratique pour les applications modernes
- ✅ Scalabilité indépendante
- ✅ Vercel gratuit pour le frontend
- ✅ Idéal pour apprendre l'architecture API REST

### **Architecture**

```
┌─────────────────┐         API REST          ┌──────────────────┐
│   Frontend      │ ◄────────────────────────► │   Backend        │
│   React + Vite  │    (HTTPS + CORS)          │   Laravel API    │
│   (Vercel)      │                            │   (Railway)      │
└─────────────────┘                            └──────────────────┘
        │                                               │
        │                                               │
        ↓                                               ↓
  Utilisateurs                                    PostgreSQL
```

---

## 📦 **Étape 1 : Déployer le Backend Laravel sur Railway**

### **1.1 Créer un compte Railway**
1. Allez sur [railway.app](https://railway.app)
2. Connectez-vous avec GitHub
3. Cliquez sur "New Project"

### **1.2 Déployer depuis GitHub**
1. Sélectionnez "Deploy from GitHub repo"
2. Choisissez `abdourrazak/gestion-quincaillerie`
3. Railway détectera automatiquement Laravel

### **1.3 Ajouter PostgreSQL**
1. Dans votre projet Railway, cliquez sur "+ New"
2. Sélectionnez "Database" → "PostgreSQL"
3. Railway créera automatiquement la base de données

### **1.4 Configurer les Variables d'Environnement**

Dans Railway, allez dans "Variables" et ajoutez :

```bash
# Application
APP_NAME="Gestion Quincaillerie API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.railway.app

# Base de données (automatique avec Railway PostgreSQL)
# DB_CONNECTION, DB_HOST, DB_PORT, etc. sont auto-configurés

# Génération de clé
APP_KEY=
# Railway peut générer automatiquement avec: php artisan key:generate --show
```

### **1.5 Configurer le Build**

Créez un fichier `railway.json` à la racine :

```json
{
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

### **1.6 Déployer**
1. Railway déploiera automatiquement
2. Récupérez l'URL : `https://votre-app.railway.app`
3. Testez : `https://votre-app.railway.app/api/test`

---

## 🎨 **Étape 2 : Déployer le Frontend React sur Vercel**

### **2.1 Préparer le Frontend**

**IMPORTANT** : Vous devez créer un projet React séparé qui consomme l'API Laravel.

#### **Option A : Créer un nouveau projet React (Recommandé)**

```bash
# Créer un nouveau dépôt pour le frontend
npx create-vite@latest quincaillerie-frontend --template react-ts
cd quincaillerie-frontend
npm install axios
```

Configurez l'URL de l'API :

```typescript
// src/config.ts
export const API_URL = import.meta.env.VITE_API_URL || 'https://votre-app.railway.app/api';
```

```typescript
// src/api/client.ts
import axios from 'axios';
import { API_URL } from '../config';

export const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Intercepteur pour ajouter le token
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
```

#### **Option B : Utiliser le frontend actuel (Complexe)**

Si vous voulez garder Inertia.js, vous devrez :
1. Configurer un proxy vers Railway
2. Gérer SSR (Server-Side Rendering)
3. C'est plus complexe et non recommandé pour Vercel

### **2.2 Déployer sur Vercel**

1. Poussez votre frontend sur GitHub
2. Allez sur [vercel.com](https://vercel.com)
3. "Import Project" → Sélectionnez votre dépôt frontend
4. Ajoutez les variables d'environnement :

```bash
VITE_API_URL=https://votre-app.railway.app/api
```

5. Déployez !

---

## 🔧 **Étape 3 : Configurer CORS sur Laravel**

Pour que le frontend (Vercel) puisse appeler le backend (Railway), configurez CORS :

### **3.1 Installer le package CORS (déjà inclus dans Laravel)**

Modifiez `config/cors.php` :

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://votre-frontend.vercel.app',
        'http://localhost:5173', // Pour le développement local
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### **3.2 Configurer Sanctum**

Modifiez `config/sanctum.php` :

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1,votre-frontend.vercel.app',
    Sanctum::currentApplicationUrlWithPort()
))),
```

---

## 🎯 **Option 2 : Déployer tout sur Railway (Plus Simple)**

Si vous ne voulez pas séparer frontend/backend :

### **Avantages**
- ✅ Tout au même endroit
- ✅ Pas de problèmes CORS
- ✅ Plus simple à gérer

### **Inconvénients**
- ❌ Moins scalable
- ❌ Pas de CDN pour les assets statiques
- ❌ Coût potentiellement plus élevé

### **Configuration**

1. Déployez sur Railway comme dans l'Étape 1
2. Railway servira à la fois l'API et le frontend
3. Pas besoin de Vercel

---

## 📝 **Fichiers Modifiés pour Vercel**

### **vite.config.ts**
- ✅ Désactivation de Wayfinder en CI/production
- ✅ Détection automatique de l'environnement Vercel

### **vercel.json**
- ✅ Configuration du build
- ✅ Variables d'environnement

---

## 🧪 **Tester le Déploiement**

### **Backend (Railway)**
```bash
# Test de l'API
curl https://votre-app.railway.app/api/test

# Test de connexion
curl -X POST https://votre-app.railway.app/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@quincaillerie.com","password":"password123"}'
```

### **Frontend (Vercel)**
1. Ouvrez `https://votre-frontend.vercel.app`
2. Testez la connexion
3. Vérifiez que les appels API fonctionnent

---

## ❓ **FAQ**

### **Q: Pourquoi Vercel ne supporte pas Laravel ?**
**R:** Vercel est optimisé pour les applications frontend (React, Next.js, Vue). Laravel nécessite PHP et une base de données, ce que Vercel ne fournit pas.

### **Q: Railway est-il gratuit ?**
**R:** Railway offre un plan gratuit avec $5 de crédit mensuel, suffisant pour un projet de test.

### **Q: Puis-je utiliser Heroku à la place de Railway ?**
**R:** Oui ! Heroku fonctionne aussi, mais le plan gratuit a été supprimé. Railway est actuellement la meilleure option gratuite.

### **Q: Comment gérer les migrations en production ?**
**R:** Railway peut exécuter automatiquement `php artisan migrate --force` au déploiement.

### **Q: Et pour les images des produits ?**
**R:** Utilisez un service de stockage comme :
- AWS S3
- Cloudinary
- DigitalOcean Spaces

---

## 🚀 **Prochaines Étapes**

1. ✅ Corriger l'erreur Vercel (fait)
2. ⏳ Décider de l'architecture (séparée ou monolithique)
3. ⏳ Déployer le backend sur Railway
4. ⏳ Configurer CORS
5. ⏳ Déployer le frontend sur Vercel (si architecture séparée)
6. ⏳ Tester l'application en production

---

**Quelle option préférez-vous ?**
- **Option 1** : Architecture séparée (Frontend Vercel + Backend Railway)
- **Option 2** : Tout sur Railway

Dites-moi et je vous guide étape par étape ! 🎯
