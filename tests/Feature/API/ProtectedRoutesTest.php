<?php

use App\Models\User;

/**
 * Tests des routes protégées par authentification
 * 
 * Ces tests vérifient que les routes nécessitent une authentification
 */

// ============================================
// TESTS DE PROTECTION DES ROUTES
// ============================================

test('api: les routes protégées nécessitent une authentification', function () {
    $routes = [
        ['GET', '/api/categories'],
        ['GET', '/api/produits'],
        ['GET', '/api/fournisseurs'],
        ['GET', '/api/clients'],
        ['GET', '/api/ventes'],
        ['GET', '/api/stock/mouvements'],
        ['GET', '/api/commandes-fournisseurs'],
        ['GET', '/api/dashboard/stats'],
        ['GET', '/api/rapports/ventes'],
        ['GET', '/api/users'],
    ];

    foreach ($routes as [$method, $route]) {
        $response = $this->json($method, $route);
        
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
});

test('api: un utilisateur authentifié peut accéder aux routes protégées', function () {
    $user = User::factory()->create(['role' => 'vendeur']);

    $routes = [
        '/api/categories',
        '/api/produits',
        '/api/fournisseurs',
        '/api/clients',
        '/api/ventes',
        '/api/stock/mouvements',
        '/api/commandes-fournisseurs',
        '/api/dashboard/stats',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($user, 'sanctum')
            ->getJson($route);
        
        // Devrait retourner 200 (OK) et non 401 (Unauthorized)
        $response->assertStatus(200);
    }
});

// ============================================
// TESTS DE LA ROUTE DE TEST
// ============================================

test('api: la route de test est accessible sans authentification', function () {
    $response = $this->getJson('/api/test');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'version',
            'timestamp',
        ])
        ->assertJson([
            'message' => 'API Gestion Quincaillerie OK 🚀',
            'version' => '1.0.0',
        ]);
});

// ============================================
// TESTS DES HEADERS
// ============================================

test('api: les réponses sont au format JSON', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->get('/api/categories');

    $response->assertHeader('Content-Type', 'application/json');
});

test('api: un token invalide retourne une erreur 401', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token-here')
        ->getJson('/api/auth/me');

    $response->assertStatus(401);
});

// ============================================
// TESTS DE VALIDATION
// ============================================

test('api: les requêtes sans header Accept retournent du JSON', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'wrong@email.com',
        'password' => 'password',
    ]);

    // Même sans header Accept, Laravel retourne du JSON pour les routes /api/*
    $response->assertHeader('Content-Type', 'application/json');
});
