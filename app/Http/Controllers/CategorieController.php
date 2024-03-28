<?php

namespace App\Http\Controllers;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  Categorie::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $fields= $request->validate([
            'nom'=> 'required|string',
           
        ]);
        
            $categorie=Categorie::create([
                'nom'=>$fields['nom'],
                           
            ]);
        
       
       
            if($categorie){
                return response()->json([
                    'data'=>$categorie
                ],200);
            }else{
                return response()->json([
                    "message"=>"la categorie n'a pas ete crée"
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
        $categorie=Categorie::find($id);
        if($categorie){
            
            if($categorie->update($request->all())){
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
        $categorie=Categorie::find($id);
        if ($categorie){
           
            if($categorie->delete()){
                return response()->json([
                    "message"=>"categorie supprimée"
                ],200); 
            }else{
                return response()->json([
                    "message"=>"categorie n'a pas ete supprimée"
                ],401); 
            }
        }else{
            return response()->json([
                "message"=>"la categorie  n'existe pas pour pouvoir la supprimer"
            ],401);
        }
    }
}
