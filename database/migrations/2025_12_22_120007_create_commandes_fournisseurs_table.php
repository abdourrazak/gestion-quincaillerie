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
        Schema::create('commandes_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Créateur de la commande
            
            $table->enum('statut', ['brouillon', 'envoyee', 'confirmee', 'recue', 'annulee'])->default('brouillon');
            
            $table->decimal('montant_total', 10, 2);
            $table->text('notes')->nullable();
            
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('numero_commande');
            $table->index('fournisseur_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes_fournisseurs');
    }
};
