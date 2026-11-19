<?php

require 'db.php';
require_once 'validation.php';

$sv = trim($_POST['sv_nummer'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isValidSvNummer($sv)) {

    $stmt = $pdo->prepare(
        'DELETE FROM patient WHERE sv_nummer = :sv'
    );

    $stmt->execute([
        'sv' => $sv
    ]);
}

header('Location: index.php');
exit;

?>