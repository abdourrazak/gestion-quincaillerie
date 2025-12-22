<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produits';

    protected $fillable = [
        'nom',
        'reference',
        'code_barre',
        'categorie_id',
        'fournisseur_id',
        'description',
        'prix_achat',
        'prix_vente',
        'tva',
        'stock_actuel',
        'stock_minimum',
        'stock_maximum',
        'unite',
        'image_principale',
        'images_supplementaires',
        'actif',
        'en_promotion',
        'prix_promotion',
        'date_debut_promotion',
        'date_fin_promotion',
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'tva' => 'decimal:2',
        'prix_promotion' => 'decimal:2',
        'stock_actuel' => 'integer',
        'stock_minimum' => 'integer',
        'stock_maximum' => 'integer',
        'actif' => 'boolean',
        'en_promotion' => 'boolean',
        'images_supplementaires' => 'array',
        'date_debut_promotion' => 'date',
        'date_fin_promotion' => 'date',
    ];

    /**
     * Relation avec la catégorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    /**
     * Relation avec le fournisseur
     */
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    /**
     * Relation avec les mouvements de stock
     */
    public function mouvementsStock(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'produit_id');
    }

    /**
     * Relation avec les items de vente
     */
    public function venteItems(): HasMany
    {
        return $this->hasMany(VenteItem::class, 'produit_id');
    }

    /**
     * Scope pour récupérer uniquement les produits actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour récupérer les produits en stock faible
     */
    public function scopeStockFaible($query)
    {
        return $query->whereColumn('stock_actuel', '<=', 'stock_minimum');
    }

    /**
     * Scope pour récupérer les produits en rupture de stock
     */
    public function scopeRuptureStock($query)
    {
        return $query->where('stock_actuel', '<=', 0);
    }

    /**
     * Scope pour récupérer les produits en promotion
     */
    public function scopeEnPromotion($query)
    {
        return $query->where('en_promotion', true)
            ->where('date_debut_promotion', '<=', now())
            ->where('date_fin_promotion', '>=', now());
    }

    /**
     * Vérifier si le produit est en stock faible
     */
    public function getEstStockFaibleAttribute(): bool
    {
        return $this->stock_actuel <= $this->stock_minimum;
    }

    /**
     * Vérifier si le produit est en rupture de stock
     */
    public function getEstRuptureStockAttribute(): bool
    {
        return $this->stock_actuel <= 0;
    }

    /**
     * Prix de vente avec TVA
     */
    public function getPrixVenteTtcAttribute(): float
    {
        return $this->prix_vente * (1 + ($this->tva / 100));
    }

    /**
     * Prix effectif (promotion ou normal)
     */
    public function getPrixEffectifAttribute(): float
    {
        if ($this->en_promotion && 
            $this->prix_promotion && 
            now()->between($this->date_debut_promotion, $this->date_fin_promotion)) {
            return $this->prix_promotion;
        }
        return $this->prix_vente;
    }

    /**
     * Marge bénéficiaire
     */
    public function getMargeAttribute(): float
    {
        return $this->prix_vente - $this->prix_achat;
    }

    /**
     * Pourcentage de marge
     */
    public function getPourcentageMargeAttribute(): float
    {
        if ($this->prix_achat == 0) {
            return 0;
        }
        return (($this->prix_vente - $this->prix_achat) / $this->prix_achat) * 100;
    }
}
