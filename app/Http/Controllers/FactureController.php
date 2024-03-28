<?php

namespace App\Http\Controllers;
use App\Models\Commande;
use App\Models\Produit;
use App\Models\Facture;
use App\Models\ProduitCommande;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;


class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Facture::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields= $request->validate([
            'numero_carte'=> 'required|string',
            'mode_paiement'=> 'required|string',
            'commande_id' =>'required'
           
        ]);
            

        // calcule du montat globale 
            $produits=[];
            $commande=Commande::find($fields['commande_id']);  // recuperartion de la commande 
            $produits_commandes=ProduitCommande::where('commande_id',$commande->id)->get(); //  recuperation des produits de la commande 
            $montant_total=0;
            
            foreach ( $produits_commandes as $produit_commande) {
                $produit=Produit::find($produit_commande->produit_id);
                array_push($produits,$produit);
                $montant_total = $montant_total + $produit->prix * $produit_commande->quantite;
            }
         
            // enregistrement des information de la facture dans la bdd
            $facture=Facture::create([
                'numero_carte'=>$fields['numero_carte'],
                'mode_paiement'=>$fields['mode_paiement'],
                'commande_id'=>$fields['commande_id'],
                'montant_total' => $montant_total,
                           
            ]);
        
       
       
            if($facture){
                 $client=Client::find($commande->client_id);
                 $user=User::find($client->user_id);
                
                 // etablissemnt du  pdf
                $data=[
                    'client' => $client,
                    'user' => $user,
                    'produits' => $produits,
                    'facture' => $facture,
                    'date' => $facture->created_at->format('d/m/Y'),
                ];
                $pdf = Pdf::loadView('facture', $data);

                $pdfContent = $pdf->output();

                // Générer un nom de fichier unique pour le PDF
                $fileName = 'facture_' . uniqid() . '.pdf';

                // Stocker le contenu du PDF dans le stockage de Laravel
                Storage::put('public/pdf/' . $fileName, $pdfContent);
                // mettre à jour le file path dans la bdd
                $facture->file_path = 'public/pdf/' . $fileName;
                $facture->save();
                // Envoi de l'e-mail avec le PDF en pièce jointe au client
                Mail::send([], [], function ($message) use ($pdfContent, $fileName,$user) {
                    $message->to($user->email)
                    ->subject('Votre facture')
                    ->attachData($pdfContent, $fileName, [
                        'mime' => 'application/pdf',
            ]);
});             
               // affichage du pdf à l'administrateur
                return $pdf->stream();
                 
               
            }else{
                return response()->json([
                    "message"=>"la facture n'a pas ete crée"
                ],401);
    
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $facture=Facture::find($id);
        if($facture){
            
            if($facture->update($request->all())){
                return response()->json([
                    'message'=>'facture modifiée avec succès'
                ],200);
            }else{
                return response()->json([
                    "message"=>"la facture n'a pas pu etre modifiée"
                ],401);
            }  
   
            
        }else{
            return response()->json([
                "message"=>"la facture n'existe pas pour pouvoir la modifier"
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
        $facture=Facture::find($id);
        if ($facture){
            $old_file=$facture->file_path;
            if(Storage::exists($old_file)){
                Storage::delete($old_file);
            }
           
            if($facture->delete()){
                return response()->json([
                    "message"=>"facture supprimée"
                ],200); 
            }else{
                return response()->json([
                    "message"=>"facture n'a pas ete supprimée"
                ],401); 
            }
        }else{
            return response()->json([
                "message"=>"la facture  n'existe pas pour pouvoir la supprimer"
            ],401);
        }
    }
}
