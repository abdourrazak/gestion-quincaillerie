# 🎯 Guide de Test des Contrôleurs API

## ✅ **Contrôleurs Implémentés**

### **1. CategoryController** ✅
- CRUD complet
- Toggle actif/inactif
- Comptage des produits

### **2. ProductController** ✅
- CRUD complet
- Recherche et filtres
- Recherche par code-barre
- Gestion du stock
- Gestion des promotions
- Pagination

---

## 🔐 **Étape 1 : Obtenir un Token**

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@quincaillerie.com","password":"Admin@2025"}'
```

**Réponse :**
```json
{
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "Administrateur",
    "email": "admin@quincaillerie.com",
    "role": "admin"
  },
  "token": "2|rYFzVoHoYRmStvbjsvR1sBj3cRpSTR6Fte7yyhle74466b80"
}
```

**Copiez le token** pour les requêtes suivantes.

---

## 📦 **Tests des Catégories**

### **1. Liste toutes les catégories**
```bash
curl http://localhost:8000/api/categories \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

**Réponse :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Ciment & Béton",
      "slug": "ciment-beton",
      "description": "...",
      "icone": "Package",
      "couleur": "#6B7280",
      "actif": true,
      "produits_count": 5
    }
  ],
  "total": 10
}
```

### **2. Voir une catégorie spécifique**
```bash
curl http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **3. Créer une nouvelle catégorie**
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Isolation",
    "description": "Matériaux d'\''isolation thermique et phonique",
    "icone": "Shield",
    "couleur": "#10B981"
  }'
```

### **4. Mettre à jour une catégorie**
```bash
curl -X PUT http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Nouvelle description"
  }'
```

### **5. Activer/Désactiver une catégorie**
```bash
curl -X POST http://localhost:8000/api/categories/1/toggle \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **6. Supprimer une catégorie**
```bash
curl -X DELETE http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

---

## 🛍️ **Tests des Produits**

### **1. Liste tous les produits (avec pagination)**
```bash
curl "http://localhost:8000/api/produits?per_page=10" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

**Réponse :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Ciment gris 25kg",
      "reference": "REF-CIM-0001",
      "prix_vente": "8.90",
      "stock_actuel": 200,
      "categorie": {
        "id": 1,
        "nom": "Ciment & Béton"
      },
      "fournisseur": {
        "id": 1,
        "entreprise": "Matériaux Dupont SA"
      }
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 10,
    "current_page": 1,
    "last_page": 5
  }
}
```

### **2. Rechercher des produits**
```bash
# Par nom
curl "http://localhost:8000/api/produits?search=ciment" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"

# Par catégorie
curl "http://localhost:8000/api/produits?categorie_id=1" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"

# Stock faible
curl "http://localhost:8000/api/produits?stock_faible=true" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"

# En promotion
curl "http://localhost:8000/api/produits?en_promotion=true" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **3. Rechercher par code-barre**
```bash
curl http://localhost:8000/api/produits/code-barre/8162286280023 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **4. Voir un produit spécifique**
```bash
curl http://localhost:8000/api/produits/1 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **5. Créer un nouveau produit**
```bash
curl -X POST http://localhost:8000/api/produits \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Sable fin 25kg",
    "reference": "REF-SAB-0001",
    "code_barre": "1234567890123",
    "categorie_id": 1,
    "fournisseur_id": 1,
    "description": "Sable fin pour mortier",
    "prix_achat": 3.50,
    "prix_vente": 5.90,
    "stock_actuel": 100,
    "stock_minimum": 20,
    "unite": "sac"
  }'
```

### **6. Mettre à jour un produit**
```bash
curl -X PUT http://localhost:8000/api/produits/1 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "prix_vente": 9.90,
    "stock_actuel": 180
  }'
```

### **7. Mettre à jour le stock**
```bash
# Ajouter du stock
curl -X POST http://localhost:8000/api/produits/1/stock \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "quantite": 50,
    "type": "ajout",
    "motif": "Réception commande fournisseur"
  }'

# Retirer du stock
curl -X POST http://localhost:8000/api/produits/1/stock \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "quantite": 10,
    "type": "retrait",
    "motif": "Vente"
  }'

# Ajuster le stock
curl -X POST http://localhost:8000/api/produits/1/stock \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "quantite": 150,
    "type": "ajustement",
    "motif": "Inventaire"
  }'
```

### **8. Mettre en promotion**
```bash
curl -X POST http://localhost:8000/api/produits/1/promotion \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "prix_promotion": 7.90,
    "date_debut": "2025-12-24",
    "date_fin": "2025-12-31"
  }'
```

### **9. Retirer la promotion**
```bash
curl -X DELETE http://localhost:8000/api/produits/1/promotion \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **10. Activer/Désactiver un produit**
```bash
curl -X POST http://localhost:8000/api/produits/1/toggle \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **11. Supprimer un produit (soft delete)**
```bash
curl -X DELETE http://localhost:8000/api/produits/1 \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

---

## 🎯 **Filtres et Tri Avancés**

### **Combiner plusieurs filtres**
```bash
curl "http://localhost:8000/api/produits?categorie_id=1&search=ciment&sort_by=prix_vente&sort_order=asc&per_page=20" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"
```

### **Paramètres disponibles**
- `search` - Recherche par nom, référence ou code-barre
- `categorie_id` - Filtrer par catégorie
- `fournisseur_id` - Filtrer par fournisseur
- `stock_faible` - Produits en stock faible (true/false)
- `rupture_stock` - Produits en rupture (true/false)
- `en_promotion` - Produits en promotion (true/false)
- `actif` - Produits actifs/inactifs (true/false)
- `sort_by` - Trier par (nom, prix_vente, stock_actuel, etc.)
- `sort_order` - Ordre (asc/desc)
- `per_page` - Nombre par page (défaut: 15)

---

## 📊 **Voir les Requêtes dans Telescope**

1. **Ouvrez Telescope** : `http://localhost:8000/telescope`
2. **Cliquez sur "Queries"**
3. **Voyez toutes les requêtes SQL exécutées**

---

## ✅ **Tests Réussis**

- ✅ Authentification fonctionne
- ✅ CategoryController fonctionne
- ✅ ProductController fonctionne
- ✅ Filtres et recherche fonctionnent
- ✅ Pagination fonctionne
- ✅ Relations (categorie, fournisseur) fonctionnent

---

## 🚀 **Prochaines Étapes**

1. Tester avec Postman (collection à créer)
2. Implémenter les autres contrôleurs (Clients, Fournisseurs, Ventes)
3. Ajouter plus de tests automatisés
4. Créer le frontend React

---

**Félicitations ! Vos contrôleurs API fonctionnent parfaitement !** 🎉
