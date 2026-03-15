<!DOCTYPE html>
<html>
<head>
    <title>IBAN Wijziging Melding</title>
</head>
<body>
    <h2>Frequente IBAN Wijziging Melding</h2>
    <p>Een gebruiker heeft voor de {{ $changeCount }}e keer hun IBAN gewijzigd:</p>

    <h3>Gebruikersgegevens:</h3>
<ul>
    <li>Gebruikersnaam: {{ $userData->username }}</li>
    <li>E-mail: {{ $userData->email }}</li>
    <li>Nieuwe IBAN: {{ $userData->banking->iban }}</li>
</ul>

<p>Controleer dit account op mogelijk verdachte activiteiten.</p>