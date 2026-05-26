<?php
/**
 * Hämtar grupper med Microsoft Graph token
 */

function getAzureGroups($graphToken): array
{
    $groups = [];
    $url = 'https://graph.microsoft.com/v1.0/me/memberOf/microsoft.graph.group?$select=displayName,id';

    do {
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
            break;
        }

        $data = json_decode($response, true);
        foreach ($data['value'] ?? [] as $group) {
            if (!empty($group['displayName'])) {
                $groups[] = $group['displayName'];
            }
        }

        $url = $data['@odata.nextLink'] ?? null;

    } while ($url !== null);

    return array_unique($groups);
}
