<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Liste tous les clients
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Filtre par type (professionnel/particulier)
        if ($request->has('type')) {
            if ($request->type === 'professionnel') {
                $query->whereNotNull('entreprise');
            } elseif ($request->type === 'particulier') {
                $query->whereNull('entreprise');
            }
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

        // Avec le nombre de ventes
        $query->withCount('ventes');

        // Tri
        $sortBy = $request->get('sort_by', 'nom');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $clients = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $clients->items(),
            'pagination' => [
                'total' => $clients->total(),
                'per_page' => $clients->perPage(),
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'from' => $clients->firstItem(),
                'to' => $clients->lastItem(),
            ],
        ]);
    }

    /**
     * Créer un nouveau client
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'entreprise' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'pays' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['pays'] = $validated['pays'] ?? 'France';
        $validated['actif'] = true;

        $client = Client::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Client créé avec succès',
            'data' => $client,
        ], 201);
    }

    /**
     * Afficher un client spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $client = Client::with(['ventes' => function($query) {
            $query->orderBy('date_vente', 'desc')->take(10);
        }])
        ->withCount('ventes')
        ->findOrFail($id);

        // Calculer le total des achats
        $totalAchats = $client->ventes()->sum('montant_total');

        return response()->json([
            'success' => true,
            'data' => array_merge($client->toArray(), [
                'total_achats' => $totalAchats,
            ]),
        ]);
    }

    /**
     * Mettre à jour un client
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'entreprise' => 'nullable|string|max:255',
            'email' => 'sometimes|required|email|unique:clients,email,' . $id,
            'telephone' => 'sometimes|required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'sometimes|required|string',
            'ville' => 'sometimes|required|string|max:255',
            'code_postal' => 'sometimes|required|string|max:10',
            'pays' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'actif' => 'sometimes|boolean',
        ]);

        $client->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Client mis à jour avec succès',
            'data' => $client->fresh(),
        ]);
    }

    /**
     * Supprimer un client
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // Vérifier si le client a des ventes
        if ($client->ventes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce client car il a des ventes associées',
            ], 422);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client supprimé avec succès',
        ]);
    }

    /**
     * Activer/Désactiver un client
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($id)
    {
        $client = Client::findOrFail($id);
        $client->actif = !$client->actif;
        $client->save();

        return response()->json([
            'success' => true,
            'message' => $client->actif ? 'Client activé' : 'Client désactivé',
            'data' => $client,
        ]);
    }

    /**
     * Obtenir l'historique des achats d'un client
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchases($id)
    {
        $client = Client::findOrFail($id);
        
        $ventes = $client->ventes()
            ->with('items.produit')
            ->orderBy('date_vente', 'desc')
            ->get();

        $totalAchats = $ventes->sum('montant_total');
        $nombreAchats = $ventes->count();

        return response()->json([
            'success' => true,
            'client' => [
                'id' => $client->id,
                'nom_complet' => $client->nom_complet,
            ],
            'statistiques' => [
                'total_achats' => $totalAchats,
                'nombre_achats' => $nombreAchats,
                'panier_moyen' => $nombreAchats > 0 ? $totalAchats / $nombreAchats : 0,
            ],
            'data' => $ventes,
        ]);
    }
}
