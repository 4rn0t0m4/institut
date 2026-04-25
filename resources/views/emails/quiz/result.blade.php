<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f9fafb; margin: 0; padding: 20px; color: #374151; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: #276e44; padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 600; }
        .header p { color: #b0f1b9; margin: 8px 0 0; font-size: 14px; }
        .content { padding: 30px; }
        .result-badge { display: inline-block; background: #f0fdf4; color: #276e44; padding: 8px 20px; border-radius: 20px; font-size: 18px; font-weight: 600; margin: 16px 0; }
        .description { font-size: 14px; line-height: 1.7; color: #4b5563; margin: 20px 0; }
        .btn { display: inline-block; background: #276e44; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
        .footer a { color: #276e44; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Ton diagnostic de peau</h1>
        <p>Resultat personnalise</p>
    </div>

    <div class="content">
        <p style="font-size: 16px;">Bonjour,</p>

        <p>Merci d'avoir realise notre diagnostic de peau ! Voici ton resultat :</p>

        @if($completion->result)
            <div style="text-align: center; margin: 24px 0;">
                <div class="result-badge">{{ $completion->result->title }}</div>
            </div>

            @if($completion->result->description)
                <div class="description">
                    {!! $completion->result->description !!}
                </div>
            @endif

            @if($completion->result->image)
                <div style="text-align: center; margin: 20px 0;">
                    <img src="{{ url($completion->result->image) }}" alt="{{ $completion->result->title }}" style="max-width: 100%; max-height: 200px; border-radius: 12px;">
                </div>
            @endif
        @endif

        <p style="font-size: 14px; color: #4b5563; margin-top: 24px;">
            Tu peux aussi retrouver ton resultat complet avec des recommandations personnalisees en ligne :
        </p>

        <div style="text-align: center; margin: 28px 0;">
            <a href="{{ url('/diagnostic-de-peau/resultat/' . $completion->id) }}" style="display: inline-block; background: #276e44; color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600;">Voir mes recommandations</a>
        </div>

        <p style="font-size: 14px; color: #4b5563;">
            N'hesite pas a nous contacter si tu as des questions sur les soins adaptes a ton type de peau.
        </p>

        <p style="font-size: 14px; color: #4b5563; margin-top: 20px;">
            A bientot,<br>
            L'equipe Institut Corps a Coeur
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Institut Corps a Coeur — Tous droits reserves</p>
        <p style="margin-top: 4px;">
            <a href="{{ url('/') }}">institutcorpsacoeur.fr</a>
        </p>
    </div>
</div>
</body>
</html>
