<?php
// API REST de la V4, inchangée : le front Vue n'en dépend que par son contrat.
//
//   GET /api/formations        → 200 + liste
//   GET /api/formations/{id}   → 200 + une formation, ou 404
//   autre verbe                → 405
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

// CORS : utile si le front appelle directement localhost:8000.
// Avec le proxy de Vite (vite.config.js), ces en-têtes ne servent plus en développement.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function repondre(int $code, $donnees): void
{
    http_response_code($code);
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
}

$methode = $_SERVER['REQUEST_METHOD'];
$chemin  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);   // ex. /api/formations/3
$parties = array_values(array_filter(explode('/', $chemin)));  // ['api','formations','3']

if (($parties[0] ?? '') !== 'api' || ($parties[1] ?? '') !== 'formations') {
    repondre(404, ['erreur' => 'Ressource inconnue']);
}

if ($methode !== 'GET') {
    header('Allow: GET');
    repondre(405, ['erreur' => 'Méthode non autorisée']);
}

$db = getDb();
$id = $parties[2] ?? null;

if ($id === null) {
    $rows = $db->query('SELECT * FROM formations ORDER BY niveau, titre')->fetchAll();
    repondre(200, $rows);
}

$stmt = $db->prepare('SELECT * FROM formations WHERE id = ?');
$stmt->execute([(int)$id]);
$formation = $stmt->fetch();

$formation
    ? repondre(200, $formation)
    : repondre(404, ['erreur' => "Formation $id introuvable"]);
