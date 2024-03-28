<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\FactureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// public routes
Route::post('/login',[AuthController::class,'login']);

// clients routes

Route::post('/clients',[ClientController::class,'store']); // inscription d'un client 


// Categories routes
Route::get('/categories',[CategorieController::class,'index']);


// Produits routes
Route::get('/produits/{id}',[ProduitController::class,'index']); // id de la categorie
Route::get('/produit/img/{id}',[ProduitController::class,'display_img']); // afficher l'image d'un produit
Route::get('/produit/{id}',[ProduitController::class,'show']); // id du produit ,afficher les informations d'un produit






// routes that requires authentification
Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::post('/categories',[CategorieController::class,'store']);
    Route::post('/categories/{id}',[CategorieController::class,'update']);
    Route::delete('/categories/{id}',[CategorieController::class,'destroy']);

    Route::post('/produits',[ProduitController::class,'store']);
    Route::post('/produits/{id}',[ProduitController::class,'update']);
    Route::delete('/produits/{id}',[ProduitController::class,'destroy']);

    Route::get('/clients',[ClientController::class,'index']); // lister les clients aux administrateurs
    Route::post('/clients/{id}',[ClientController::class,'update']); // modification d'un client ses propres infos
    Route::delete('/clients/{id}',[ClientController::class,'destroy']); // suppression d'un client par admin
   

    Route::get('/commandes',[CommandeController::class,'index']);
    Route::post('/commandes',[CommandeController::class,'store']);
    Route::get('/commandes/{id}',[CommandeController::class,'show']);
    Route::post('/commandes/{id}',[CommandeController::class,'update']);
    Route::delete('/commandes/{id}',[CommandeController::class,'destroy']);
    
    Route::get('/factures',[FactureController::class,'index']);
    Route::post('/factures',[FactureController::class,'store']);
    Route::post('/factures/{id}',[FactureController::class,'update']);
    Route::delete('/factures/{id}',[FactureController::class,'destroy']);

    Route::post('/logout',[AuthController::class,'logout']);

});
