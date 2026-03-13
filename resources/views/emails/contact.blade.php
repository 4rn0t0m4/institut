<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f9fafb; margin: 0; padding: 20px; color: #374151; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: #276e44; padding: 24px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 18px; }
        .content { padding: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau message de contact</h1>
        </div>
        <div class="content">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 12px; font-weight: 600; color: #374151; width: 120px; vertical-align: top;">Nom</td>
                    <td style="padding: 8px 12px; color: #4b5563;">{{ $data['name'] }}</td>
                </tr>
                <tr style="background-color: #f9fafb;">
                    <td style="padding: 8px 12px; font-weight: 600; color: #374151; vertical-align: top;">Email</td>
                    <td style="padding: 8px 12px; color: #4b5563;"><a href="mailto:{{ $data['email'] }}" style="color: #276e44;">{{ $data['email'] }}</a></td>
                </tr>
                @if(!empty($data['phone']))
                <tr>
                    <td style="padding: 8px 12px; font-weight: 600; color: #374151; vertical-align: top;">Téléphone</td>
                    <td style="padding: 8px 12px; color: #4b5563;">{{ $data['phone'] }}</td>
                </tr>
                @endif
            </table>

            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px;">
                <p style="font-weight: 600; color: #276e44; margin-top: 0; margin-bottom: 8px;">Message :</p>
                <p style="color: #374151; white-space: pre-line; line-height: 1.6; margin: 0;">{{ $data['message'] }}</p>
            </div>
        </div>
    </div>
</body>
</html>
