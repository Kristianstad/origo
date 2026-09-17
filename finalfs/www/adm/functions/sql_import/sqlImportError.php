<?php

function sqlImportError(string $message): void
{
    http_response_code(400);
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    exit;
}
