<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'nom',
        'prenom',
        'entreprise',
        'email',
        'telephone',
        'telephone_secondaire',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'notes',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec les ventes
     */
    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class, 'client_id');
    }

    /**
     * Scope pour récupérer uniquement les clients actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Nom complet du client
     */
    public function getNomCompletAttribute(): string
    {
        $nom = $this->prenom ? "{$this->prenom} {$this->nom}" : $this->nom;
        return $this->entreprise ? "{$nom} ({$this->entreprise})" : $nom;
    }

    /**
     * Total des achats du client
     */
    public function getTotalAchatsAttribute(): float
    {
        return $this->ventes()->sum('montant_total');
    }

    /**
     * Nombre d'achats du client
     */
    public function getNombreAchatsAttribute(): int
    {
        return $this->ventes()->count();
    }
}
