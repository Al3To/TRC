<?php

require_once('db.php');

$email = $_POST["email"];
$password = $_POST["password"];

if (isset($email) && isset($password)) {

    $password = hash("sha256", $password);

    $sql = $conn->prepare("SELECT * FROM utenti WHERE email = ? AND password = ?");
    $sql->bind_param("ss", $email, $password);

    $sql->execute();

    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        session_start();
        $_SESSION["id"] = $row["id"];
        $_SESSION["nome"] = $row["nome"];
        $_SESSION["cognome"] = $row["cognome"];
        $_SESSION["email"] = $row["email"];
        $_SESSION["tipo"] = $row["tipo"];

        header("Location: ./home/index.php");
    } else {
        echo "<meta http-equiv='refresh' content='3; url=./index.php'>";
        echo "Email o password errati";
        echo "<br><br><a href='./index.php'><button>Torna indietro</button></a>";
    }
} else {
    echo "<meta http-equiv='refresh' content='3; url=./index.php'>";
    echo "Email e password sono obbligatori";
    echo "<br><br><a href='./index.php'><button>Torna indietro</button></a>";
}

?>