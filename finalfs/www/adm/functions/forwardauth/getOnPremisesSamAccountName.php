<?php
/**
 * Hämtar grupper med Microsoft Graph token
 */

function getOnPremisesSamAccountName($graphToken)
{
    $url = 'https://graph.microsoft.com/v1.0/me?$select=onPremisesSamAccountName';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $graphToken->getToken(),
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Microsoft Graph Error ($httpCode): " . substr($response, 0, 500));
        return null;
    }

    $data = json_decode($response, true);
    return $data["onPremisesSamAccountName"];
}
