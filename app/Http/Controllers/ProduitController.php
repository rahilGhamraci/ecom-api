<?php

namespace App\Http\Controllers;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    /**
     * retourner les produits d'une categorie donnée.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        
        $produits=Produit::where('categorie_id',$id)->get();
        return response()->json(
            $produits
            ,200);
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
            'nom'=> 'required|string',
            'description'=> 'required|string',
            'img_path'=> 'string',
            'prix'=> 'required',
            'marque'=> 'required',
            'categorie_id'=> 'required',
            'quantite'=> 'required',
        ]);
      
        if($request->file){ 
            $file = $request->file('file');
            $original_name = $file->getClientOriginalName();
            $fichier = $file->storeAs('public/imagesProduits', $original_name);
            $new_file_path = 'public/imagesProduits/' . uniqid() . '_' . $original_name;
            Storage::copy($fichier, $new_file_path);
            
            $produit=Produit::create([
                'nom'=>$fields['nom'],
                'description'=>$fields['description'],
                'categorie_id'=>$request->categorie_id,
                'prix'=> $request->prix,
                'img_path'=> $new_file_path,
                'marque' => $fields['marque'],
                'quantite' => $fields['quantite']
              
                           
            ]);
        }else{
           
            $produit=Produit::create([
                'nom'=>$fields['nom'],
                'description'=>$fields['description'],
                'categorie_id'=>$request->categorie_id,
                'prix'=> $request->prix,  
                'marque' => $fields['marque'],
                'quantite' => $fields['quantite'] 
            ]);

        }
       
       
        if($produit){
           
            return response()->json([
                $produit
            ],200);
        }else{
            return response()->json([
                "message"=>"le produit n a pas ete crée"
            ],401);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($nom)
    {
        return Produit::where('nom','like','%'.$nom.'%')->get();
    }
    

    // afficher l'image d'un produit
    public function display_img($id)
    {
       
        
        $produit=Produit::Find($id);
        if($produit){
        $img_path=$produit->img_path;
        if(Storage::exists($img_path)){
           
            return Storage::download($img_path);
            
        }else{
            return response()->json([
                "message"=>"le produit ne posède pas d'image"
            ],401);
        }}else{
            return response()->json([
                "message"=>"le produit n'existe pas"
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
        $produit=Produit::Find($id);
        if($produit){
        
        if($request->file){ 
           
           $old_img=$produit->img_path;
            if(Storage::exists($old_img)){
                Storage::delete($old_img);
              }
        $file = $request->file('file');
        $original_name = $file->getClientOriginalName();
        $fichier = $file->storeAs('public/imagesProduits', $original_name);
        $new_file_path = 'public/imagesProduits/' . uniqid() . '_' . $original_name;
        Storage::copy($fichier, $new_file_path);
            $produit->update([
                'img_path'=> $new_file_path ,
                
            ]);
            
            $produit->save();
        }
        if($produit->update($request->all())){
           
            return response()->json([
                $produit
            ],200);
        }else{
            return response()->json([
                "message"=>"le produit n'a pas ete modifié"
            ],401);
        }  
    }else{
        return response()->json([
            "message"=>"le produit n'existe pas pour pouvoir le modifier"
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
        $produit=Produit::Find($id);
        if ( $produit){
            $old_img=$produit->img_path;
            if(Storage::exists($old_img)){
                Storage::delete($old_img);
            }
           
            if($produit->delete()){
            return response()->json([
                "message"=>"produit supprimé"
            ],200); 
        }else{
            return response()->json([
                "message"=>"le produit n'a pas pu etre supprimé"
            ],401); 
        }
        }else{
            return response()->json([
                "message"=>"le produit n'existe pas pour pouvoir le supprimer"
            ],401);
        }
    }
}
