<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant_total',
        'numero_carte',
        'mode_paiement', 
        'commande_id',
        'file_path',
    ];
}
