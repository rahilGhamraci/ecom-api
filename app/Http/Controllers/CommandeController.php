<?php

namespace App\Http\Controllers;
use App\Models\Commande;
use App\Models\Client;
use App\Models\Produit;
use App\Models\ProduitCommande;
use Illuminate\Http\Request;
use Auth;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Commande::all(); // à rendre plus specifique 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'produit_ids'=> 'required|array',
            'quantites'=> 'required|array',
            'produit_ids.*'=> 'exists:produits,id', // Vérifie que tous les produits existent dans la table des produits
        ]);;
            $client=Client::where('user_id', Auth::user()->id)->first();
            $commande=Commande::create([
                'etat'=>'En attente',
                'client_id'=> $client->id,
                           
            ]);
        
            $data=[];
            $produit_ids=$fields["produit_ids"];
            $quantites=$fields["quantites"];
            if($commande){
                if (count($produit_ids) === count($quantites)) {
                
                    foreach (array_keys($produit_ids) as $index) {
                        // Accédez aux éléments des deux tableaux avec le même index
                        $produit_id = $produit_ids[$index];
                        $quantite = $quantites[$index];
                        $produit_commande=ProduitCommande::create([
                            'commande_id'=>$commande->id,
                            'produit_id'=> $produit_id,
                            'quantite' => $quantite,
                                       
                        ]);
                        if($produit_commande){
                            $produit=Produit::Find($produit_id);
                            $obj=[
                                'id'                    => $produit->id,
                                'nom'                   => $produit->nom,
                                'description'           => $produit->description,
                                'prix'                  => $produit->prix,
                                'marque'                => $produit->marque,
                                'quantite_commandee'    => $quantite,
                            ];           
                            array_push($data,$obj);
                        }
                    }
                } 
                
            

                return response()->json([
                    'data'=>$commande,
                    'produits'=>$data,
                ],200);

            }else{
                return response()->json([
                    "message"=>"la commande n'a pas pu etre crée"
                ],401);
    
            }
    }

    /**
     * aficher les details d'une commande
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $data=[];
        $commande=Commande::find($id);
        if($commande){
            $client=Client::find($commande->client_id);
            $produits_commandes=ProduitCommande::where('commande_id',$id)->get();
        
            foreach ( $produits_commandes as $produit_commande) {
              
                    $produit=Produit::Find($produit_commande->produit_id);
                    $obj=[
                        'id'                    => $produit->id,
                        'nom'                   => $produit->nom,
                        'description'           => $produit->description,
                        'prix'                  => $produit->prix,
                        'marque'                => $produit->marque,
                        'quantite_commandee'    => $produit_commande->quantite,
                    ];           
                    array_push($data,$obj);
                
               
            }


            return response()->json([
                'data'=>$commande,
                'client'=>$client,
                'produits'=>$data,
            ],200);

        }else{
            return response()->json([
                "message"=>"la commande n'a pas pu etre crée"
            ],401);

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $commande=Commande::find($id);
        if($commande){
            
            if($commande->update($request->all())){
                return response()->json([
                    'message'=>'categorie modifiée avec succès'
                ],200);
            }else{
                return response()->json([
                    "message"=>"la categorie n'a pas pu etre modifiée"
                ],401);
            }  
   
            
        }else{
            return response()->json([
                "message"=>"la categorie n'existe pas pour pouvoir la modifier"
            ],401); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $commande=Commande::find($id);
        if ($commande){
            // supprimer toutes les lignes de la table  produit_commandes qui ont comme cle etrangère l'id de notre commande
            $produits_commandes=ProduitCommande::where('commande_id',$id)->get();
            foreach ( $produits_commandes as $produit_commande) {

                ProduitCommande::where('produit_id', $produit_commande->produit_id)
                ->where('commande_id', $commande->id)
                ->delete();
            }
           
            if($commande->delete()){
                return response()->json([
                    "message"=>"commande supprimée"
                ],200); 
            }else{
                return response()->json([
                    "message"=>"commande n'a pas ete supprimée"
                ],401); 
            }
        }else{
            return response()->json([
                "message"=>"la commande  n'existe pas pour pouvoir la supprimer"
            ],401);
        }
    }
}
