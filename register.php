<?php
require_once('db.php');

$nome = $_POST["nome"];
$cognome = $_POST["cognome"];
$email = $_POST["email"];
$password = $_POST["password"];

if (isset($nome) && isset($cognome) && isset($email) && isset($password)) {

    $password = hash("sha256", $password);

    $sql = $conn->prepare("SELECT * FROM utenti WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        echo "<meta http-equiv='refresh' content='3; url=./index.php'>";
        echo "Email già registrata";
        echo "<br><br><a href='./index.php'><button>Torna indietro</button></a>";
        return;
    }

    $sql = $conn->prepare("INSERT INTO utenti (nome, cognome, email, password, tipo) VALUES (?, ?, ?, ?, 'Cliente')");
    $sql->bind_param("ssss", $nome, $cognome, $email, $password);

    $sql->execute();

    echo "<meta http-equiv='refresh' content='3; url=./index.php'>";
    echo "Registrazione effetuata con successo";
    echo "<br><br><a href='./index.php'><button>Torna indietro</button></a>";
} else {
    echo "<meta http-equiv='refresh' content='3; url=./index.php'>";
    echo "Tutti i campi sono obbligatori";
    echo "<br><br><a href='./index.php'><button>Torna indietro</button></a>";
}
?>