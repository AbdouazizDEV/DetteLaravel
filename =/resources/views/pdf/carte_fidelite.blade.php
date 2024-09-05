<!DOCTYPE html>
<html>
<head>
    <title>Carte de Fidélité</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 0.5rem;
        }

        img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
        }

        .qr-code {
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Carte de Fidélité</h1>
        <p>Merci d'avoir rejoint notre programme de fidélité.</p>
        <p>Votre carte de fidélité vous est fournie avec un code QR pour vous identifier facilement.</p>
        <!-- Photo de profil-->
        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo de profil" class="w-48 h-48 mx-auto rounded-full">
        <p><strong>Nom:</strong> {{ $user->prenom }} {{ $user->nom }}</p>
        <p><strong>Téléphone:</strong> {{ $user->telephone }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>

        <div class="qr-code">
            <p>Code QR:</p>
            <img src="{{ $qrCodePath }}" alt="QR Code">
        </div>
    </div>
</body>
</html>
