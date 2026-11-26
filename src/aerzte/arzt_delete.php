<?php

require_once __DIR__ . '/../patienten/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? '';

    if (!ctype_digit((string) $id) || (int) $id <= 0) {
        die('Ungültige Arzt-ID.');
    }

    try {
        $stmt = $pdo->prepare(
            'DELETE FROM arzt WHERE id = :id'
        );

        $stmt->execute([
            'id' => (int) $id
        ]);

    } catch (PDOException $e) {
        die('Arzt kann nicht gelöscht werden, weil er noch Termine hat.');
    }
}

header('Location: arzt_index.php');
exit;

?>