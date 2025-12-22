<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes API pour l'application de gestion de quincaillerie
|
*/

// Route de test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Gestion Quincaillerie OK 🚀',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Routes d'authentification (publiques)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Routes protégées par authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });
});

// Routes protégées par authentification Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Routes pour les catégories
    Route::prefix('categories')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des catégories']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer une catégorie']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Détails de la catégorie {$id}"]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Modifier la catégorie {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Supprimer la catégorie {$id}"]);
        });
    });

    // Routes pour les produits
    Route::prefix('produits')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des produits']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer un produit']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Détails du produit {$id}"]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Modifier le produit {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Supprimer le produit {$id}"]);
        });
        Route::get('/code-barre/{code}', function ($code) {
            return response()->json(['message' => "Recherche par code-barre: {$code}"]);
        });
    });

    // Routes pour les fournisseurs
    Route::prefix('fournisseurs')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des fournisseurs']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer un fournisseur']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Détails du fournisseur {$id}"]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Modifier le fournisseur {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Supprimer le fournisseur {$id}"]);
        });
    });

    // Routes pour les clients
    Route::prefix('clients')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des clients']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer un client']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Détails du client {$id}"]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Modifier le client {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Supprimer le client {$id}"]);
        });
    });

    // Routes pour les ventes
    Route::prefix('ventes')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des ventes']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer une vente']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Détails de la vente {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Annuler la vente {$id}"]);
        });
        Route::get('/{id}/facture', function ($id) {
            return response()->json(['message' => "Générer la facture {$id}"]);
        });
    });

    // Routes pour le stock
    Route::prefix('stock')->group(function () {
        Route::get('/mouvements', function () {
            return response()->json(['message' => 'Liste des mouvements de stock']);
        });
        Route::post('/entree', function () {
            return response()->json(['message' => 'Entrée de stock']);
        });
        Route::post('/sortie', function () {
            return response()->json(['message' => 'Sortie de stock']);
        });
        Route::post('/ajustement', function () {
            return response()->json(['message' => 'Ajustement de stock']);
        });
        Route::get('/alertes', function () {
            return response()->json(['message' => 'Alertes de stock faible']);
        });
    });

    // Routes pour les commandes fournisseurs
    Route::prefix('commandes-fournisseurs')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des commandes fournisseurs']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer une commande fournisseur']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => "Détails de la commande {$id}"]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Modifier la commande {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Supprimer la commande {$id}"]);
        });
        Route::post('/{id}/recevoir', function ($id) {
            return response()->json(['message' => "Recevoir la commande {$id}"]);
        });
    });

    // Routes pour le tableau de bord
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', function () {
            return response()->json(['message' => 'Statistiques du tableau de bord']);
        });
        Route::get('/ventes-jour', function () {
            return response()->json(['message' => 'Ventes du jour']);
        });
        Route::get('/ventes-mois', function () {
            return response()->json(['message' => 'Ventes du mois']);
        });
        Route::get('/produits-populaires', function () {
            return response()->json(['message' => 'Produits les plus vendus']);
        });
    });

    // Routes pour les rapports
    Route::prefix('rapports')->group(function () {
        Route::get('/ventes', function () {
            return response()->json(['message' => 'Rapport des ventes']);
        });
        Route::get('/stock', function () {
            return response()->json(['message' => 'Rapport de stock']);
        });
        Route::get('/benefices', function () {
            return response()->json(['message' => 'Rapport des bénéfices']);
        });
    });

    // Routes pour les utilisateurs (admin uniquement)
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des utilisateurs']);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => "Modifier l'utilisateur {$id}"]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => "Supprimer l'utilisateur {$id}"]);
        });
    });
});
