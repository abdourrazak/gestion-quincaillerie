<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use HasFactory;

    protected $table = 'fournisseurs';

    protected $fillable = [
        'nom',
        'entreprise',
        'email',
        'telephone',
        'telephone_secondaire',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'conditions_paiement',
        'notes',
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
        return $this->hasMany(Produit::class, 'fournisseur_id');
    }

    /**
     * Relation avec les commandes
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(CommandeFournisseur::class, 'fournisseur_id');
    }

    /**
     * Scope pour récupérer uniquement les fournisseurs actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Nom complet du fournisseur
     */
    public function getNomCompletAttribute(): string
    {
        return $this->entreprise ? "{$this->nom} ({$this->entreprise})" : $this->nom;
    }
}
