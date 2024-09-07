<!DOCTYPE html>
<html>
<head>
    <title>Carte de Fidélité</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 350px;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden; /* Ajouté pour éviter les débordements */
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #9c27b0; /* Purple color */
            margin-bottom: 1rem;
        }

        p {
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .avatar-circle {
            position: relative;
            margin-bottom: 1rem;
        }

        .avatar-circle::before {
            content: '';
            width: 120px;
            height: 120px;
            background: #e0e0e0; /* Light gray circle background */
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .qr-code {
            margin-top: 1rem;
            text-align: center;
        }

        .qr-code img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
        }

        .qr-code p {
            margin-top: 1rem;
            font-weight: 700;
            color: #333;
        }

        /* Decorative elements */
        .decorative-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }

        .decorative-element-1 {
            width: 80px;
            height: 80px;
            top: -20px;
            left: -20px;
        }

        .decorative-element-2 {
            width: 50px;
            height: 50px;
            bottom: -20px;
            right: -20px;
        }

        .decorative-element-3 {
            width: 40px;
            height: 40px;
            bottom: 10px;
            left: 10px;
        }
        .map-container {
          display: flex;
          align-items: center;
          justify-content: center;
          height: 100vh;
        }
        
        .map-container iframe {
          width: 100%;
          height: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="avatar-circle">
            <img src="{{ asset("storage/{$user->photo}") }}" alt="Photo de profil" class="profile-photo">
        </div>
        <h1>Carte de fidélité</h1>
        <p>{{ $user->prenom }} {{ $user->nom }}</p>
        
        <div class="qr-code">
            <img src="{{ $qrCodePath }}" alt="QR Code">
        </div>

        <!-- Decorative Elements -->
        <div class="decorative-element decorative-element-1"></div>
        <div class="decorative-element decorative-element-2"></div>
        <div class="decorative-element decorative-element-3"></div>
    </div>
</body>
</html>