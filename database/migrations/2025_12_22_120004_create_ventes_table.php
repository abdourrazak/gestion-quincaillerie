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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Vendeur
            
            // Montants
            $table->decimal('sous_total', 10, 2);
            $table->decimal('montant_tva', 10, 2);
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_remise', 10, 2)->default(0);
            
            // Paiement
            $table->enum('mode_paiement', ['especes', 'carte', 'cheque', 'virement', 'autre'])->default('especes');
            $table->enum('statut_paiement', ['paye', 'partiel', 'impaye'])->default('paye');
            $table->decimal('montant_paye', 10, 2);
            $table->decimal('montant_rendu', 10, 2)->default(0);
            
            // Métadonnées
            $table->text('notes')->nullable();
            $table->timestamp('date_vente')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('numero_facture');
            $table->index('client_id');
            $table->index('user_id');
            $table->index('date_vente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
