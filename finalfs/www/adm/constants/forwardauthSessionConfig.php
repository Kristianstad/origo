<?php
/**
 * Generell sessionskonfiguration för applikationen
 * 
 * Används av forwardauth.php, azure-callback.php m.fl.
 */

$forwardauthSessionConfig = [
    // Grundlivstid vid inloggning
    'baseLifetime'     => 60 * 60 * 24 * 10,      // 10 dagar

    // Sliding expiration – hur mycket sessionen förlängs vid aktivitet
    'slideExtension'   => 60 * 60 * 24 * 5,       // +5 dagar per giltigt anrop

    // Absolut max-tid en session får leva (säkerhetsgräns)
    'absoluteMax'      => 60 * 60 * 24 * 30,      // 30 dagar
];
