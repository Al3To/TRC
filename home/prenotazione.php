<?php

session_start();
if (!isset($_SESSION['id'])) {
    echo "Area riservata ai clienti";
    exit();
}

if (!isset($_GET['idRistorante'])) {
    Header('Location: /home/index.php');
    exit();
}

require_once("../db.php");

$sql = $conn->prepare("SELECT idProprietario, postiMassimi as postiLiberi
FROM ristoranti WHERE id = ?");
$sql->bind_param("i", $_GET['idRistorante']);
$sql->execute();
$result = $sql->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows < 1) {
    echo "<div class='container-fluid bg-dark text-white py-3'>
		<div class='container'>
			<h1 class='my-0'>Nessun ristorante trovato</h1>
		</div>
	</div>";
    exit();
}

$idUtente = $_SESSION['id'];
$numPostiLiberi = $row["postiLiberi"];
$idProprietario = $row["idProprietario"];
$erroreDataOra = "";
$errorenumPostiLiberi = "";
$idRistorante = $_GET['idRistorante'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        $DataOraPrenotazione = $_POST["dataOraPrenotazione"];
        $numeroDiPersonePrenotazione = $_POST["numeroDiPersonePrenotazione"];
        if (strtotime($DataOraPrenotazione) > strtotime(date('Y-m-d'))) {
            if ($numeroDiPersonePrenotazione <= $numPostiLiberi) {
                $timestamp = strtotime($DataOraPrenotazione);
                $date = date("Y-m-d", $timestamp);
                $hour = $_POST["hour"];

                $sql = $conn->prepare("SELECT (ristoranti.postiMassimi - COALESCE(SUM(prenotazioni.numeroPersone), 0)) AS postiDisponibili 
FROM ristoranti 
LEFT JOIN prenotazioni ON ristoranti.id = prenotazioni.idRistorante AND prenotazioni.data = ? AND prenotazioni.ora = ?
WHERE ristoranti.id = ?
GROUP BY ristoranti.id
");
                $sql->bind_param("ssi", $date, $hour, $idRistorante);
                $sql->execute();
                $result = $sql->get_result();
                $row = $result->fetch_assoc();
                
                if ($result->num_rows < 1) {
                    echo "
                    <div class='container-fluid bg-dark text-white py-3'>
            		  <div class='container'>
            			<h1 class='my-0'>Errore nella prenotazione</h1>
            	      </div>
            	    </div>";
                    exit();
                }
                $numPostiLiberi = $row["postiDisponibili"];
                if ($numeroDiPersonePrenotazione <= $numPostiLiberi) {
                    $sql = $conn->prepare("INSERT INTO prenotazioni (idRistorante, idUtente, data, ora, numeroPersone) VALUES (?, ?, ?, ?, ?)");

                    $sql->bind_param("iissi", $idRistorante, $idUtente, $date, $hour, $numeroDiPersonePrenotazione);
                    $result = $sql->execute();

                    if (!$result) {
                        echo "<div class='container-fluid bg-dark text-white py-3'>
            		<div class='container'>
            			<h1 class='my-0'>Errore nella prenotazione</h1>
            		</div>
            	</div>";
                        exit();
                    } else {

                        $_SESSION['notification'] = array(
                            'message' => 'Prenotazione effettuata il ' . date("d/m/Y",$date) . ' alle ' . $hour . ' per ' . $numeroDiPersonePrenotazione . ' persone.',
                            'type' => 'success'
                        );
                    }
                }
            } else {
                $errorenumPostiLiberi = "Posti insufficienti!";
            }
        } else {
            $erroreDataOra = "Data non valida!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.js"></script>
</head>

<body>
    <?php if (isset($_SESSION['notification'])) : ?>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Prenotazione effettuata con successo</h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $_SESSION["notification"]["message"]; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="closeModal" class="btn btn-secondary" mdb-data-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const modal = new mdb.Modal(document.getElementById('myModal'), {})
            modal.show();

            const btnclose = document.getElementById("closeModal");
            console.log(btnclose);

            btnclose.addEventListener('click', function() {
                <?php
                unset($_SESSION['notification']);
                ?>
                window.location.href = './';
            });

            const body = document.getElementsByTagName("body")[0];
            body.addEventListener("click", (event) => {
                <?php
                unset($_SESSION['notification']);
                ?>
                window.location.href = './';
            })
        </script>
    <?php endif; ?>

    <header class="container-fluid bg-dark text-white py-3">
        <div class="container">
            <a href="./index.php" class="text-white text-decoration-none">
                <h1 class="my-0">TRC Ristoranti</h1>
            </a>
        </div>
    </header>
    <main class="container my-5 mb-3 d-flex justify-content-center">
        <form method="post">
            <div class="mb-3">
                <label for="dataOraPrenotazione" class="form-label">Data ed ora prenotazione</label>
                <input id="dataOraPrenotazione" type="date" class="form-control" name="dataOraPrenotazione" required>
                <select class="form-select mt-3" name="hour" aria-label="Default select example">
                    <option selected>Seleziona ora</option>
                    <?php
                    if (isset($_GET["dataOraPrenotazione"])) {
                        $dataPrenotazione = $_GET["dataOraPrenotazione"];
                        $dataPrenotazione = date("Y-m-d", strtotime($dataPrenotazione));
                        $numeroGiornoPrenotazione = date("N", strtotime($dataPrenotazione));
                        $sql = "SELECT * FROM orari WHERE orari.idRistorante = ? AND orari.giornoSettimana = ?";
                        $sql = $conn->prepare($sql);
                        $sql->bind_param("ii", $idRistorante, $numeroGiornoPrenotazione);
                        $sql->execute();
                        $result = $sql->get_result();
                        if (!$result) {
                            echo "<div class='container-fluid bg-dark text-white py-3'>
            	                   	<div class='container'>
            	                   		<h1 class='my-0'>Errore nella prenotazione</h1>
            	                   	</div>
            	                  </div>";
                            exit();
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["oraApertura"] . " - " . $row["oraChiusura"] . "'>" . $row["oraApertura"] . " - " . $row["oraChiusura"] . "</option>";
                            }
                        }
                    }
                    ?>

                </select>
                <div id="dataOraPrenotazioneHelper" class="form-text text-warning">Seleziona la data e ora per la prenotazione</div>
                <?php if (isset($erroreDataOra)) : ?>
                    <div class="form-text text-danger"><?php echo $erroreDataOra; ?></div>
                <?php endif; ?>

            </div>
            <div class="mb-3">
                <label for="numeroDiPersonePrenotazione" class="form-label">Numero di persone</label>
                <input type="number" class="form-control" name="numeroDiPersonePrenotazione" min="1" max="<?php echo $numPostiLiberi; ?>" required>
                <?php if (isset($errorenumPostiLiberi)) : ?>
                    <div class="form-text text-danger"><?php echo $errorenumPostiLiberi; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-danger w-100">Effettua prenotazione</button>
        </form>
    </main>
    <script>
        const dataOraPrenotazione = document.getElementById("dataOraPrenotazione");

        dataOraPrenotazione.addEventListener("change", (event) => {
            const params = new URLSearchParams(window.location.search);
            params.set("dataOraPrenotazione", event.target.value);
            window.location.search = params.toString();
        });

        const params = new URLSearchParams(window.location.search);
        if (params.has("dataOraPrenotazione")) {
            dataOraPrenotazione.value = params.get("dataOraPrenotazione");
        }
    </script>
</body>

</html>