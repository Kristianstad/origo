<?php
/**
 * Generell sessionskonfiguration för applikationen
 * 
 * Används av forwardauth.php, azure-callback.php m.fl.
 */

$forwardauthSessionConfig = [
    // Grundlivstid vid inloggning
    'baseLifetime'     => 60 * 60 * 10,      // 10 timmar

    // Sliding expiration – hur mycket sessionen förlängs vid aktivitet
    'slideExtension'   => 60 * 60 * 1,       // +1 timme per giltigt anrop

    // Absolut max-tid en session får leva (säkerhetsgräns)
    'absoluteMax'      => 60 * 60 * 24 * 10, // 10 dagar
];
