<?php

// Hjälpfunktion: Hämta alla lagernamn under en grupp (rekursiv, namespace-säker)
function getLayerNamesInGroup($xml, $groupName) {
    $layerNames = [];

    // Namespace-säker sökning efter Layer med rätt Name
    $layers = $xml->xpath("//*[local-name()='Layer' and *[local-name()='Name' and normalize-space(text()) = '$groupName']]");
    if (empty($layers)) {
        return $layerNames;
    }

    $groupLayer = $layers[0];

    // Om inga barn-Layer → inte en grupp
    $childLayers = $groupLayer->xpath("*[local-name()='Layer']");
    if (empty($childLayers)) {
        return $layerNames;
    }

    // Samla rekursivt underliggande lager
    foreach ($childLayers as $childLayer) {
        $childNameNode = $childLayer->xpath("*[local-name()='Name']");
        if (empty($childNameNode)) {
            continue;
        }

        $childName = trim((string)$childNameNode[0]);

        if ($childName === '') {
            continue;  // GROK: Skyddar mot tomma namn
        }

        // Rekursiv om barnet själv är en grupp (har egna Layer-barn)
        $grandChildren = $childLayer->xpath("*[local-name()='Layer']");
        if (!empty($grandChildren)) {
            $layerNames = array_merge($layerNames, getLayerNamesInGroup($xml, $childName));
        } else {
            $layerNames[] = $childName;
        }
    }

    return array_unique($layerNames);  // GROK: Undvik dubletter om möjligt
}
