<?php
/**
 * Genererar Azure Authorize URL
 */
function getAzureAuthUrl()
{
    $provider = getAzureProvider();
    $authUrl = $provider->getAuthorizationUrl([
        //'prompt' => 'select_account'   // Låter användaren välja konto
    ]);

    $_SESSION['oauth2state'] = $provider->getState();
    return $authUrl;
}
