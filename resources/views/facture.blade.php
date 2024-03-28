<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Facture</title>
</head>
<body>
    <div class="container">
        <h2>Facture {{$facture->id }}</h2>
        <h3>Date: {{ $date }}</h3>
        
        <hr> <!-- Ligne horizontale pour séparer les sections -->
        
        <h3>Informations du Client</h3>
        <div>Nom: {{ $client->nom }}</div>
        <div>Prénom: {{ $client->prenom }}</div>
        <div>Tel: {{ $client->tel }}</div>
        <div>Adresse: {{ $client->adresse }}</div>
        
        <hr> <!-- Ligne horizontale pour séparer les sections -->
        
        <h3>Produits</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nom Produit</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $produit)
                <tr>
                    <td>{{ $produit->id }}</td>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->prix }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <hr> <!-- Ligne horizontale pour séparer les sections -->
        
        <div>Montant total : {{ $facture->montant_total }}</div>
    </div>
</body>
</html>
