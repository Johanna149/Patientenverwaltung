
CREATE TABLE patient (
    sv_nummer VARCHAR(10) PRIMARY KEY,
    vorname VARCHAR(50) NOT NULL,
    nachname VARCHAR(50) NOT NULL,
    geburtsdatum DATE NOT NULL,
    telefon VARCHAR(20)
);

CREATE TABLE arzt (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(30),
    vorname VARCHAR(50) NOT NULL,
    nachname VARCHAR(50) NOT NULL,
    fachrichtung VARCHAR(100) NOT NULL
);

CREATE TABLE termin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    datum DATE NOT NULL,
    uhrzeit TIME NOT NULL,
    grund VARCHAR(255) NOT NULL,
    patient_sv VARCHAR(10) NOT NULL,
    arzt_id INT NOT NULL,

    FOREIGN KEY (patient_sv)
        REFERENCES patient(sv_nummer),

    FOREIGN KEY (arzt_id)
        REFERENCES arzt(id),

    UNIQUE (arzt_id, datum, uhrzeit)
);

-- 4 Ärzt:innen einfügen

INSERT INTO arzt (titel, vorname, nachname, fachrichtung) VALUES
('Dr.', 'Anna', 'Huber', 'Allgemeinmedizin'),
('Dr.', 'Markus', 'Bauer', 'Dermatologie'),
('Dr.', 'Julia', 'Wagner', 'Kardiologie'),
('Dr.', 'Thomas', 'Moser', 'Orthopädie');

-- 10 Patient:innen einfügen

INSERT INTO patient
(sv_nummer, vorname, nachname, geburtsdatum, telefon) VALUES
('1234567890', 'Lisa', 'Mayer', '1995-03-12', '06641234567'),
('2345678901', 'Paul', 'Schmidt', '1988-07-24', '06642345678'),
('3456789012', 'Laura', 'Fischer', '2001-11-05', '06643456789'),
('4567890123', 'David', 'Weber', '1976-01-18', '06644567890'),
('5678901234', 'Sarah', 'Pichler', '1992-09-30', '06645678901'),
('6789012345', 'Lukas', 'Steiner', '1985-05-14', '06646789012'),
('7890123456', 'Emma', 'Berger', '2003-02-21', '06647890123'),
('8901234567', 'Felix', 'Winkler', '1999-12-08', '06648901234'),
('9012345678', 'Sophie', 'Leitner', '1970-06-16', '06649012345'),
('0123456789', 'Max', 'Hofer', '1990-10-27', '06640123456');

-- 20 Termine einfügen

INSERT INTO termin
(datum, uhrzeit, grund, patient_sv, arzt_id) VALUES
('2026-11-02', '08:00:00', 'Kontrolluntersuchung', '1234567890', 1),
('2026-11-02', '09:00:00', 'Vorsorgeuntersuchung', '2345678901', 1),
('2026-11-02', '10:00:00', 'Allgemeine Untersuchung', '3456789012', 1),
('2026-11-02', '11:00:00', 'Nachkontrolle', '4567890123', 1),
('2026-11-02', '08:00:00', 'Hautkontrolle', '5678901234', 2),
('2026-11-02', '09:00:00', 'Hautuntersuchung', '6789012345', 2),
('2026-11-02', '10:00:00', 'Muttermalkontrolle', '7890123456', 2),
('2026-11-02', '11:00:00', 'Kontrolltermin', '8901234567', 2),
('2026-11-02', '08:00:00', 'Herzuntersuchung', '9012345678', 3),
('2026-11-02', '09:00:00', 'Kontrolluntersuchung', '0123456789', 3),
('2026-11-02', '10:00:00', 'Herzkontrolle', '1234567890', 3),
('2026-11-02', '11:00:00', 'Nachuntersuchung', '2345678901', 3),
('2026-11-02', '08:00:00', 'Orthopädische Kontrolle', '3456789012', 4),
('2026-11-02', '09:00:00', 'Gelenkuntersuchung', '4567890123', 4),
('2026-11-02', '10:00:00', 'Rückenkontrolle', '5678901234', 4),
('2026-11-02', '11:00:00', 'Bewegungsapparat untersuchen', '6789012345', 4),
('2026-11-03', '08:00:00', 'Allgemeine Untersuchung', '7890123456', 1),
('2026-11-03', '09:00:00', 'Vorsorgeuntersuchung', '8901234567', 2),
('2026-11-03', '10:00:00', 'Kontrolluntersuchung', '9012345678', 3),
('2026-11-03', '11:00:00', 'Nachkontrolle', '0123456789', 4);
