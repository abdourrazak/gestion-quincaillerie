<?php

use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests du modèle Produit
 * 
 * Ces tests vérifient la logique métier du modèle Produit
 */

uses(RefreshDatabase::class);

// ============================================
// TESTS DES RELATIONS
// ============================================

test('un produit appartient à une catégorie', function () {
    $categorie = Categorie::factory()->create(['nom' => 'Ciment']);
    $produit = Produit::factory()->create(['categorie_id' => $categorie->id]);

    expect($produit->categorie)->toBeInstanceOf(Categorie::class);
    expect($produit->categorie->nom)->toBe('Ciment');
});

test('un produit peut avoir un fournisseur', function () {
    $fournisseur = Fournisseur::factory()->create(['nom' => 'Fournisseur Test']);
    $produit = Produit::factory()->create(['fournisseur_id' => $fournisseur->id]);

    expect($produit->fournisseur)->toBeInstanceOf(Fournisseur::class);
    expect($produit->fournisseur->nom)->toBe('Fournisseur Test');
});

test('un produit peut ne pas avoir de fournisseur', function () {
    $produit = Produit::factory()->create(['fournisseur_id' => null]);

    expect($produit->fournisseur)->toBeNull();
});

// ============================================
// TESTS DES SCOPES
// ============================================

test('scope actif retourne uniquement les produits actifs', function () {
    Produit::factory()->create(['nom' => 'Produit Actif', 'actif' => true]);
    Produit::factory()->create(['nom' => 'Produit Inactif', 'actif' => false]);

    $produitsActifs = Produit::actif()->get();

    expect($produitsActifs)->toHaveCount(1);
    expect($produitsActifs->first()->nom)->toBe('Produit Actif');
});

test('scope stockFaible retourne les produits avec stock faible', function () {
    Produit::factory()->create([
        'nom' => 'Stock OK',
        'stock_actuel' => 50,
        'stock_minimum' => 10,
    ]);
    
    Produit::factory()->create([
        'nom' => 'Stock Faible',
        'stock_actuel' => 5,
        'stock_minimum' => 10,
    ]);

    $produitsFaibles = Produit::stockFaible()->get();

    expect($produitsFaibles)->toHaveCount(1);
    expect($produitsFaibles->first()->nom)->toBe('Stock Faible');
});

test('scope ruptureStock retourne les produits en rupture', function () {
    Produit::factory()->create(['nom' => 'En stock', 'stock_actuel' => 10]);
    Produit::factory()->create(['nom' => 'Rupture', 'stock_actuel' => 0]);
    Produit::factory()->create(['nom' => 'Rupture négatif', 'stock_actuel' => -5]);

    $produitsRupture = Produit::ruptureStock()->get();

    expect($produitsRupture)->toHaveCount(2);
});

test('scope enPromotion retourne les produits en promotion active', function () {
    // Promotion active
    Produit::factory()->create([
        'nom' => 'Promo Active',
        'en_promotion' => true,
        'date_debut_promotion' => now()->subDays(1),
        'date_fin_promotion' => now()->addDays(1),
    ]);

    // Promotion expirée
    Produit::factory()->create([
        'nom' => 'Promo Expirée',
        'en_promotion' => true,
        'date_debut_promotion' => now()->subDays(10),
        'date_fin_promotion' => now()->subDays(1),
    ]);

    // Promotion future
    Produit::factory()->create([
        'nom' => 'Promo Future',
        'en_promotion' => true,
        'date_debut_promotion' => now()->addDays(1),
        'date_fin_promotion' => now()->addDays(10),
    ]);

    $promosActives = Produit::enPromotion()->get();

    expect($promosActives)->toHaveCount(1);
    expect($promosActives->first()->nom)->toBe('Promo Active');
});

// ============================================
// TESTS DES ACCESSORS (ATTRIBUTS CALCULÉS)
// ============================================

test('est_stock_faible retourne true si stock <= stock_minimum', function () {
    $produit = Produit::factory()->create([
        'stock_actuel' => 5,
        'stock_minimum' => 10,
    ]);

    expect($produit->est_stock_faible)->toBeTrue();
});

test('est_stock_faible retourne false si stock > stock_minimum', function () {
    $produit = Produit::factory()->create([
        'stock_actuel' => 20,
        'stock_minimum' => 10,
    ]);

    expect($produit->est_stock_faible)->toBeFalse();
});

test('est_rupture_stock retourne true si stock <= 0', function () {
    $produit = Produit::factory()->create(['stock_actuel' => 0]);

    expect($produit->est_rupture_stock)->toBeTrue();
});

test('prix_vente_ttc calcule correctement le prix TTC', function () {
    $produit = Produit::factory()->create([
        'prix_vente' => 100.00,
        'tva' => 20.00, // 20%
    ]);

    expect($produit->prix_vente_ttc)->toBe(120.0);
});

test('prix_effectif retourne le prix promotion si active', function () {
    $produit = Produit::factory()->create([
        'prix_vente' => 100.00,
        'en_promotion' => true,
        'prix_promotion' => 80.00,
        'date_debut_promotion' => now()->subDays(1),
        'date_fin_promotion' => now()->addDays(1),
    ]);

    expect($produit->prix_effectif)->toBe(80.0);
});

test('prix_effectif retourne le prix normal si pas de promotion', function () {
    $produit = Produit::factory()->create([
        'prix_vente' => 100.00,
        'en_promotion' => false,
    ]);

    expect($produit->prix_effectif)->toBe(100.0);
});

test('prix_effectif retourne le prix normal si promotion expirée', function () {
    $produit = Produit::factory()->create([
        'prix_vente' => 100.00,
        'en_promotion' => true,
        'prix_promotion' => 80.00,
        'date_debut_promotion' => now()->subDays(10),
        'date_fin_promotion' => now()->subDays(1),
    ]);

    expect($produit->prix_effectif)->toBe(100.0);
});

test('marge calcule la différence entre prix de vente et prix d\'achat', function () {
    $produit = Produit::factory()->create([
        'prix_achat' => 50.00,
        'prix_vente' => 100.00,
    ]);

    expect($produit->marge)->toBe(50.0);
});

test('pourcentage_marge calcule le pourcentage de marge', function () {
    $produit = Produit::factory()->create([
        'prix_achat' => 50.00,
        'prix_vente' => 100.00,
    ]);

    // Marge = (100 - 50) / 50 * 100 = 100%
    expect($produit->pourcentage_marge)->toBe(100.0);
});

test('pourcentage_marge retourne 0 si prix_achat est 0', function () {
    $produit = Produit::factory()->create([
        'prix_achat' => 0,
        'prix_vente' => 100.00,
    ]);

    expect($produit->pourcentage_marge)->toBe(0.0);
});

// ============================================
// TESTS DE VALIDATION
// ============================================

test('un produit nécessite un nom', function () {
    $this->expectException(\Illuminate\Database\QueryException::class);
    
    Produit::factory()->create(['nom' => null]);
});

test('un produit nécessite une référence unique', function () {
    Produit::factory()->create(['reference' => 'REF-001']);

    $this->expectException(\Illuminate\Database\QueryException::class);
    
    Produit::factory()->create(['reference' => 'REF-001']);
});

test('un code-barre doit être unique', function () {
    Produit::factory()->create(['code_barre' => '123456789']);

    $this->expectException(\Illuminate\Database\QueryException::class);
    
    Produit::factory()->create(['code_barre' => '123456789']);
});

// ============================================
// TESTS DE SOFT DELETE
// ============================================

test('un produit supprimé n\'apparaît pas dans les requêtes normales', function () {
    $produit = Produit::factory()->create(['nom' => 'Produit à supprimer']);
    
    $produit->delete();

    expect(Produit::all())->toHaveCount(0);
});

test('un produit supprimé peut être récupéré avec withTrashed', function () {
    $produit = Produit::factory()->create(['nom' => 'Produit à supprimer']);
    
    $produit->delete();

    $produits = Produit::withTrashed()->get();
    
    expect($produits)->toHaveCount(1);
    expect($produits->first()->deleted_at)->not->toBeNull();
});

test('un produit supprimé peut être restauré', function () {
    $produit = Produit::factory()->create(['nom' => 'Produit à restaurer']);
    
    $produit->delete();
    expect(Produit::all())->toHaveCount(0);
    
    $produit->restore();
    expect(Produit::all())->toHaveCount(1);
});

// ============================================
// TESTS DES TYPES DE DONNÉES
// ============================================

test('images_supplementaires est casté en array', function () {
    $produit = Produit::factory()->create([
        'images_supplementaires' => ['image1.jpg', 'image2.jpg'],
    ]);

    expect($produit->images_supplementaires)->toBeArray();
    expect($produit->images_supplementaires)->toHaveCount(2);
});

test('les prix sont castés en decimal', function () {
    $produit = Produit::factory()->create([
        'prix_vente' => '99.99',
        'prix_achat' => '50.50',
    ]);

    // Laravel retourne les decimals comme des strings
    expect($produit->prix_vente)->toBeString();
    expect($produit->prix_achat)->toBeString();
    expect((float) $produit->prix_vente)->toBe(99.99);
    expect((float) $produit->prix_achat)->toBe(50.50);
});

test('actif est casté en boolean', function () {
    $produit = Produit::factory()->create(['actif' => 1]);

    expect($produit->actif)->toBeTrue();
    expect($produit->actif)->toBeBool();
});
