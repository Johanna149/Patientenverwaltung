<?php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sv_nummer = $_POST['sv_nummer'] ?? '';

    if ($sv_nummer === '') {
        die('Keine SV-Nummer angegeben!');
    }

    try {
        $stmt = $pdo->prepare(
            'DELETE FROM patient WHERE sv_nummer = :sv_nummer'
        );

        $stmt->execute([
            'sv_nummer' => $sv_nummer
        ]);

        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        die('Fehler beim Löschen: ' . $e->getMessage());
    }

} else {
    header('Location: index.php');
    exit;
}
