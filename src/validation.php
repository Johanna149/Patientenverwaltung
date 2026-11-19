<?php

// Gemeinsame Prüfungen für alle Formulare (F5)

function isValidSvNummer(string $sv): bool
{
    return preg_match('/^\d{10}$/', $sv) === 1;
}

function isValidDate(string $d): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    return $dt !== false && $dt->format('Y-m-d') === $d;
}

function isValidTime(string $t): bool
{
    $dt = DateTime::createFromFormat('H:i', $t);
    return $dt !== false && $dt->format('H:i') === $t;
}