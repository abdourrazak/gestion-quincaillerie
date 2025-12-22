# 🧪 Guide de Test de l'API - Gestion Quincaillerie

Ce guide vous permet de tester l'API avec **Postman**, **Insomnia** ou **curl**.

## 📋 Prérequis

1. **Démarrer le serveur Laravel** :
```bash
php artisan serve
```
L'API sera accessible sur `http://localhost:8000`

2. **Créer la base de données et exécuter les migrations** :
```bash
createdb quincaillerie
php artisan migrate
```

3. **Créer un utilisateur admin** :
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@quincaillerie.com',
    'password' => bcrypt('password123'),
    'role' => 'admin',
    'actif' => true,
]);
```

---

## 🔐 Tests d'Authentification

### 1. Test de l'API (Public)
```bash
curl http://localhost:8000/api/test
```

**Réponse attendue** :
```json
{
  "message": "API Gestion Quincaillerie OK 🚀",
  "version": "1.0.0",
  "timestamp": "2025-12-22T12:00:00+01:00"
}
```

---

### 2. Connexion (Login)
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@quincaillerie.com",
    "password": "password123"
  }'
```

**Réponse attendue** :
```json
{
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@quincaillerie.com",
    "role": "admin"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

⚠️ **Important** : Copiez le `token` pour les requêtes suivantes !

---

### 3. Récupérer le profil utilisateur (Protégé)
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"
```

**Réponse attendue** :
```json
{
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@quincaillerie.com",
    "role": "admin",
    "actif": true
  }
}
```

---

### 4. Inscription d'un nouvel utilisateur (Admin uniquement)
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Vendeur Test",
    "email": "vendeur@quincaillerie.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "vendeur"
  }'
```

---

### 5. Mettre à jour le profil
```bash
curl -X PUT http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Admin Modifié"
  }'
```

---

### 6. Changer le mot de passe
```bash
curl -X PUT http://localhost:8000/api/auth/password \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "current_password": "password123",
    "password": "nouveaumotdepasse",
    "password_confirmation": "nouveaumotdepasse"
  }'
```

---

### 7. Déconnexion
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"
```

---

## 📦 Tests des Routes Protégées

**Note** : Toutes les routes ci-dessous nécessitent le header :
```
Authorization: Bearer VOTRE_TOKEN_ICI
```

### Catégories
```bash
# Liste des catégories
curl http://localhost:8000/api/categories \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"

# Créer une catégorie
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{}'

# Détails d'une catégorie
curl http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"
```

### Produits
```bash
# Liste des produits
curl http://localhost:8000/api/produits \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"

# Recherche par code-barre
curl http://localhost:8000/api/produits/code-barre/123456789 \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"
```

### Stock
```bash
# Alertes de stock faible
curl http://localhost:8000/api/stock/alertes \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"

# Mouvements de stock
curl http://localhost:8000/api/stock/mouvements \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"
```

### Dashboard
```bash
# Statistiques
curl http://localhost:8000/api/dashboard/stats \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"

# Ventes du jour
curl http://localhost:8000/api/dashboard/ventes-jour \
  -H "Authorization: Bearer VOTRE_TOKEN_ICI" \
  -H "Accept: application/json"
```

---

## 📝 Collection Postman

Vous pouvez importer cette collection dans Postman :

1. Créer une nouvelle collection "Gestion Quincaillerie"
2. Ajouter une variable d'environnement :
   - `base_url` = `http://localhost:8000/api`
   - `token` = (sera rempli après le login)
3. Créer les requêtes ci-dessus

---

## ❌ Tests d'Erreurs

### Connexion avec mauvais identifiants
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "wrong@email.com",
    "password": "wrongpassword"
  }'
```

**Réponse attendue** (422) :
```json
{
  "message": "Les identifiants fournis sont incorrects.",
  "errors": {
    "email": ["Les identifiants fournis sont incorrects."]
  }
}
```

### Accès sans token
```bash
curl http://localhost:8000/api/auth/me \
  -H "Accept: application/json"
```

**Réponse attendue** (401) :
```json
{
  "message": "Unauthenticated."
}
```

---

## 🔧 Debugging

### Activer le mode debug
Dans `.env` :
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Voir les logs Laravel
```bash
tail -f storage/logs/laravel.log
```

### Vérifier les routes
```bash
php artisan route:list --path=api
```

---

## ✅ Checklist de Tests

- [ ] Route de test accessible
- [ ] Login avec identifiants corrects
- [ ] Login avec identifiants incorrects
- [ ] Récupération du profil avec token
- [ ] Accès refusé sans token
- [ ] Inscription d'un nouvel utilisateur
- [ ] Mise à jour du profil
- [ ] Changement de mot de passe
- [ ] Déconnexion
- [ ] Accès aux routes protégées avec token
- [ ] Toutes les routes retournent du JSON

---

**Prochaine étape** : Implémenter les contrôleurs pour chaque ressource !
