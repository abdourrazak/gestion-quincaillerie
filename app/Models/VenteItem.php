<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenteItem extends Model
{
    use HasFactory;

    protected $table = 'vente_items';

    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'tva',
        'montant_tva',
        'sous_total',
        'total',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'tva' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'sous_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relation avec la vente
     */
    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }

    /**
     * Relation avec le produit
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Calculer les montants automatiquement avant la sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->sous_total = $item->quantite * $item->prix_unitaire;
            $item->montant_tva = $item->sous_total * ($item->tva / 100);
            $item->total = $item->sous_total + $item->montant_tva;
        });

        static::updating(function ($item) {
            $item->sous_total = $item->quantite * $item->prix_unitaire;
            $item->montant_tva = $item->sous_total * ($item->tva / 100);
            $item->total = $item->sous_total + $item->montant_tva;
        });
    }
}
