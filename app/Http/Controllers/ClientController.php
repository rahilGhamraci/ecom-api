<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=[];
        
        $clients=Client::all();
        for($i = 0; $i <  count($clients); $i++){
           
            $compte=User::where('id', $clients[$i]->user_id)->first();
           
         
    
             $obj=[
                 'id'                    => $clients[$i]->id,
                 'nom'                   => $clients[$i]->nom,
                 'prenom'                => $clients[$i]->prenom,
                 'tel'                   => $clients[$i]->tel,
                 'adresse'               => $clients[$i]->adresse,
                 'user_id'               => $clients[$i]->user_id,
                 'name'                  => $compte->name,
                 'email'                 => $compte->email,
               
                 
             ];
           
           
             array_push($data,$obj);
         }
         return response()->json(
            $data
         ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // l'inscription d un client avec son compte se fait en meme temps 
    public function store(Request $request)
    {
        $fields= $request->validate([
            'nom'=> 'required|string',
            'prenom'=> 'required|string',
            'tel'=> 'required|string',
            'adresse'=> 'required|string',
            'user_name'=> 'required|string',
            'email'=>  'required|email|unique:users,email',
            'password'=> 'required|string|confirmed',
          
        ]);

        // on procède d'abord à la creation d'un compte utilisateur par la suite on associer ce compte au client
        $hashedPassword= hash('sha256', $fields['password']);
        $user=User::create([
            'name'=>$fields['user_name'],
            'email'=>$fields['email'],
            'password'=>  $hashedPassword,
            'role' => 'client',
        ]);
       
        if($user){
            $client=Client::create([
                'nom'=>$fields['nom'],
                'prenom'=>$fields['prenom'],
                'tel'=>$fields['tel'],
                'adresse'=>$fields['adresse'],
                'user_id'=>  $user->id,
            ]);
            if($client){
                $obj=[
                    'nom'=>$client-> nom,
                    'prenom'=>$client->prenom,
                    'tel'=>$client->tel,
                    'adresse'=>$client->adresse,
                    'user_id'=>  $user->id,
                    "user_name"=> $user->name,
                    "email"=> $user->email,
                
                ];
                return response()->json(
                    $obj
               ,200);
            }else{
                return response()->json([
                    "message"=>"le client n'a pas ete enregistré"
                ],401);
    
            }
            
        }else{
            return response([
                "message"=>"utilisateur non enregistré"
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
        $client=Client::find($id); 
     
    if($client){
        $user=User::where('id',$client->user_id)->first();

        // mettre à jour les informations du compte ( sans le mot de passe , car se dernier est modifier dans une autre fonction)

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        
        if ($request->filled('email')) {
            $user->email= $request->prenom;
        }

        $user->save();

        // mettre à jour les informations du client 
        if ($request->filled('nom')) {
            $client->nom = $request->nom;
        } 
        
        if ($request->filled('prenom')) {
            $client->prenom = $request->prenom;
        } 
        
        if ($request->filled('tel')) {
            $client->tel = $request->tel;
        }
        if ($request->filled('adresse')) {
            $client->adresse = $request->adresse;
        }
        
        $client->save();
        $obj=[
            'nom'=>$client-> nom,
            'prenom'=>$client->prenom,
            'tel'=>$client->tel,
            'adresse'=>$client->adresse,
            'user_id'=>  $user->id,
            "name"=> $user->name,
            "email"=> $user->email,
        
        ];
        return response()->json(
            $obj
       ,200);
    }else{
        return response()->json([
            "message"=>"le client n'existe pas pour pouvoir le modifier"
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
        $client=Client::find($id); 
     
       
        if ( $client){
            $user=User::where('id',$client->user_id)->first();
           
            if($user->delete()){
                if($client->delete()){
                    return response()->json([
                        "message"=>"le client a bien été supprimé"
                      ],401);
           
                }else{
                    return response()->json([
                       "message"=>"le client n'a pas pu etre supprimé"
                     ],401); 
                }
           
            }else{
                return response()->json([
                   "message"=>"le compte du client n'a pas pu etre supprimé"
                 ],401); 
            }
        }else{
            return response()->json([
                "message"=>"le client n'existe pas pour pouvoir le supprimer"
            ],401);
        }
    }
}
