<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Liste tous les fournisseurs
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Fournisseur::query();

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par statut actif
        if ($request->has('actif')) {
            if ($request->boolean('actif')) {
                $query->actif();
            } else {
                $query->where('actif', false);
            }
        } else {
            $query->actif();
        }

        // Avec le nombre de produits
        $query->withCount('produits');

        // Tri
        $sortBy = $request->get('sort_by', 'entreprise');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $fournisseurs = $query->get();

        return response()->json([
            'success' => true,
            'data' => $fournisseurs,
            'total' => $fournisseurs->count(),
        ]);
    }

    /**
     * Créer un nouveau fournisseur
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'entreprise' => 'required|string|max:255',
            'email' => 'required|email|unique:fournisseurs,email',
            'telephone' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'pays' => 'nullable|string|max:255',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['pays'] = $validated['pays'] ?? 'France';
        $validated['actif'] = true;

        $fournisseur = Fournisseur::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fournisseur créé avec succès',
            'data' => $fournisseur,
        ], 201);
    }

    /**
     * Afficher un fournisseur spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $fournisseur = Fournisseur::with(['produits' => function($query) {
            $query->actif()->orderBy('nom');
        }])
        ->withCount('produits')
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $fournisseur,
        ]);
    }

    /**
     * Mettre à jour un fournisseur
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'entreprise' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:fournisseurs,email,' . $id,
            'telephone' => 'sometimes|required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'sometimes|required|string',
            'ville' => 'sometimes|required|string|max:255',
            'code_postal' => 'sometimes|required|string|max:10',
            'pays' => 'nullable|string|max:255',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
            'actif' => 'sometimes|boolean',
        ]);

        $fournisseur->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fournisseur mis à jour avec succès',
            'data' => $fournisseur->fresh(),
        ]);
    }

    /**
     * Supprimer un fournisseur
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        // Vérifier si le fournisseur a des produits
        if ($fournisseur->produits()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce fournisseur car il a des produits associés',
            ], 422);
        }

        $fournisseur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fournisseur supprimé avec succès',
        ]);
    }

    /**
     * Activer/Désactiver un fournisseur
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->actif = !$fournisseur->actif;
        $fournisseur->save();

        return response()->json([
            'success' => true,
            'message' => $fournisseur->actif ? 'Fournisseur activé' : 'Fournisseur désactivé',
            'data' => $fournisseur,
        ]);
    }

    /**
     * Obtenir les produits d'un fournisseur
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function products($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $produits = $fournisseur->produits()
            ->actif()
            ->with('categorie')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'fournisseur' => [
                'id' => $fournisseur->id,
                'entreprise' => $fournisseur->entreprise,
            ],
            'data' => $produits,
            'total' => $produits->count(),
        ]);
    }
}
