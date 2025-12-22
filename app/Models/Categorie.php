<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'icone',
        'couleur',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec les produits
     */
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    /**
     * Scope pour récupérer uniquement les catégories actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Nombre de produits dans cette catégorie
     */
    public function getNombreProduitsAttribute(): int
    {
        return $this->produits()->count();
    }
}
