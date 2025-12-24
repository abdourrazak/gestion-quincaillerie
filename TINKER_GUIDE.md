# 🔍 Guide d'Exploration des Données avec Tinker

## 🎯 Qu'est-ce que Tinker ?

**Laravel Tinker** est un REPL (Read-Eval-Print Loop) qui vous permet d'interagir avec votre application Laravel en ligne de commande. C'est comme une console JavaScript dans le navigateur, mais pour Laravel !

---

## 🚀 Démarrer Tinker

```bash
php artisan tinker
```

Vous verrez :
```
Psy Shell v0.12.0 (PHP 8.x.x — cli) by Justin Hileman
>
```

---

## 📊 **Commandes de Base**

### **1. Compter les enregistrements**

```php
// Compter les catégories
\App\Models\Categorie::count();
// Résultat : 10

// Compter les produits
\App\Models\Produit::count();
// Résultat : 50

// Compter les fournisseurs
\App\Models\Fournisseur::count();
// Résultat : 5

// Compter les clients
\App\Models\Client::count();
// Résultat : 8
```

### **2. Voir tous les enregistrements**

```php
// Voir toutes les catégories
\App\Models\Categorie::all();

// Voir seulement les noms des catégories
\App\Models\Categorie::pluck('nom');

// Voir les 5 premiers produits
\App\Models\Produit::take(5)->get();
```

### **3. Trouver un enregistrement spécifique**

```php
// Trouver la catégorie avec l'ID 1
$categorie = \App\Models\Categorie::find(1);

// Voir son nom
$categorie->nom;
// Résultat : "Ciment & Béton"

// Voir sa description
$categorie->description;

// Voir sa couleur
$categorie->couleur;
```

---

## 🔗 **Explorer les Relations**

### **1. Produits d'une catégorie**

```php
// Récupérer la catégorie "Ciment & Béton"
$categorie = \App\Models\Categorie::find(1);

// Voir tous ses produits
$categorie->produits;

// Compter ses produits
$categorie->produits->count();

// Voir juste les noms des produits
$categorie->produits->pluck('nom');
```

### **2. Catégorie d'un produit**

```php
// Récupérer un produit
$produit = \App\Models\Produit::first();

// Voir sa catégorie
$produit->categorie;

// Voir le nom de sa catégorie
$produit->categorie->nom;
```

### **3. Fournisseur d'un produit**

```php
// Récupérer un produit
$produit = \App\Models\Produit::first();

// Voir son fournisseur
$produit->fournisseur;

// Voir le nom de l'entreprise
$produit->fournisseur->entreprise;
```

### **4. Produits d'un fournisseur**

```php
// Récupérer un fournisseur
$fournisseur = \App\Models\Fournisseur::first();

// Voir tous ses produits
$fournisseur->produits;

// Compter ses produits
$fournisseur->produits->count();
```

---

## 🎨 **Utiliser les Scopes**

### **1. Produits actifs**

```php
// Tous les produits actifs
\App\Models\Produit::actif()->get();

// Compter les produits actifs
\App\Models\Produit::actif()->count();
```

### **2. Produits en stock faible**

```php
// Produits avec stock <= stock_minimum
\App\Models\Produit::stockFaible()->get();

// Voir leurs noms et stock
\App\Models\Produit::stockFaible()->get()->map(function($p) {
    return $p->nom . ' : ' . $p->stock_actuel . ' unités';
});
```

### **3. Produits en rupture**

```php
// Produits avec stock <= 0
\App\Models\Produit::ruptureStock()->get();
```

### **4. Catégories actives**

```php
// Toutes les catégories actives
\App\Models\Categorie::actif()->get();
```

---

## 💰 **Tester les Accessors (Attributs Calculés)**

### **1. Prix TTC**

```php
// Récupérer un produit
$produit = \App\Models\Produit::first();

// Voir son prix HT
$produit->prix_vente;
// Exemple : 8.90

// Voir son prix TTC (calculé automatiquement)
$produit->prix_vente_ttc;
// Exemple : 10.68 (8.90 + 20% TVA)
```

### **2. Marge**

```php
$produit = \App\Models\Produit::first();

// Prix d'achat
$produit->prix_achat;
// Exemple : 5.50

// Prix de vente
$produit->prix_vente;
// Exemple : 8.90

// Marge (calculée automatiquement)
$produit->marge;
// Exemple : 3.40 (8.90 - 5.50)

// Pourcentage de marge
$produit->pourcentage_marge;
// Exemple : 61.82% ((8.90 - 5.50) / 5.50 * 100)
```

### **3. Stock faible**

```php
$produit = \App\Models\Produit::first();

// Vérifier si le stock est faible
$produit->est_stock_faible;
// Résultat : true ou false

// Vérifier si en rupture
$produit->est_rupture_stock;
// Résultat : true ou false
```

---

## 📈 **Statistiques et Agrégations**

### **1. Produits par catégorie**

```php
// Compter les produits par catégorie
\App\Models\Categorie::withCount('produits')->get()->map(function($c) {
    return $c->nom . ' : ' . $c->produits_count . ' produits';
});
```

### **2. Prix moyen par catégorie**

```php
// Prix moyen de tous les produits
\App\Models\Produit::avg('prix_vente');

// Prix moyen par catégorie
$categorie = \App\Models\Categorie::find(1);
$categorie->produits()->avg('prix_vente');
```

### **3. Stock total**

```php
// Stock total de tous les produits
\App\Models\Produit::sum('stock_actuel');

// Valeur totale du stock (prix d'achat × stock)
\App\Models\Produit::all()->sum(function($p) {
    return $p->prix_achat * $p->stock_actuel;
});
```

---

## 🔍 **Recherches Avancées**

### **1. Rechercher par nom**

```php
// Produits contenant "ciment"
\App\Models\Produit::where('nom', 'like', '%ciment%')->get();

// Catégories contenant "bois"
\App\Models\Categorie::where('nom', 'like', '%bois%')->get();
```

### **2. Filtrer par prix**

```php
// Produits moins de 10€
\App\Models\Produit::where('prix_vente', '<', 10)->get();

// Produits entre 10€ et 50€
\App\Models\Produit::whereBetween('prix_vente', [10, 50])->get();

// Produits plus de 100€
\App\Models\Produit::where('prix_vente', '>', 100)->get();
```

### **3. Filtrer par stock**

```php
// Produits avec plus de 100 en stock
\App\Models\Produit::where('stock_actuel', '>', 100)->get();

// Produits avec stock entre 50 et 100
\App\Models\Produit::whereBetween('stock_actuel', [50, 100])->get();
```

---

## 🎯 **Exemples Pratiques**

### **Exemple 1 : Top 5 des produits les plus chers**

```php
\App\Models\Produit::orderBy('prix_vente', 'desc')->take(5)->get()->map(function($p) {
    return $p->nom . ' : ' . $p->prix_vente . '€';
});
```

### **Exemple 2 : Produits d'une catégorie avec leur fournisseur**

```php
\App\Models\Produit::where('categorie_id', 1)
    ->with(['categorie', 'fournisseur'])
    ->get()
    ->map(function($p) {
        return [
            'produit' => $p->nom,
            'categorie' => $p->categorie->nom,
            'fournisseur' => $p->fournisseur->entreprise,
            'prix' => $p->prix_vente . '€'
        ];
    });
```

### **Exemple 3 : Clients professionnels**

```php
// Clients avec une entreprise
\App\Models\Client::whereNotNull('entreprise')->get();

// Leurs noms et entreprises
\App\Models\Client::whereNotNull('entreprise')->get()->map(function($c) {
    return $c->nom_complet . ' - ' . $c->entreprise;
});
```

### **Exemple 4 : Valeur du stock par catégorie**

```php
\App\Models\Categorie::all()->map(function($cat) {
    $valeur = $cat->produits->sum(function($p) {
        return $p->prix_achat * $p->stock_actuel;
    });
    return $cat->nom . ' : ' . number_format($valeur, 2) . '€';
});
```

---

## 🛠️ **Commandes Utiles**

### **Quitter Tinker**

```php
exit
// ou Ctrl+D
```

### **Effacer l'écran**

```php
clear
```

### **Voir l'aide**

```php
help
```

### **Voir les méthodes d'un modèle**

```php
$produit = \App\Models\Produit::first();
get_class_methods($produit);
```

---

## 💡 **Astuces**

### **1. Utiliser des variables**

```php
// Stocker dans une variable
$produits = \App\Models\Produit::all();

// Réutiliser
$produits->count();
$produits->first();
```

### **2. Formater la sortie**

```php
// Afficher en JSON
\App\Models\Categorie::all()->toJson();

// Afficher en tableau
\App\Models\Categorie::all()->toArray();
```

### **3. Déboguer**

```php
// Voir la requête SQL
\App\Models\Produit::where('prix_vente', '>', 50)->toSql();

// Voir les requêtes SQL avec les valeurs
\DB::enableQueryLog();
\App\Models\Produit::where('prix_vente', '>', 50)->get();
\DB::getQueryLog();
```

---

## 🎯 **Exercices Pratiques**

Essayez ces commandes dans Tinker :

1. Trouvez tous les produits de la catégorie "Quincaillerie"
2. Calculez la valeur totale du stock
3. Trouvez les 3 produits avec la meilleure marge
4. Listez tous les fournisseurs avec le nombre de produits qu'ils fournissent
5. Trouvez tous les produits en stock faible

---

**Prêt à explorer vos données ? Lancez Tinker et amusez-vous !** 🚀

```bash
php artisan tinker
```
