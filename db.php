<?php
    $DB_HOST = "localhost";
    $DB_USERNAME  = "tommasialessandro";
    $DB_PASSWORD = "";
    $DB_DB = "my_tommasialessandro";

    $conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_DB);

    if ($conn->connect_error) {
        die("Errore connessione: " . $conn->connect_error);
    }

    $sql = 
    "CREATE TABLE IF NOT EXISTS ristoranti (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome TEXT NOT NULL,
        descrizione TEXT NOT NULL,
        telefono TEXT NOT NULL,
        citta TEXT NOT NULL,
        via TEXT NOT NULL,
        civico TEXT NOT NULL,
        cap TEXT NOT NULL,
        coordinateX DOUBLE NOT NULL,
        coordinateY DOUBLE NOT NULL,
        idProprietario INT NOT NULL,
        postiMassimi INT UNSIGNED NOT NULL
    )";

    $conn->query($sql);

    $sql = 
    "CREATE TABLE IF NOT EXISTS utenti (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome TEXT NOT NULL,
        cognome TEXT NOT NULL,
        email TEXT NOT NULL,
        password TEXT NOT NULL,
        tipo TEXT NOT NULL
    )";

    $conn->query($sql);

    $sql = 
    "CREATE TABLE IF NOT EXISTS prenotazioni (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        idRistorante INT NOT NULL,
        idUtente INT NOT NULL,
        data TEXT NOT NULL,
        ora TEXT NOT NULL,
        numeroPersone INT NOT NULL
    )";

    $conn->query($sql);

    $sql =
    "CREATE TABLE IF NOT EXISTS orari (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        idRistorante INT NOT NULL,
        giornoSettimana INT NOT NULL,
        oraApertura TEXT NOT NULL,
        oraChiusura TEXT NOT NULL
    )";

    $conn->query($sql);

    unset($sql);
        
?>