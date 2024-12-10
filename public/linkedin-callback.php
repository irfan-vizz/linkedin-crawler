<?php
$client_id = '78gc6x52m62941';
$client_secret = 'WPL_AP1.3sEjhyrnzrerluMN.4Y02tQ==';
$redirect_uri = 'https://linkedin-crowler.updatemedaily.com/linkedin-callback';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Exchange authorization code for access token
    $token_url = "https://www.linkedin.com/oauth/v2/accessToken";
    $post_data = http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret
    ]);

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $response = curl_exec($ch);
    curl_close($ch);
    echo 'Token Exchange Response: ' . $response;
    $response_data = json_decode($response, true);

    if (isset($response_data['access_token'])) {
        $access_token = $response_data['access_token'];

        // Fetch LinkedIn profile information
        $profile_url = "https://api.linkedin.com/v2/me";
        $headers = [
            "Authorization: Bearer $access_token"
        ];

        $ch = curl_init($profile_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $profile_response = curl_exec($ch);
        curl_close($ch);

        $profile_data = json_decode($profile_response, true);
        
        // Display user data
        echo '<pre>';
        print_r($profile_data);
        echo '</pre>';
    } else {
        echo "Error fetching access token.";
    }
} else {
    echo "Authorization failed.";
}
?>
