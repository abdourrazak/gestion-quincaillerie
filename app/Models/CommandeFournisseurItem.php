<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeFournisseurItem extends Model
{
    use HasFactory;

    protected $table = 'commande_fournisseur_items';

    protected $fillable = [
        'commande_fournisseur_id',
        'produit_id',
        'quantite_commandee',
        'quantite_recue',
        'prix_unitaire',
        'total',
    ];

    protected $casts = [
        'quantite_commandee' => 'integer',
        'quantite_recue' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relation avec la commande fournisseur
     */
    public function commandeFournisseur(): BelongsTo
    {
        return $this->belongsTo(CommandeFournisseur::class, 'commande_fournisseur_id');
    }

    /**
     * Relation avec le produit
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Calculer le total automatiquement avant la sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->total = $item->quantite_commandee * $item->prix_unitaire;
        });

        static::updating(function ($item) {
            $item->total = $item->quantite_commandee * $item->prix_unitaire;
        });
    }

    /**
     * Quantité restante à recevoir
     */
    public function getQuantiteRestanteAttribute(): int
    {
        return max(0, $this->quantite_commandee - $this->quantite_recue);
    }

    /**
     * Vérifier si l'item est complètement reçu
     */
    public function getEstCompletAttribute(): bool
    {
        return $this->quantite_recue >= $this->quantite_commandee;
    }
}
