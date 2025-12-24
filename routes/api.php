<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SupplierController;
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
    
    // ============================================
    // CATÉGORIES
    // ============================================
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::post('/{id}/toggle', [CategoryController::class, 'toggle']);
    });

    // ============================================
    // PRODUITS
    // ============================================
    Route::prefix('produits')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/code-barre/{codeBarre}', [ProductController::class, 'findByBarcode']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::post('/{id}/toggle', [ProductController::class, 'toggle']);
        Route::post('/{id}/stock', [ProductController::class, 'updateStock']);
        Route::post('/{id}/promotion', [ProductController::class, 'setPromotion']);
        Route::delete('/{id}/promotion', [ProductController::class, 'removePromotion']);
    });

    // ============================================
    // FOURNISSEURS
    // ============================================
    Route::prefix('fournisseurs')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/{id}', [SupplierController::class, 'show']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
        Route::post('/{id}/toggle', [SupplierController::class, 'toggle']);
        Route::get('/{id}/produits', [SupplierController::class, 'products']);
    });

    // ============================================
    // CLIENTS
    // ============================================
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{id}', [ClientController::class, 'show']);
        Route::put('/{id}', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);
        Route::post('/{id}/toggle', [ClientController::class, 'toggle']);
        Route::get('/{id}/achats', [ClientController::class, 'purchases']);
    });

    // ============================================
    // VENTES
    // ============================================
    Route::prefix('ventes')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des ventes']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer une vente']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => 'Détails de la vente ' . $id]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => 'Mettre à jour la vente ' . $id]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => 'Supprimer la vente ' . $id]);
        });
        Route::get('/{id}/facture', function ($id) {
            return response()->json(['message' => 'Générer la facture ' . $id]);
        });
    });

    // ============================================
    // STOCK
    // ============================================
    Route::prefix('stock')->group(function () {
        Route::get('/alertes', function () {
            return response()->json(['message' => 'Alertes de stock']);
        });
        Route::get('/mouvements', function () {
            return response()->json(['message' => 'Liste des mouvements de stock']);
        });
        Route::post('/mouvement', function () {
            return response()->json(['message' => 'Créer un mouvement de stock']);
        });
        Route::get('/inventaire', function () {
            return response()->json(['message' => 'Inventaire complet']);
        });
    });

    // ============================================
    // COMMANDES FOURNISSEURS
    // ============================================
    Route::prefix('commandes-fournisseurs')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des commandes fournisseurs']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer une commande fournisseur']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => 'Détails de la commande ' . $id]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => 'Mettre à jour la commande ' . $id]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => 'Supprimer la commande ' . $id]);
        });
        Route::post('/{id}/recevoir', function ($id) {
            return response()->json(['message' => 'Recevoir la commande ' . $id]);
        });
    });

    // ============================================
    // DASHBOARD
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', function () {
            return response()->json(['message' => 'Statistiques du dashboard']);
        });
        Route::get('/ventes-jour', function () {
            return response()->json(['message' => 'Ventes du jour']);
        });
        Route::get('/ventes-mois', function () {
            return response()->json(['message' => 'Ventes du mois']);
        });
        Route::get('/top-produits', function () {
            return response()->json(['message' => 'Top produits']);
        });
    });

    // ============================================
    // RAPPORTS
    // ============================================
    Route::prefix('rapports')->group(function () {
        Route::get('/ventes', function () {
            return response()->json(['message' => 'Rapport des ventes']);
        });
        Route::get('/stock', function () {
            return response()->json(['message' => 'Rapport du stock']);
        });
        Route::get('/benefices', function () {
            return response()->json(['message' => 'Rapport des bénéfices']);
        });
        Route::get('/clients', function () {
            return response()->json(['message' => 'Rapport des clients']);
        });
    });

    // ============================================
    // UTILISATEURS (Admin uniquement)
    // ============================================
    Route::prefix('users')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Liste des utilisateurs']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Créer un utilisateur']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => 'Détails de l\'utilisateur ' . $id]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => 'Mettre à jour l\'utilisateur ' . $id]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => 'Supprimer l\'utilisateur ' . $id]);
        });
    });
});
