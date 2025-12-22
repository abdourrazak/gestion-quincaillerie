<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique(); // SKU
            $table->string('code_barre')->unique()->nullable();
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->onDelete('set null');
            $table->text('description')->nullable();
            
            // Prix
            $table->decimal('prix_achat', 10, 2)->default(0);
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('tva', 5, 2)->default(20.00); // TVA en pourcentage
            
            // Stock
            $table->integer('stock_actuel')->default(0);
            $table->integer('stock_minimum')->default(10);
            $table->integer('stock_maximum')->nullable();
            
            // Unité de mesure
            $table->enum('unite', ['piece', 'sac', 'm2', 'litre', 'kg', 'metre', 'rouleau', 'boite'])->default('piece');
            
            // Images
            $table->string('image_principale')->nullable();
            $table->json('images_supplementaires')->nullable();
            
            // Métadonnées
            $table->boolean('actif')->default(true);
            $table->boolean('en_promotion')->default(false);
            $table->decimal('prix_promotion', 10, 2)->nullable();
            $table->date('date_debut_promotion')->nullable();
            $table->date('date_fin_promotion')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour améliorer les performances
            $table->index('reference');
            $table->index('code_barre');
            $table->index('categorie_id');
            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
