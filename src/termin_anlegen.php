<?php
require_once 'db.php';
require_once 'validation.php';
 
// Patienten aus der Datenbank laden
$stmt = $pdo->query("
    SELECT sv_nummer, vorname, nachname
    FROM patient
    ORDER BY nachname, vorname
");
$patienten = $stmt->fetchAll();
 
// Ärzte aus der Datenbank laden
$stmt = $pdo->query("
    SELECT id, titel, vorname, nachname, fachrichtung
    FROM arzt
    ORDER BY nachname, vorname
");
$aerzte = $stmt->fetchAll();
 
$fehler = [];
$datum = $uhrzeit = $grund = $patient_sv = $arzt_id = '';
 
// Wenn das Formular abgeschickt (POST) wurde, die Daten verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datum = trim($_POST['datum'] ?? '');
    $uhrzeit = trim($_POST['uhrzeit'] ?? '');
    $grund = trim($_POST['grund'] ?? '');
    $patient_sv = trim($_POST['patient_sv'] ?? '');
    $arzt_id = trim($_POST['arzt_id'] ?? '');
 
    //F5: serverseitige Validierung, alle Fehler werden gesammelt
    if (!isValidDate($datum)) {
        $fehler[] = 'Bitte ein gültiges Datum angeben.';
    }

    if (!isValidTime($uhrzeit)) {
        $fehler[] = 'Bitte eine gültige Uhrzeit angeben.';
    }

    if ($grund === '') {
        $fehler[] = 'Grund ist ein Pflichtfeld.';
    } elseif (mb_strlen($grund) > 255) {
        $fehler[] = 'Der Grund darf höchstens 255 Zeichen haben.';
    }

    // Patient:in muss gewählt sein und in der Datenbank existieren
    if (!isValidSvNummer($patient_sv)) {
        $fehler[] = 'Bitte eine Patientin / einen Patienten auswählen.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM patient WHERE sv_nummer = :sv');
        $stmt->execute([':sv' => $patient_sv]);
        if ($stmt->fetchColumn() == 0) {
            $fehler[] = 'Diese Patientin / dieser Patient existiert nicht.';
        }
    }

    // Arzt muss gewählt sein und in der Datenbank existieren
    if (!ctype_digit($arzt_id)) {
        $fehler[] = 'Bitte einen Arzt auswählen.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM arzt WHERE id = :id');
        $stmt->execute([':id' => $arzt_id]);
        if ($stmt->fetchColumn() == 0) {
            $fehler[] = 'Dieser Arzt existiert nicht.';
        }
    }

    //F4: keine Doppelbuchung von Terminen zulassen
    //wird nur geprüft, wenn alle Eingaben bis hierher gültig sind
    if (!$fehler) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM termin
            WHERE arzt_id = :arzt_id AND datum = :datum AND uhrzeit = :uhrzeit
        ");
        $stmt->execute([
            ':arzt_id' => $arzt_id,
            ':datum' => $datum,
            ':uhrzeit' => $uhrzeit
        ]);

        if ($stmt->fetchColumn() > 0) {
            $fehler[] = 'Fehler: Dieser Arzt hat zu dieser Zeit bereits einen Termin.';
        }
    }
 
    //wenn alle Eingaben gültig sind, dann wird ein Termin in der Datenbank gespeichert
    //wenn Speicherung abgeschlossen -> weiterleitung auf termine.php
    if (!$fehler) {
        $sql = "
            INSERT INTO termin
            (datum, uhrzeit, grund, patient_sv, arzt_id)
            VALUES
            (:datum, :uhrzeit, :grund, :patient_sv, :arzt_id)
        ";
 
        $stmt = $pdo->prepare($sql);
 
        //Sicherheitsnetz: falls die Datenbank trotzdem eine Doppelbuchung meldet (z. B. zwei gleichzeitige Anfragen)
        try {
            $stmt->execute([
                ':datum' => $datum,
                ':uhrzeit' => $uhrzeit,
                ':grund' => $grund,
                ':patient_sv' => $patient_sv,
                ':arzt_id' => $arzt_id
            ]);
 
            header('Location: termine.php');
            exit;
 
        } catch (PDOException $e) {
            // 1062 = doppelter Eintrag (UNIQUE-Constraint)
            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                $fehler[] = 'Fehler: Dieser Arzt hat zu dieser Zeit bereits einen Termin.';
            } else {
                throw $e;
            }
        }
    }
}
?>
 
<h1>Neuen Termin anlegen</h1>
 
<!--Meldungen gehören zu F5 (Validierung) und F4 (keine Doppelbuchung von Terminen)-->
<?php if ($fehler): ?>
    <ul style="color:red;">
        <?php foreach ($fehler as $f): ?>
            <li><?= htmlspecialchars($f) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
 
 
<form method="POST">
<!--Auswahlfeld für Patienten, die in der Datenbank gespeichert sind-->
    <label>Patient:</label>
    <select name="patient_sv" required>
        <option value="">Patient auswählen</option>
        <?php foreach ($patienten as $patient): ?>
            <option value="<?= htmlspecialchars($patient['sv_nummer']) ?>" <?= (string)$patient['sv_nummer'] === $patient_sv ? 'selected' : '' ?>>
                <?= htmlspecialchars($patient['vorname'] . ' ' . $patient['nachname']) ?>
            </option>
        <?php endforeach; ?>
    </select>
 
    <br><br>
 
<!--Auswahlfeld für Ärzte, die in der Datenbank gespeichert sind-->
    <label>Arzt:</label>
    <select name="arzt_id" required>
<!--gibt name des Auswahlfeldes an, das beim Absenden des Formulars an den Server gesendet wird-->
<!--required bedeutet, dass man etwas auswählen muss, bevor man das Formular überhaupt absenden kann-->
        <option value="">Arzt auswählen</option>
        <?php foreach ($aerzte as $arzt): ?>
            <option value="<?= $arzt['id'] ?>" <?= (string)$arzt['id'] === $arzt_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($arzt['titel'] . ' ' . $arzt['vorname'] . ' ' . $arzt['nachname'] . ' (' . $arzt['fachrichtung'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
 
    <br><br>
 
<!--hier wird das Datum, die Startzeit und der Grund für den Termin eingegeben-->
    <label>Datum:</label>
    <input type="date" name="datum" value="<?= htmlspecialchars($datum) ?>" required>
 
    <br><br>
 
    <label>Startzeit:</label>
    <input type="time" name="uhrzeit" value="<?= htmlspecialchars($uhrzeit) ?>" required>
 
    <br><br>
 
    <label>Grund:</label>
    <input type="text" name="grund" maxlength="255" value="<?= htmlspecialchars($grund) ?>" required>
 
    <br><br>
 
<!--submit button, um den Termin zu speichern und ein Link, um den Vorgang abzubrechen und zurück zur Terminübersicht zu gelangen-->
    <button type="submit">Termin speichern</button>
    <a href="termine.php">Abbrechen</a>
 
</form>