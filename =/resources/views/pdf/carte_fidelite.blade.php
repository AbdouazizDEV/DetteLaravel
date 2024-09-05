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
    <p>Nom: {{ $user->prenom }} {{ $user->nom }}</p>
    <p>Telephone: {{ $user->telephone }}</p>
    <p>Email: {{ $user->email }}</p>
    <img src="{{ $qrCodePath }}" alt="QR Code">
</body>
</html>
