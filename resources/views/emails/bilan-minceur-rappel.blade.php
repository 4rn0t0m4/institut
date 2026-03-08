<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de rappel – Bilan Minceur</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 15px; color: #333; background: #f9f9f9; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #276e44; color: #fff; padding: 24px 30px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .body { padding: 28px 30px; }
        .field { margin-bottom: 16px; }
        .field strong { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #888; margin-bottom: 4px; }
        .field span { font-size: 15px; color: #222; }
        .tag { display: inline-block; background: #e8f5ee; color: #276e44; font-size: 13px; font-weight: 600; padding: 3px 10px; border-radius: 4px; margin: 2px; }
        .footer { border-top: 1px solid #eee; padding: 16px 30px; font-size: 13px; color: #aaa; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Nouvelle demande de rappel — Bilan Minceur</h1>
    </div>
    <div class="body">
        <div class="field">
            <strong>Nom</strong>
            <span>{{ $data['prenom'] }} {{ $data['nom'] }}</span>
        </div>
        <div class="field">
            <strong>E-mail</strong>
            <span><a href="mailto:{{ $data['email'] }}" style="color:#276e44;">{{ $data['email'] }}</a></span>
        </div>
        <div class="field">
            <strong>Téléphone</strong>
            <span>{{ $data['telephone'] }}</span>
        </div>
        <div class="field">
            <strong>Objectifs</strong>
            <span>
                @foreach($data['objectifs'] as $obj)
                    <span class="tag">{{ $obj }}</span>
                @endforeach
            </span>
        </div>
        <div class="field">
            <strong>Kilos / centimètres souhaités</strong>
            <span>{{ $data['objectif_quantite'] ?: '—' }}</span>
        </div>
        <div class="field">
            <strong>Délai souhaité</strong>
            <span>{{ $data['objectif_delai'] ?: '—' }}</span>
        </div>
        <div class="field">
            <strong>A accepté les conditions</strong>
            <span>{{ isset($data['acceptation']) ? 'Oui ✓' : 'Non' }}</span>
        </div>
    </div>
    <div class="footer">Institut Corps à Cœur — message généré automatiquement</div>
</div>
</body>
</html>
