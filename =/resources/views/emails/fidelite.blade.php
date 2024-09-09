<!DOCTYPE html>
<html>
<head>
    <title>Carte de Fidélité</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 24px;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
        }
        .profile-photo {
            display: block;
            margin: 20px auto;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #2980b9;
        }
        .qr-code {
            display: block;
            margin: 20px auto;
            width: 150px;
            height: 150px;
        }
        .info {
            text-align: center;
            margin-top: 20px;
        }
        .info p {
            font-weight: bold;
            color: #34495e;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Carte de Fidélité</h1>
        <p style="text-align: center;">Merci d'avoir rejoint notre programme de fidélité.</p>
        <p style="text-align: center;">Votre carte de fidélité est fournie avec un code QR pour vous identifier facilement.</p>
        
        <!-- Photo de profil -->
        <img src="{{ asset('/' . $client->user->photo) }}" class="profile-photo" alt="Photo de profil">

        <!-- Informations du client -->
        <div class="info">
            <p>Nom: {{ $client->surnom }} {{ $client->user->prenom }}</p>
            <p>Téléphone: {{ $client->telephone_portable}}</p>
            <p>Email: {{ $client->user->login }}</p>
        </div>

        <!-- Code QR
        <p style="text-align: center;">Votre Code QR:</p>
        <img src="{{ $qrCodePath }}" class="qr-code" alt="QR Code"> -->
    </div>

    <div class="footer">
        <p>Si vous avez des questions, veuillez nous contacter à abdouazizdiop583@gmail.com</p>
        <p>Merci pour votre fidélité !</p>
    </div>
</body>
</html>
