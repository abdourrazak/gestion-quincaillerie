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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->onDelete('set null');
            
            $table->enum('type', ['entree', 'sortie', 'ajustement', 'retour'])->default('entree');
            $table->integer('quantite');
            $table->integer('stock_avant');
            $table->integer('stock_apres');
            
            $table->string('motif')->nullable(); // Raison du mouvement
            $table->text('notes')->nullable();
            
            $table->timestamp('date_mouvement')->useCurrent();
            $table->timestamps();
            
            // Index
            $table->index('produit_id');
            $table->index('type');
            $table->index('date_mouvement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
