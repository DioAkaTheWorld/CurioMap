<?php
declare(strict_types=1);

// --- AJOUT DE SECOURS CORS ---
// Permet d'afficher les erreurs fatales (bdd, vendor manquant...) au lieu d'une erreur CORS générique dans le navigateur
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}
// --- FIN AJOUT DE SECOURS ---

try {
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        throw new Exception("Dossier /vendor/ introuvable. Lancez 'composer install' dans le dossier /back/ !");
    }

    require __DIR__ . '/../vendor/autoload.php';

    $app = require __DIR__ . '/../conf/bootstrap.php';

    $app->run();

} catch (Throwable $e) {
    // En cas d'erreur critique, on renvoie du JSON pour que le front puisse le lire
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'CRITICAL_ERROR',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
