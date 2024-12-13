<!DOCTYPE html>
<html>

<head>
    <title>Message du site Wooden Factory</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <tr>
                        <td
                            style="padding: 20px; text-align: center; background-color: #39905e; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Nouveau Message du site Wooden
                                Factory</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px;">
                            <p style="font-size: 16px; color: #333333; margin-bottom: 10px;">
                                <strong>Nom :</strong> {{ $data['name'] }}
                            </p>
                            <p style="font-size: 16px; color: #333333; margin-bottom: 10px;">
                                <strong>Email :</strong> {{ $data['email'] }}
                            </p>
                            <p style="font-size: 16px; color: #333333; margin-bottom: 20px;">
                                <strong>Message :</strong>
                            </p>
                            <p style="font-size: 16px; color: #555555; line-height: 1.5;">
                                {{ $data['message'] }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding: 20px; text-align: center; background-color: #f3f4f6; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                            <p style="font-size: 14px; color: #888888; margin: 0;">
                                &copy; {{ date('Y') }} Votre Entreprise. Tous droits réservés.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
