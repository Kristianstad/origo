<?php

function fixDuplicateDeclarations($jsCode) {
    // Split the code into lines
    $lines = explode("\n", $jsCode);
    $declarations = []; // Tracks declared variables and their initial keyword
    $isReassigned = []; // Tracks if a variable is reassigned/redeclared in root scope
    $outputLines = [];
    $depth = 0; // Tracks scope depth (0 = root scope)
    $inString = false;
    $stringChar = '';

    // Regular expression to match variable declarations (single or multi-variable)
    $pattern = '/^\s*(const|let|var)\s+(.+?)\s*=\s*([^;]+);$/';

    // Helper function to split values respecting nested structures
    function splitValues($valueString) {
        $values = [];
        $current = '';
        $depth = 0; // Tracks nested brackets/braces/parentheses
        $inString = false;
        $stringChar = '';

        for ($i = 0; $i < strlen($valueString); $i++) {
            $char = $valueString[$i];

            if ($inString) {
                if ($char === $stringChar && $valueString[$i - 1] !== '\\') {
                    $inString = false;
                }
                $current .= $char;
                continue;
            }

            if ($char === '"' || $char === "'") {
                $inString = true;
                $stringChar = $char;
                $current .= $char;
                continue;
            }

            if ($char === '{' || $char === '[' || $char === '(') {
                $depth++;
                $current .= $char;
                continue;
            }

            if ($char === '}' || $char === ']' || $char === ')') {
                $depth--;
                $current .= $char;
                continue;
            }

            if ($char === ',' && $depth === 0) {
                $values[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (trim($current) !== '') {
            $values[] = trim($current);
        }

        return $values;
    }

    // First pass: Identify declarations and reassignments in root scope
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        // Update scope depth
        for ($i = 0; $i < strlen($line); $i++) {
            $char = $line[$i];

            if ($inString) {
                if ($char === $stringChar && $line[$i - 1] !== '\\') {
                    $inString = false;
                }
                continue;
            }

            if ($char === '"' || $char === "'") {
                $inString = true;
                $stringChar = $char;
                continue;
            }

            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
            }
        }

        // Process declarations only in root scope (depth == 0 before this line)
        if ($depth === 0 && preg_match($pattern, $line, $matches)) {
            $keyword = $matches[1];
            $varList = $matches[2];
            $valueList = $matches[3];

            // Split variables and values
            $vars = array_map('trim', explode(',', $varList));
            $values = splitValues($valueList);

            // Ensure the number of variables matches the number of values
            if (count($vars) !== count($values)) {
                continue; // Skip malformed declarations
            }

            foreach ($vars as $varName) {
                if (isset($declarations[$varName])) {
                    $isReassigned[$varName] = true; // Mark as reassigned/redeclared
                } else {
                    $declarations[$varName] = $keyword; // Store initial keyword
                }
            }
        }
    }

    // Reset declarations and depth for second pass
    $declarations = [];
    $depth = 0;
    $inString = false;
    $stringChar = '';

    // Second pass: Process declarations in root scope, preserve all else
    foreach ($lines as $line) {
        $originalLine = $line; // Preserve original line
        $line = trim($line);
        if (empty($line)) {
            $outputLines[] = $originalLine;
            continue;
        }

        // Update scope depth
        $newDepth = $depth;
        for ($i = 0; $i < strlen($line); $i++) {
            $char = $line[$i];

            if ($inString) {
                if ($char === $stringChar && $line[$i - 1] !== '\\') {
                    $inString = false;
                }
                continue;
            }

            if ($char === '"' || $char === "'") {
                $inString = true;
                $stringChar = $char;
                continue;
            }

            if ($char === '{') {
                $newDepth++;
            } elseif ($char === '}') {
                $newDepth--;
            }
        }

        // Process only variable declarations in root scope
        if ($depth === 0 && preg_match($pattern, $line, $matches)) {
            $keyword = $matches[1];
            $varList = $matches[2];
            $valueList = $matches[3];

            // Split variables and values
            $vars = array_map('trim', explode(',', $varList));
            $values = splitValues($valueList);

            // Ensure the number of variables matches the number of values
            if (count($vars) !== count($values)) {
                $outputLines[] = $originalLine; // Keep malformed line unchanged
                continue;
            }

            foreach ($vars as $index => $varName) {
                $value = $values[$index];

                if (isset($declarations[$varName])) {
                    // Convert to assignment (include even for const redeclarations)
                    $outputLines[] = "$varName = $value;";
                } else {
                    // First declaration
                    $declarations[$varName] = true;
                    // Use 'let' if initially var/let or reassigned/redeclared, 'const' only if initially const and not reassigned
                    $newKeyword = ($keyword === 'var' || $keyword === 'let' || isset($isReassigned[$varName])) ? 'let' : 'const';
                    $outputLines[] = "$newKeyword $varName = $value;";
                }
            }
        } else {
            // Non-declaration line or non-root scope, keep unchanged
            $outputLines[] = $originalLine;
        }

        // Update depth after processing the line
        $depth = $newDepth;
    }

    // Join lines back into a string
    return implode("\n", $outputLines);
}

// Example usage with var/let declarations
/*
$jsCode = <<<JS
const a = 1, arr = [2,1,3], obj = {x:1,y:2};
var variable2 = 3;
let variable1 = 5;
let variable1 = 'banan';
const variable3 = 'hej';
const variable1 = 'boll';
const variable2 = '3';
const variable3 = 'hej';
let a = 1, b = 2;
const b = 3, variable1 = 'test';
const arr = [1, 2, 3], obj = {x: 1, y: 2};
// This is a comment
console.log('Hello');
x = 5;
function test() {
    const a = 10;
    let variable1 = 'inner';
    const arr = [4,5,6];
    x = 10;
    console.log('Inside function');
}
if (true) {
    const variable2 = 42;
}
JS;

$fixedCode = fixDuplicateDeclarations($jsCode);
echo $fixedCode;
*/

?>
