<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'produit_id',
        'user_id',
        'vente_id',
        'type',
        'quantite',
        'stock_avant',
        'stock_apres',
        'motif',
        'notes',
        'date_mouvement',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'stock_avant' => 'integer',
        'stock_apres' => 'integer',
        'date_mouvement' => 'datetime',
    ];

    /**
     * Relation avec le produit
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec la vente (optionnel)
     */
    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }

    /**
     * Scope pour récupérer les entrées de stock
     */
    public function scopeEntrees($query)
    {
        return $query->where('type', 'entree');
    }

    /**
     * Scope pour récupérer les sorties de stock
     */
    public function scopeSorties($query)
    {
        return $query->where('type', 'sortie');
    }

    /**
     * Scope pour récupérer les mouvements d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_mouvement', today());
    }

    /**
     * Créer un mouvement de stock et mettre à jour le produit
     */
    public static function creerMouvement(
        int $produitId,
        int $userId,
        string $type,
        int $quantite,
        ?string $motif = null,
        ?string $notes = null,
        ?int $venteId = null
    ): self {
        $produit = Produit::findOrFail($produitId);
        $stockAvant = $produit->stock_actuel;

        // Calculer le nouveau stock
        $stockApres = match ($type) {
            'entree', 'retour' => $stockAvant + $quantite,
            'sortie' => $stockAvant - $quantite,
            'ajustement' => $quantite, // Ajustement direct
            default => $stockAvant,
        };

        // Créer le mouvement
        $mouvement = static::create([
            'produit_id' => $produitId,
            'user_id' => $userId,
            'vente_id' => $venteId,
            'type' => $type,
            'quantite' => $quantite,
            'stock_avant' => $stockAvant,
            'stock_apres' => $stockApres,
            'motif' => $motif,
            'notes' => $notes,
            'date_mouvement' => now(),
        ]);

        // Mettre à jour le stock du produit
        $produit->update(['stock_actuel' => $stockApres]);

        return $mouvement;
    }
}
