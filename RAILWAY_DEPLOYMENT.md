# 🚂 Guide de Déploiement Railway - Gestion Quincaillerie

## 🎯 **Déploiement Complet sur Railway (GRATUIT)**

Railway offre **$5 de crédit gratuit par mois**, largement suffisant pour votre application.

---

## 📋 **Prérequis**

- ✅ Compte GitHub (vous l'avez déjà)
- ✅ Code poussé sur GitHub (fait)
- ✅ 10 minutes de votre temps

---

## 🚀 **Étape 1 : Créer un Compte Railway**

1. Allez sur **[railway.app](https://railway.app)**
2. Cliquez sur **"Login"**
3. Sélectionnez **"Login with GitHub"**
4. Autorisez Railway à accéder à vos dépôts

✅ **Vous avez maintenant $5 de crédit gratuit !**

---

## 📦 **Étape 2 : Créer un Nouveau Projet**

1. Sur le dashboard Railway, cliquez sur **"New Project"**
2. Sélectionnez **"Deploy from GitHub repo"**
3. Choisissez le dépôt **`abdourrazak/gestion-quincaillerie`**
4. Railway va détecter automatiquement que c'est une application Laravel

---

## 🗄️ **Étape 3 : Ajouter PostgreSQL**

1. Dans votre projet, cliquez sur **"+ New"**
2. Sélectionnez **"Database"**
3. Choisissez **"Add PostgreSQL"**
4. Railway créera automatiquement la base de données

✅ **Railway configure automatiquement les variables de connexion !**

---

## ⚙️ **Étape 4 : Configurer les Variables d'Environnement**

Railway a déjà configuré les variables de base de données, mais vous devez ajouter :

### **4.1 Cliquez sur votre service Laravel**

### **4.2 Allez dans l'onglet "Variables"**

### **4.3 Ajoutez ces variables :**

```bash
# Application
APP_NAME="Gestion Quincaillerie"
APP_ENV=production
APP_DEBUG=false
APP_URL=${{ RAILWAY_PUBLIC_DOMAIN }}

# Locale
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database

# Queue
QUEUE_CONNECTION=database
```

### **4.4 Générer APP_KEY**

Railway peut générer automatiquement la clé :

1. Dans "Variables", ajoutez une nouvelle variable
2. Nom : `APP_KEY`
3. Valeur : Cliquez sur **"Generate"** ou utilisez cette commande localement :

```bash
php artisan key:generate --show
```

Copiez la clé générée (ex: `base64:xxxxxxxxxxxxx`)

---

## 🔧 **Étape 5 : Configurer le Domaine Public**

1. Dans votre service Laravel, allez dans **"Settings"**
2. Trouvez la section **"Networking"**
3. Cliquez sur **"Generate Domain"**
4. Railway générera un domaine comme : `gestion-quincaillerie-production.up.railway.app`

✅ **Votre application sera accessible sur ce domaine !**

---

## 🚀 **Étape 6 : Déployer**

Railway déploie automatiquement dès que vous poussez sur GitHub !

### **6.1 Vérifier le Build**

1. Allez dans l'onglet **"Deployments"**
2. Vous verrez le build en cours
3. Attendez que le statut soit **"Success"** (2-5 minutes)

### **6.2 Vérifier les Logs**

Si le déploiement échoue :
1. Cliquez sur le déploiement
2. Consultez les logs pour voir l'erreur
3. Corrigez et poussez à nouveau sur GitHub

---

## 🧪 **Étape 7 : Tester l'Application**

### **7.1 Tester l'API**

Ouvrez votre navigateur ou utilisez curl :

```bash
# Test de l'API
curl https://votre-app.up.railway.app/api/test
```

**Réponse attendue :**
```json
{
  "message": "API Gestion Quincaillerie OK 🚀",
  "version": "1.0.0",
  "timestamp": "2025-12-24T12:00:00+01:00"
}
```

### **7.2 Créer un Utilisateur Admin**

Railway n'a pas de console interactive, donc créez un seeder :

**Localement, créez un fichier :**

`database/seeders/AdminSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@quincaillerie.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('Admin@2025'),
                'role' => 'admin',
                'actif' => true,
            ]
        );
    }
}
```

**Modifiez `database/seeders/DatabaseSeeder.php` :**

```php
public function run(): void
{
    $this->call([
        AdminSeeder::class,
    ]);
}
```

**Poussez sur GitHub :**

```bash
git add .
git commit -m "Add admin seeder"
git push
```

**Exécutez le seeder sur Railway :**

Dans Railway, allez dans l'onglet **"Settings"** → **"Deploy"** → Ajoutez à la commande de démarrage :

```bash
php artisan db:seed --class=AdminSeeder --force
```

### **7.3 Tester la Connexion**

```bash
curl -X POST https://votre-app.up.railway.app/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@quincaillerie.com",
    "password": "Admin@2025"
  }'
```

**Réponse attendue :**
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

---

## 📊 **Étape 8 : Surveiller l'Utilisation**

### **8.1 Vérifier le Crédit**

1. Allez sur le dashboard Railway
2. En haut à droite, vous verrez votre utilisation
3. Exemple : `$1.23 / $5.00 used this month`

### **8.2 Optimiser les Coûts**

Si vous dépassez les $5, voici comment réduire :

1. **Mettre l'app en veille** quand vous ne l'utilisez pas :
   - Settings → Sleep Mode → Activé
   - L'app s'endort après 30 min d'inactivité

2. **Utiliser SQLite au lieu de PostgreSQL** (pour les tests) :
   - Économise ~$1-2/mois
   - Mais moins réaliste pour la production

---

## 🔄 **Déploiement Automatique**

Railway redéploie automatiquement à chaque push sur GitHub !

```bash
# Faire une modification
git add .
git commit -m "Update feature"
git push

# Railway redéploie automatiquement (2-5 min)
```

---

## 🐛 **Dépannage**

### **Erreur : "APP_KEY not set"**

**Solution :**
1. Générez une clé : `php artisan key:generate --show`
2. Ajoutez-la dans Railway Variables

### **Erreur : "Database connection failed"**

**Solution :**
1. Vérifiez que PostgreSQL est bien ajouté au projet
2. Railway configure automatiquement `DATABASE_URL`
3. Vérifiez dans Variables que `DB_CONNECTION=pgsql`

### **Erreur : "Migration failed"**

**Solution :**
1. Vérifiez les logs du déploiement
2. Assurez-vous que PostgreSQL est démarré
3. Exécutez manuellement : `php artisan migrate --force`

### **L'application est lente au premier chargement**

**Normal !** Railway met l'app en veille après 30 min d'inactivité.
- Premier chargement : ~10 secondes (réveil)
- Chargements suivants : rapides

---

## 📈 **Statistiques de Coût Estimées**

Pour une utilisation normale (développement/apprentissage) :

```
┌─────────────────────┬─────────────────┐
│ Service             │ Coût/mois       │
├─────────────────────┼─────────────────┤
│ Laravel + React     │ ~$3.00          │
│ PostgreSQL          │ ~$1.50          │
│ Total               │ ~$4.50          │
├─────────────────────┼─────────────────┤
│ Crédit gratuit      │ $5.00           │
│ Reste               │ $0.50 ✅        │
└─────────────────────┴─────────────────┘
```

✅ **Vous restez dans le plan gratuit !**

---

## 🎯 **Checklist de Déploiement**

- [ ] Compte Railway créé
- [ ] Projet créé depuis GitHub
- [ ] PostgreSQL ajouté
- [ ] Variables d'environnement configurées
- [ ] APP_KEY générée
- [ ] Domaine public généré
- [ ] Build réussi
- [ ] Migrations exécutées
- [ ] Admin créé (seeder)
- [ ] API testée
- [ ] Connexion testée

---

## 🚀 **Prochaines Étapes**

Une fois déployé :

1. ✅ Testez toutes les routes API
2. ✅ Créez des données de test (seeders)
3. ✅ Configurez le frontend pour appeler l'API Railway
4. ✅ Ajoutez un nom de domaine personnalisé (optionnel)

---

## 💡 **Astuces**

### **Domaine Personnalisé (Gratuit)**

Railway permet d'ajouter un domaine personnalisé :
1. Achetez un domaine (ex: Namecheap, ~$10/an)
2. Dans Railway Settings → Domains
3. Ajoutez votre domaine
4. Configurez les DNS

### **Environnements Multiples**

Créez plusieurs environnements :
- **Production** : branche `main`
- **Staging** : branche `develop`
- **Preview** : Pull Requests

### **Sauvegardes PostgreSQL**

Railway sauvegarde automatiquement votre base de données !
- Rétention : 7 jours
- Restauration en 1 clic

---

## ❓ **Questions Fréquentes**

### **Q: Railway est-il vraiment gratuit ?**
**R:** Oui, $5/mois de crédit gratuit, renouvelé chaque mois. Parfait pour l'apprentissage.

### **Q: Que se passe-t-il si je dépasse $5 ?**
**R:** Railway vous demandera d'ajouter une carte bancaire. Vous pouvez aussi mettre l'app en pause.

### **Q: Puis-je utiliser Railway en production ?**
**R:** Oui ! Beaucoup de startups utilisent Railway. Le plan payant commence à $5/mois.

### **Q: Railway vs Heroku ?**
**R:** Railway est meilleur :
- ✅ Plan gratuit (Heroku n'en a plus)
- ✅ Plus rapide
- ✅ Meilleure interface
- ✅ PostgreSQL inclus

---

**Prêt à déployer ? Suivez les étapes ci-dessus et votre application sera en ligne en 10 minutes !** 🚀

**Besoin d'aide ? Dites-moi où vous bloquez !**
