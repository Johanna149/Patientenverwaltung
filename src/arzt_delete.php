<?php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $stmt = $pdo->prepare(
            'DELETE FROM arzt WHERE id = :id'
        );

        $stmt->execute([
            'id' => $_POST['id']
        ]);

    } catch (PDOException $e) {
        die('Arzt kann nicht gelöscht werden, weil er noch Termine hat.');
    }
}

header('Location: index.php');
exit;
?>