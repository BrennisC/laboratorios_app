<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$flag = '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'correct' => false,
        'message' => 'Use POST with flag=VSHOP{...}.',
    ]);
    exit;
}

$flag = trim($_POST['flag'] ?? '');
if ($flag === '') {
    $input = json_decode((string) file_get_contents('php://input'), true);
    $flag = trim($input['flag'] ?? '');
}

$stage = flag_stage($flag);
$correct = $stage !== null ? 'true' : 'false';
$safeFlag = str_replace("'", "''", $flag);
$safeStage = $stage === null ? 'NULL' : "'" . str_replace("'", "''", $stage) . "'";

db()->exec("INSERT INTO flag_submissions (flag, stage, correct, submitted_at) VALUES ('$safeFlag', $safeStage, $correct, CURRENT_TIMESTAMP)");

if ($stage === null) {
    echo json_encode([
        'correct' => false,
        'message' => 'Incorrect flag. Keep enumerating.',
    ]);
    exit;
}

echo json_encode([
    'correct' => true,
    'stage' => $stage,
    'message' => strtoupper($stage) . ' accepted. Check /status.php for your unlocked achievement.',
]);
