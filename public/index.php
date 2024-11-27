<?php

function getLinkedInPostReactions($postUrl, $linkedinEmail, $linkedinPassword)
{
    $ch = curl_init();

    // Step 1: Log in to LinkedIn
    curl_setopt($ch, CURLOPT_URL, "https://www.linkedin.com/login");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'session_key' => $linkedinEmail,
        'session_password' => $linkedinPassword,
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Save cookies
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Reuse cookies
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Handle redirections
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36",
    ]);

    $loginResponse = curl_exec($ch);

    if (!$loginResponse) {
        echo "cURL Error: " . curl_error($ch);
        return false;
    }

    echo "Login Response:\n";
    echo htmlspecialchars($loginResponse); // Output the login response for debugging

    // Step 2: Fetch LinkedIn Post
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_POST, false);
    $postPage = curl_exec($ch);

    if (!$postPage) {
        echo "Unable to fetch the post. cURL Error: " . curl_error($ch);
        return false;
    }

    echo "Post Page Response:\n";
    echo htmlspecialchars($postPage); // Output the post page for debugging

    // Step 3: Extract Reactions (Example Pattern)
    preg_match_all('/"reactionType":"([^"]+)","count":([0-9]+)/', $postPage, $matches);

    if (empty($matches[1]) || empty($matches[2])) {
        echo "No reactions found or extraction failed.";
        return false;
    }

    $reactions = array_combine($matches[1], $matches[2]);

    // Close the cURL session
    curl_close($ch);

    return $reactions;
}

// Example usage
$postUrl = "https://www.linkedin.com/posts/vizzwebsolutions_sff2024-singaporefintechfestival-fintechinnovation-activity-7259792733274324993-M6WJ?utm_source=share&utm_medium=member_desktop";
$linkedinEmail = "irfanali970@yahoo.com";
$linkedinPassword = "hafiz_147_4862";

$reactions = getLinkedInPostReactions($postUrl, $linkedinEmail, $linkedinPassword);

if ($reactions) {
    echo "Reactions: " . json_encode($reactions, JSON_PRETTY_PRINT);
}
?>
