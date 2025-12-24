<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Liste tous les produits avec filtres et recherche
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Produit::with(['categorie', 'fournisseur']);

        // Filtre par catégorie
        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        // Filtre par fournisseur
        if ($request->has('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        // Recherche par nom ou référence
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('code_barre', 'like', "%{$search}%");
            });
        }

        // Filtre par stock faible
        if ($request->boolean('stock_faible')) {
            $query->stockFaible();
        }

        // Filtre par rupture de stock
        if ($request->boolean('rupture_stock')) {
            $query->ruptureStock();
        }

        // Filtre par promotion
        if ($request->boolean('en_promotion')) {
            $query->enPromotion();
        }

        // Filtre par statut actif
        if ($request->has('actif')) {
            if ($request->boolean('actif')) {
                $query->actif();
            } else {
                $query->where('actif', false);
            }
        } else {
            // Par défaut, seulement les produits actifs
            $query->actif();
        }

        // Tri
        $sortBy = $request->get('sort_by', 'nom');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $produits = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $produits->items(),
            'pagination' => [
                'total' => $produits->total(),
                'per_page' => $produits->perPage(),
                'current_page' => $produits->currentPage(),
                'last_page' => $produits->lastPage(),
                'from' => $produits->firstItem(),
                'to' => $produits->lastItem(),
            ],
        ]);
    }

    /**
     * Créer un nouveau produit
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:produits,reference',
            'code_barre' => 'nullable|string|max:255|unique:produits,code_barre',
            'categorie_id' => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'description' => 'nullable|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0|max:100',
            'stock_actuel' => 'nullable|integer|min:0',
            'stock_minimum' => 'nullable|integer|min:0',
            'stock_maximum' => 'nullable|integer|min:0',
            'unite' => 'required|in:piece,sac,m2,litre,kg,metre,rouleau,boite',
            'image_principale' => 'nullable|string',
            'images_supplementaires' => 'nullable|array',
        ]);

        // Valeurs par défaut
        $validated['tva'] = $validated['tva'] ?? 20.00;
        $validated['stock_actuel'] = $validated['stock_actuel'] ?? 0;
        $validated['stock_minimum'] = $validated['stock_minimum'] ?? 10;
        $validated['actif'] = true;
        $validated['en_promotion'] = false;

        $produit = Produit::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produit créé avec succès',
            'data' => $produit->load(['categorie', 'fournisseur']),
        ], 201);
    }

    /**
     * Afficher un produit spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $produit = Produit::with(['categorie', 'fournisseur'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $produit,
        ]);
    }

    /**
     * Rechercher un produit par code-barre
     * 
     * @param string $codeBarre
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByBarcode($codeBarre)
    {
        $produit = Produit::with(['categorie', 'fournisseur'])
            ->where('code_barre', $codeBarre)
            ->actif()
            ->first();

        if (!$produit) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $produit,
        ]);
    }

    /**
     * Mettre à jour un produit
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'reference' => 'sometimes|required|string|max:255|unique:produits,reference,' . $id,
            'code_barre' => 'nullable|string|max:255|unique:produits,code_barre,' . $id,
            'categorie_id' => 'sometimes|required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'description' => 'nullable|string',
            'prix_achat' => 'sometimes|required|numeric|min:0',
            'prix_vente' => 'sometimes|required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0|max:100',
            'stock_actuel' => 'nullable|integer|min:0',
            'stock_minimum' => 'nullable|integer|min:0',
            'stock_maximum' => 'nullable|integer|min:0',
            'unite' => 'sometimes|required|in:piece,sac,m2,litre,kg,metre,rouleau,boite',
            'image_principale' => 'nullable|string',
            'images_supplementaires' => 'nullable|array',
            'actif' => 'sometimes|boolean',
            'en_promotion' => 'sometimes|boolean',
            'prix_promotion' => 'nullable|numeric|min:0',
            'date_debut_promotion' => 'nullable|date',
            'date_fin_promotion' => 'nullable|date|after:date_debut_promotion',
        ]);

        $produit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produit mis à jour avec succès',
            'data' => $produit->fresh(['categorie', 'fournisseur']),
        ]);
    }

    /**
     * Supprimer un produit (soft delete)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $produit = Produit::findOrFail($id);
        $produit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé avec succès',
        ]);
    }

    /**
     * Activer/Désactiver un produit
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $produit = Produit::findOrFail($id);
        $produit->actif = !$produit->actif;
        $produit->save();

        return response()->json([
            'success' => true,
            'message' => $produit->actif ? 'Produit activé' : 'Produit désactivé',
            'data' => $produit,
        ]);
    }

    /**
     * Mettre à jour le stock d'un produit
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStock(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'quantite' => 'required|integer',
            'type' => 'required|in:ajout,retrait,ajustement',
            'motif' => 'nullable|string',
        ]);

        $ancienStock = $produit->stock_actuel;

        switch ($validated['type']) {
            case 'ajout':
                $produit->stock_actuel += $validated['quantite'];
                break;
            case 'retrait':
                $produit->stock_actuel -= $validated['quantite'];
                break;
            case 'ajustement':
                $produit->stock_actuel = $validated['quantite'];
                break;
        }

        // Empêcher le stock négatif
        if ($produit->stock_actuel < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Le stock ne peut pas être négatif',
            ], 422);
        }

        $produit->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock mis à jour avec succès',
            'data' => [
                'produit' => $produit,
                'ancien_stock' => $ancienStock,
                'nouveau_stock' => $produit->stock_actuel,
                'difference' => $produit->stock_actuel - $ancienStock,
            ],
        ]);
    }

    /**
     * Mettre un produit en promotion
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPromotion(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'prix_promotion' => 'required|numeric|min:0|lt:prix_vente',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $produit->update([
            'en_promotion' => true,
            'prix_promotion' => $validated['prix_promotion'],
            'date_debut_promotion' => $validated['date_debut'],
            'date_fin_promotion' => $validated['date_fin'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promotion activée avec succès',
            'data' => $produit,
        ]);
    }

    /**
     * Retirer la promotion d'un produit
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePromotion($id)
    {
        $produit = Produit::findOrFail($id);

        $produit->update([
            'en_promotion' => false,
            'prix_promotion' => null,
            'date_debut_promotion' => null,
            'date_fin_promotion' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promotion retirée avec succès',
            'data' => $produit,
        ]);
    }
}
