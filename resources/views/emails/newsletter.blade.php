<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f9fafb; margin: 0; padding: 20px; color: #374151; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: #276e44; padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 20px; font-weight: 600; }
        .header p { color: #b0f1b9; margin: 8px 0 0; font-size: 14px; }
        .content { padding: 30px; font-size: 15px; line-height: 1.7; color: #374151; }
        .content h2, .content h3, .content h4 { color: #276e44; }
        .content a { color: #276e44; }
        .content img { max-width: 100%; height: auto; border-radius: 8px; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
        .footer a { color: #276e44; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Institut Corps &agrave; Coeur</h1>
            <p>M&eacute;zidon Canon</p>
        </div>

        <div class="content">
            {!! $mailContent !!}
        </div>

        <div class="footer">
            <p><a href="{{ url('/boutique') }}">Voir la boutique</a></p>
            <p>Institut Corps &agrave; Coeur &mdash; 22 avenue Jean Jaur&egrave;s, 14270 M&eacute;zidon-Vall&eacute;e d'Auge</p>
            <p><a href="{{ url('/') }}">institutcorpsacoeur.fr</a></p>
        </div>
    </div>
</body>
</html>
