<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'actif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'actif' => 'boolean',
        ];
    }

    /**
     * Relation avec les ventes (en tant que vendeur)
     */
    public function ventes()
    {
        return $this->hasMany(Vente::class, 'user_id');
    }

    /**
     * Relation avec les mouvements de stock
     */
    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class, 'user_id');
    }

    /**
     * Relation avec les commandes fournisseurs
     */
    public function commandesFournisseurs()
    {
        return $this->hasMany(CommandeFournisseur::class, 'user_id');
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifier si l'utilisateur est gérant
     */
    public function estGerant(): bool
    {
        return $this->role === 'gerant';
    }

    /**
     * Vérifier si l'utilisateur est vendeur
     */
    public function estVendeur(): bool
    {
        return $this->role === 'vendeur';
    }

    /**
     * Vérifier si l'utilisateur est magasinier
     */
    public function estMagasinier(): bool
    {
        return $this->role === 'magasinier';
    }

}
