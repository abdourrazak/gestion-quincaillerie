<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventes';

    protected $fillable = [
        'numero_facture',
        'client_id',
        'user_id',
        'sous_total',
        'montant_tva',
        'montant_total',
        'montant_remise',
        'mode_paiement',
        'statut_paiement',
        'montant_paye',
        'montant_rendu',
        'notes',
        'date_vente',
    ];

    protected $casts = [
        'sous_total' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'montant_remise' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_rendu' => 'decimal:2',
        'date_vente' => 'datetime',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Relation avec le vendeur (user)
     */
    public function vendeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les items de vente
     */
    public function items(): HasMany
    {
        return $this->hasMany(VenteItem::class, 'vente_id');
    }

    /**
     * Relation avec les mouvements de stock
     */
    public function mouvementsStock(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'vente_id');
    }

    /**
     * Scope pour récupérer les ventes d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_vente', today());
    }

    /**
     * Scope pour récupérer les ventes du mois en cours
     */
    public function scopeMoisEnCours($query)
    {
        return $query->whereMonth('date_vente', now()->month)
            ->whereYear('date_vente', now()->year);
    }

    /**
     * Scope pour récupérer les ventes par période
     */
    public function scopePeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_vente', [$dateDebut, $dateFin]);
    }

    /**
     * Générer un numéro de facture unique
     */
    public static function genererNumeroFacture(): string
    {
        $prefix = 'FAC';
        $date = now()->format('Ymd');
        $derniere = static::whereDate('created_at', today())->count() + 1;
        $numero = str_pad($derniere, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$date}-{$numero}";
    }

    /**
     * Nombre d'articles dans la vente
     */
    public function getNombreArticlesAttribute(): int
    {
        return $this->items()->sum('quantite');
    }
}
