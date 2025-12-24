<?php

use App\Models\User;

/**
 * Tests d'authentification API
 * 
 * Ces tests vérifient que l'authentification via Laravel Sanctum fonctionne correctement
 */

// ============================================
// TESTS DE CONNEXION (LOGIN)
// ============================================

test('api: un utilisateur peut se connecter avec des identifiants valides', function () {
    // Arrange (Préparation)
    $user = User::factory()->create([
        'email' => 'test@quincaillerie.com',
        'password' => bcrypt('password123'),
        'role' => 'vendeur',
        'actif' => true,
    ]);

    // Act (Action)
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@quincaillerie.com',
        'password' => 'password123',
    ]);

    // Assert (Vérification)
    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'role'],
            'token',
        ])
        ->assertJson([
            'message' => 'Connexion réussie',
            'user' => [
                'email' => 'test@quincaillerie.com',
                'role' => 'vendeur',
            ],
        ]);

    // Vérifier que le token est bien généré
    expect($response->json('token'))->not->toBeNull();
});

test('api: la connexion échoue avec un email incorrect', function () {
    $user = User::factory()->create([
        'email' => 'test@quincaillerie.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'wrong@email.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('api: la connexion échoue avec un mot de passe incorrect', function () {
    $user = User::factory()->create([
        'email' => 'test@quincaillerie.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@quincaillerie.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('api: la connexion échoue si le compte est désactivé', function () {
    $user = User::factory()->create([
        'email' => 'test@quincaillerie.com',
        'password' => bcrypt('password123'),
        'actif' => false, // Compte désactivé
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@quincaillerie.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('api: la connexion nécessite un email', function () {
    $response = $this->postJson('/api/auth/login', [
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('api: la connexion nécessite un mot de passe', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@quincaillerie.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

// ============================================
// TESTS D'INSCRIPTION (REGISTER)
// ============================================

test('api: un admin peut créer un nouvel utilisateur', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/auth/register', [
            'name' => 'Nouveau Vendeur',
            'email' => 'vendeur@quincaillerie.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'vendeur',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'role'],
        ])
        ->assertJson([
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'name' => 'Nouveau Vendeur',
                'email' => 'vendeur@quincaillerie.com',
                'role' => 'vendeur',
            ],
        ]);

    // Vérifier que l'utilisateur existe dans la base de données
    $this->assertDatabaseHas('users', [
        'email' => 'vendeur@quincaillerie.com',
        'role' => 'vendeur',
    ]);
});

test('api: l\'inscription nécessite une confirmation de mot de passe', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@quincaillerie.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'role' => 'vendeur',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('api: l\'inscription nécessite un email unique', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['email' => 'existing@quincaillerie.com']);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'existing@quincaillerie.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'vendeur',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('api: l\'inscription nécessite un rôle valide', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@quincaillerie.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['role']);
});

// ============================================
// TESTS DE PROFIL UTILISATEUR
// ============================================

test('api: un utilisateur authentifié peut récupérer son profil', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@quincaillerie.com',
        'role' => 'vendeur',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => 'John Doe',
                'email' => 'john@quincaillerie.com',
                'role' => 'vendeur',
            ],
        ]);
});

test('api: un utilisateur non authentifié ne peut pas accéder au profil', function () {
    $response = $this->getJson('/api/auth/me');

    $response->assertStatus(401);
});

test('api: un utilisateur peut mettre à jour son profil', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@quincaillerie.com',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/auth/profile', [
            'name' => 'New Name',
            'email' => 'new@quincaillerie.com',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'name' => 'New Name',
                'email' => 'new@quincaillerie.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@quincaillerie.com',
    ]);
});

// ============================================
// TESTS DE CHANGEMENT DE MOT DE PASSE
// ============================================

test('api: un utilisateur peut changer son mot de passe', function () {
    $user = User::factory()->create([
        'password' => bcrypt('oldpassword'),
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/auth/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Mot de passe modifié avec succès',
        ]);

    // Vérifier que le nouveau mot de passe fonctionne
    $this->assertTrue(
        \Hash::check('newpassword123', $user->fresh()->password)
    );
});

test('api: le changement de mot de passe échoue avec un mauvais mot de passe actuel', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correctpassword'),
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/auth/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['current_password']);
});

// ============================================
// TESTS DE DÉCONNEXION (LOGOUT)
// ============================================

test('api: un utilisateur peut se déconnecter', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Déconnexion réussie',
        ]);

    // Vérifier que le token a été supprimé
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

test('api: la déconnexion nécessite une authentification', function () {
    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(401);
});

// ============================================
// TESTS DE SÉCURITÉ
// ============================================

test('api: les anciens tokens sont supprimés à la connexion', function () {
    $user = User::factory()->create([
        'email' => 'test@quincaillerie.com',
        'password' => bcrypt('password123'),
    ]);

    // Créer un ancien token
    $oldToken = $user->createToken('old-token')->plainTextToken;

    // Se connecter (devrait supprimer l'ancien token)
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@quincaillerie.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);

    // Vérifier que l'ancien token ne fonctionne plus
    $testResponse = $this->withHeader('Authorization', 'Bearer ' . $oldToken)
        ->getJson('/api/auth/me');

    $testResponse->assertStatus(401);
});
