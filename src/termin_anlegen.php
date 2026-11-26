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

    $datum = trim($_POST['datum'] ?? '');
    $uhrzeit = trim($_POST['uhrzeit'] ?? '');
    $grund = trim($_POST['grund'] ?? '');
    $patient_sv = trim($_POST['patient_sv'] ?? '');
    $arzt_id = trim($_POST['arzt_id'] ?? '');
    $dauer = trim($_POST['dauer'] ?? '');

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

    // Überprüfen, ob die SV-Nummer genau 10 Ziffern enthält
    } elseif (!preg_match('/^[0-9]{10}$/', $patient_sv)) {
        $fehler = 'Die SV-Nummer muss genau 10 Ziffern enthalten.';

    // Überprüfen, ob eine erlaubte Termindauer ausgewählt wurde
    } elseif (!in_array((int)$dauer, $erlaubteDauern, true)) {
        $fehler = 'Bitte eine gültige Termindauer auswählen.';

    // Überprüfen, ob eine gültige Arzt-ID übermittelt wurde
    } elseif (
        filter_var($arzt_id, FILTER_VALIDATE_INT) === false ||
        (int)$arzt_id <= 0
    ) {
        $fehler = 'Bitte einen gültigen Arzt auswählen.';

    // Überprüfen, ob das Datum gültig ist
    } elseif (
        !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $datum) ||
        !checkdate(
            (int)substr($datum, 5, 2),
            (int)substr($datum, 8, 2),
            (int)substr($datum, 0, 4)
        )
    ) {
        $fehler = 'Bitte ein gültiges Datum eingeben.';

    // Überprüfen, ob die Uhrzeit gültig ist
    } elseif (!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $uhrzeit)) {
        $fehler = 'Bitte eine gültige Uhrzeit eingeben.';

    // Überprüfen, ob der Grund maximal 255 Zeichen lang ist
    } elseif (strlen($grund) > 255) {
        $fehler = 'Der Termin-Grund darf maximal 255 Zeichen lang sein.';

    } else {

        // Prüfen, ob der Patient tatsächlich in der Datenbank existiert
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM patient
            WHERE sv_nummer = :sv
        ");
        $stmt->execute([
            ':sv' => $patient_sv
        ]);

        if ($stmt->fetchColumn() == 0) {
            $fehler = 'Der ausgewählte Patient existiert nicht.';
        }

        // Prüfen, ob der Arzt tatsächlich in der Datenbank existiert
        if (!$fehler) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM arzt
                WHERE id = :arzt_id
            ");
            $stmt->execute([
                ':arzt_id' => (int)$arzt_id
            ]);

            if ($stmt->fetchColumn() == 0) {
                $fehler = 'Der ausgewählte Arzt existiert nicht.';
            }
        }

        if (!$fehler) {

            // Startzeit und Endzeit in Sekunden berechnen
            // Die Uhrzeit wird direkt in Stunden und Minuten zerlegt
            $teile = explode(':', $uhrzeit);

            $startSekunden = ((int)$teile[0] * 3600)
                           + ((int)$teile[1] * 60);

            $endeSekunden = $startSekunden + ((int)$dauer * 60);

            // Prüfen, ob der Termin über Mitternacht hinausgehen würde
            if ($endeSekunden >= 86400) {
                $fehler = 'Der Termin darf nicht über Mitternacht hinausgehen.';

            } else {

                // Endzeit des neuen Termins berechnen
                $neuesEnde = sprintf(
                    '%02d:%02d:%02d',
                    intdiv($endeSekunden, 3600),
                    intdiv($endeSekunden % 3600, 60),
                    0
                );

                // F4: Prüfen, ob sich Termine überschneiden
                $sql = "
                    SELECT COUNT(*)
                    FROM termin
                    WHERE datum = :datum
                    AND arzt_id = :arzt_id
                    AND (
                        uhrzeit < :neues_ende
                        AND ADDTIME(
                            uhrzeit,
                            SEC_TO_TIME(dauer * 60)
                        ) > :neue_uhrzeit
                    )
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':datum' => $datum,
                    ':arzt_id' => (int)$arzt_id,
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
                        ':arzt_id' => (int)$arzt_id
                    ]);

                    // Weiterleitung zur Terminübersicht
                    header('Location: termine.php');
                    exit;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Neuen Termin anlegen</title>
</head>
<body>

<h1>Neuen Termin anlegen</h1>

<!-- Meldung gehört zu F4: keine Doppelbuchung von Terminen zulassen -->
<?php if ($fehler): ?>
    <p style="color: red;">
        <?= htmlspecialchars($fehler, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<form method="POST">

    <!-- Auswahlfeld für Patienten, die in der Datenbank gespeichert sind -->
    <label>Patient:</label>

    <select name="patient_sv" required>
        <option value="">Patient auswählen</option>

        <?php foreach ($patienten as $patient): ?>
            <option
                value="<?= htmlspecialchars($patient['sv_nummer'], ENT_QUOTES, 'UTF-8') ?>"
                <?= (isset($patient_sv) && $patient_sv === $patient['sv_nummer']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($patient['vorname'] . ' ' . $patient['nachname'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <!-- Auswahlfeld für Ärzte, die in der Datenbank gespeichert sind -->
    <label>Arzt:</label>

    <select name="arzt_id" required>
        <option value="">Arzt auswählen</option>

        <?php foreach ($aerzte as $arzt): ?>
            <option
                value="<?= htmlspecialchars((string)$arzt['id'], ENT_QUOTES, 'UTF-8') ?>"
                <?= (isset($arzt_id) && (string)$arzt_id === (string)$arzt['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($arzt['titel'] . ' ' . $arzt['vorname'] . ' ' . $arzt['nachname'] . ' (' . $arzt['fachrichtung'] . ')', ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <!-- Hier werden Datum, Startzeit und Grund für den Termin eingegeben -->
    <label>Datum:</label>
    <input type="date" name="datum" value="<?= htmlspecialchars($datum ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <br><br>

    <label>Startzeit:</label>
    <input type="time" name="uhrzeit" value="<?= htmlspecialchars($uhrzeit ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <br><br>

    <!-- Auswahl der Termindauer -->
    <label>Termindauer:</label>

    <br><br>

    <select name="dauer" required>
        <option value="">Dauer auswählen</option>
        <option value="15" <?= (isset($dauer) && $dauer == 15) ? 'selected' : '' ?>>15 Minuten</option>
        <option value="30" <?= (isset($dauer) && $dauer == 30) ? 'selected' : '' ?>>30 Minuten</option>
        <option value="45" <?= (isset($dauer) && $dauer == 45) ? 'selected' : '' ?>>45 Minuten</option>
        <option value="60" <?= (isset($dauer) && $dauer == 60) ? 'selected' : '' ?>>60 Minuten</option>
    </select>

    <br><br>

    <label>Grund:</label>
    <input
        type="text"
        name="grund"
        maxlength="255"
        value="<?= htmlspecialchars($grund ?? '', ENT_QUOTES, 'UTF-8') ?>"
        required
    >

    <br><br>

    <!-- Submit-Button, um den Termin zu speichern -->
    <button type="submit">Termin speichern</button>
    <a href="termine.php">Abbrechen</a>

</form>

</body>
</html>
