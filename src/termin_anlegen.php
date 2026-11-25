<?php
require_once 'db.php';

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

$fehler = '';

// Erlaubte Termindauern
$erlaubteDauern = [15, 30, 45, 60];

// Wenn das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datum = $_POST['datum'] ?? '';
    $uhrzeit = $_POST['uhrzeit'] ?? '';
    $grund = trim($_POST['grund'] ?? '');
    $patient_sv = $_POST['patient_sv'] ?? '';
    $arzt_id = $_POST['arzt_id'] ?? '';
    $dauer = $_POST['dauer'] ?? '';

    // Überprüfen, ob alle Felder ausgefüllt wurden
    if (
        $datum === '' ||
        $uhrzeit === '' ||
        $grund === '' ||
        $patient_sv === '' ||
        $arzt_id === '' ||
        $dauer === ''
    ) {
        $fehler = 'Bitte alle Felder ausfüllen.';

    // Überprüfen, ob eine erlaubte Termindauer ausgewählt wurde
    } elseif (!in_array((int)$dauer, $erlaubteDauern, true)) {

        $fehler = 'Bitte eine gültige Termindauer auswählen.';

    } else {

        // F4: Prüfen, ob sich Termine überschneiden

        $sql = "
            SELECT COUNT(*)
            FROM termin
            WHERE datum = :datum
            AND arzt_id = :arzt_id
            AND (
                uhrzeit < :neues_ende
                AND ADDTIME(uhrzeit, SEC_TO_TIME(dauer * 60)) > :neue_uhrzeit
            )
        ";

        $stmt = $pdo->prepare($sql);

        // Endzeit des neuen Termins berechnen
        $neuesEnde = date(
            'H:i:s',
            strtotime($uhrzeit) + ((int)$dauer * 60)
        );

        $stmt->execute([
            ':datum' => $datum,
            ':arzt_id' => $arzt_id,
            ':neues_ende' => $neuesEnde,
            ':neue_uhrzeit' => $uhrzeit
            
        ]);

        $anzahl = $stmt->fetchColumn();

        // Wenn sich Termine überschneiden
        if ($anzahl > 0) {

            $fehler = 'Fehler: Dieser Arzt hat zu dieser Zeit bereits einen Termin.';

        } else {

            // Wenn kein Termin überschneidet, neuen Termin speichern

            $sql = "
                INSERT INTO termin
                (datum, uhrzeit, dauer, grund, patient_sv, arzt_id)
                VALUES
                (:datum, :uhrzeit, :dauer, :grund, :patient_sv, :arzt_id)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':datum' => $datum,
                ':uhrzeit' => $uhrzeit,
                ':dauer' => (int)$dauer,
                ':grund' => $grund,
                ':patient_sv' => $patient_sv,
                ':arzt_id' => $arzt_id
            ]);

            // Weiterleitung zur Terminübersicht
            header('Location: termine.php');
            exit;
        }
    }
}
?>

<h1>Neuen Termin anlegen</h1>
 
<!--Meldung gehört zu F4:keine Doppelbuchung von Terminen zulassen-->
<?php if ($fehler): ?>
    <p><?= htmlspecialchars($fehler) ?></p>
<?php endif; ?>
 
 
<form method="POST">
<!--Auswahlfeld für Patienten, die in der Datenbank gespeichert sind-->
    <label>Patient:</label>
    <select name="patient_sv" required>
        <option value="">Patient auswählen</option>
        <?php foreach ($patienten as $patient): ?>
            <option value="<?= htmlspecialchars($patient['sv_nummer']) ?>">
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
            <option value="<?= $arzt['id'] ?>">
                <?= htmlspecialchars($arzt['titel'] . ' ' . $arzt['vorname'] . ' ' . $arzt['nachname'] . ' (' . $arzt['fachrichtung'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
 
    <br><br>
 
<!--hier wird das Datum, die Startzeit und der Grund für den Termin eingegeben-->
    <label>Datum:</label>
    <input type="date" name="datum" required>
 
    <br><br>
 
    <label>Startzeit:</label>
    <input type="time" name="uhrzeit" required>
 
    <br><br>
    <label>Termindauer:</label>

    <br><br>
<select name="dauer" required>
    <option value="">Dauer auswählen</option>
    <option value="15">15 Minuten</option>
    <option value="30">30 Minuten</option>
    <option value="45">45 Minuten</option>
    <option value="60">60 Minuten</option>
</select>

<br><br>
 
    <label>Grund:</label>
    <input type="text" name="grund" maxlength="255" required>
 
    <br><br>
 
<!--submit button, um den Termin zu speichern und ein Link, um den Vorgang abzubrechen und zurück zur Terminübersicht zu gelangen-->
    <button type="submit">Termin speichern</button>
    <a href="termine.php">Abbrechen</a>
 
</form>
 