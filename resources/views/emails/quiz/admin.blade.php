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
        .info-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .info-label { font-weight: 600; color: #374151; min-width: 120px; }
        .info-value { color: #6b7280; }
        .result-badge { display: inline-block; background: #f0fdf4; color: #276e44; padding: 6px 16px; border-radius: 16px; font-size: 14px; font-weight: 600; }
        .btn { display: inline-block; background: #276e44; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Nouveau diagnostic complete</h1>
        <p>{{ $completion->created_at->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="content">
        <p style="font-size: 16px; margin-bottom: 20px;">Un visiteur vient de completer le diagnostic de peau.</p>

        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 24px;">
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #374151; width: 140px;">Email</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">
                    <a href="mailto:{{ $completion->email }}" style="color: #276e44;">{{ $completion->email }}</a>
                </td>
            </tr>
            @if($completion->result)
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #374151;">Resultat</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">
                    <span class="result-badge">{{ $completion->result->title }}</span>
                </td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #374151;">Score</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">{{ $completion->score }} points</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #374151;">IP</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">{{ $completion->ip }}</td>
            </tr>
            @if($completion->user)
            <tr>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-weight: 600; color: #374151;">Compte client</td>
                <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; color: #6b7280;">{{ $completion->user->first_name }} {{ $completion->user->last_name }}</td>
            </tr>
            @endif
        </table>

        @if($completion->result && $completion->result->description)
            <div style="background: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <p style="font-size: 13px; font-weight: 600; color: #374151; margin: 0 0 8px;">Description du profil :</p>
                <div style="font-size: 13px; color: #6b7280; line-height: 1.6;">
                    {!! $completion->result->description !!}
                </div>
            </div>
        @endif

        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ url('/diagnostic-de-peau/resultat/' . $completion->id) }}" class="btn">Voir le resultat complet</a>
        </div>
    </div>

    <div class="footer">
        <p>Notification automatique — Institut Corps a Coeur</p>
    </div>
</div>
</body>
</html>
