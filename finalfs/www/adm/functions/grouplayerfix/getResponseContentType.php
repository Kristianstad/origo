<?php

// Hjälpfunktion: Bestäm rätt Content-Type baserat på request-parametrar
function getResponseContentType($params) {
    if (isset($params['outputFormat']) && !empty($params['outputFormat'])) {
        // Använd exakt vad klienten begär + charset
        return $params['outputFormat'] . '; charset=utf-8';
    }
    // Fallback för WFS/WMS utan explicit format (oftast XML)
    return 'text/xml;charset=UTF-8';
}
