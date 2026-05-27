<?php
/**
 * Hämtar Microsoft Graph token
 */

function getGraphToken($token)
{
    $provider = getAzureProvider();

    try {
        // Hämta ett token specifikt för Microsoft Graph
        $graphToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $token->getRefreshToken(),
            'scope'         => 'https://graph.microsoft.com/.default'
        ]);
    } catch (\Exception $e) {
        error_log("Kunde inte refresha Graph token: " . $e->getMessage());
        $graphToken = $token; // fallback
    }
    
    return $graphToken;
}
