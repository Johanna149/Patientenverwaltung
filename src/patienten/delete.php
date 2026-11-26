<?php

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare(
        'DELETE FROM patient WHERE sv_nummer = :sv'
    );

    $stmt->execute([
        'sv' => $_POST['sv_nummer']
    ]);
}

header('Location: ../index.php');
exit;

?>