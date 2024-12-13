<!DOCTYPE html>
<html>

<head>
    <title>Nouvelle demande personnalisée</title>
</head>

<body>
    <h1>Nouvelle demande personnalisée</h1>
    <p><strong>Utilisateur :</strong> {{ $data['user_name'] }}</p>
    <p><strong>Email :</strong> {{ $data['user_email'] }}</p>
    <p><strong>Téléphone :</strong> {{ $data['phone'] ?? 'Non fourni' }}</p>
    <p><strong>Catégorie :</strong> {{ $data['category'] }}</p>
    <p><strong>Message :</strong></p>
    <p>{{ $data['message'] }}</p>

    @if (!empty($data['images']))
        <h2>Images :</h2>
        <ul>
            @foreach ($data['images'] as $image)
                <li><a href="{{ $image }}" target="_blank">Voir l'image</a></li>
            @endforeach
        </ul>
    @endif
</body>

</html>
