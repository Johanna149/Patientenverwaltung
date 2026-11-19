<?php

require 'db.php';

$id = $_POST['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ctype_digit((string)$id)) {

    try {
        $stmt = $pdo->prepare(
            'DELETE FROM arzt WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

    } catch (PDOException $e) {
        die('Arzt kann nicht gelöscht werden, weil er noch Termine hat.');
    }
}

header('Location: index.php');
exit;
?>