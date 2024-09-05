<!DOCTYPE html>
<html>
<head>
    <title>Carte de Fidélité</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>
    <h1>Carte de Fidélité</h1>
    <!-- Lien vers l'image de QR-->
    <img src="{{ $qrCodePath }}" alt="QR Code">
    <p>Merci d'avoir rejoint notre programme de fidélité.</p>
    <p>Votre carte de fidélité vous est fournie avec un code QR pour vous identifier facilement.</p>
    <!-- Photo de profil-->
    <img src="{{ asset('storage/' . $client->user->photo) }}" width="300" height="300" alt="Photo de profil">
    <p>Nom: {{ $client->surnom }} {{ $client->user->prenom }}</p>
    <p>Téléphone: {{ $client->user->telephone }}</p>
    <p>Email: {{ $client->user->email }}</p>

    <p>Code QR:</p>
    <img src="{{ $qrCodePath }}" alt="QR Code">
</body>
</html>
