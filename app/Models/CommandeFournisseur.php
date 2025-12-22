<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommandeFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'commandes_fournisseurs';

    protected $fillable = [
        'numero_commande',
        'fournisseur_id',
        'user_id',
        'statut',
        'montant_total',
        'notes',
        'date_commande',
        'date_livraison_prevue',
        'date_livraison_reelle',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'date_livraison_reelle' => 'date',
    ];

    /**
     * Relation avec le fournisseur
     */
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    /**
     * Relation avec l'utilisateur (créateur)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les items de commande
     */
    public function items(): HasMany
    {
        return $this->hasMany(CommandeFournisseurItem::class, 'commande_fournisseur_id');
    }

    /**
     * Scope pour récupérer les commandes en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->whereIn('statut', ['brouillon', 'envoyee', 'confirmee']);
    }

    /**
     * Scope pour récupérer les commandes reçues
     */
    public function scopeRecues($query)
    {
        return $query->where('statut', 'recue');
    }

    /**
     * Générer un numéro de commande unique
     */
    public static function genererNumeroCommande(): string
    {
        $prefix = 'CMD';
        $date = now()->format('Ymd');
        $derniere = static::whereDate('created_at', today())->count() + 1;
        $numero = str_pad($derniere, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$date}-{$numero}";
    }

    /**
     * Vérifier si la commande est complètement reçue
     */
    public function getEstCompleteAttribute(): bool
    {
        return $this->items->every(function ($item) {
            return $item->quantite_recue >= $item->quantite_commandee;
        });
    }

    /**
     * Pourcentage de réception
     */
    public function getPourcentageReceptionAttribute(): float
    {
        $totalCommandee = $this->items->sum('quantite_commandee');
        $totalRecue = $this->items->sum('quantite_recue');
        
        if ($totalCommandee == 0) {
            return 0;
        }
        
        return ($totalRecue / $totalCommandee) * 100;
    }
}
