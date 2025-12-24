<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Liste toutes les catégories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Categorie::actif()
            ->withCount('produits')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'total' => $categories->count(),
        ]);
    }

    /**
     * Créer une nouvelle catégorie
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:50',
            'couleur' => 'nullable|string|max:7', // Format: #RRGGBB
        ]);

        $validated['slug'] = Str::slug($validated['nom']);
        $validated['actif'] = true;

        $categorie = Categorie::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie créée avec succès',
            'data' => $categorie,
        ], 201);
    }

    /**
     * Afficher une catégorie spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $categorie = Categorie::with(['produits' => function($query) {
            $query->actif()->orderBy('nom');
        }])
        ->withCount('produits')
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $categorie,
        ]);
    }

    /**
     * Mettre à jour une catégorie
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255|unique:categories,nom,' . $id,
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:50',
            'couleur' => 'nullable|string|max:7',
            'actif' => 'sometimes|boolean',
        ]);

        if (isset($validated['nom'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $categorie->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $categorie->fresh(),
        ]);
    }

    /**
     * Supprimer une catégorie
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);

        // Vérifier si la catégorie a des produits
        if ($categorie->produits()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cette catégorie car elle contient des produits',
            ], 422);
        }

        $categorie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès',
        ]);
    }

    /**
     * Désactiver une catégorie (soft disable)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->actif = !$categorie->actif;
        $categorie->save();

        return response()->json([
            'success' => true,
            'message' => $categorie->actif ? 'Catégorie activée' : 'Catégorie désactivée',
            'data' => $categorie,
        ]);
    }
}
