<?php
session_start();
if (!isset($_SESSION['id'])) {
    echo "Area riservata ai proprietari";
    exit();
}

if ($_SESSION['tipo'] != 'Proprietario') {
    echo "Area rieservata ai proprietari";
    exit();
}

require_once('../db.php');
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
    <header class="container-fluid bg-dark text-white py-3">
        <div class="container d-flex flex-row align-items-baseline justify-content-between">
            <a href="." class="text-white text-decoration-none">
                <h1 class="my-0">TRC Ristoranti - Area proprietario</h1>
            </a>
            <a href="../home" class="text-white text-decoration-none">
                <h3 class="my-0">Area cliente</h3>
            </a>
        </div>
    </header>
    <main class="container my-5">
        <div class="card">
            <div class="card-body border rounded">
                <?php
                $sql = "SELECT * FROM ristoranti WHERE idProprietario=" . $_SESSION['id'];
                $result = $conn->query($sql);
                $ristorante = $result->fetch_assoc();
                ?>

                <h3 class="card-title">Informazioni ristorante</h3>
                <div class="card-text"><b>Nome:</b> <?php echo $ristorante['nome'] ?></div>
                <div class="card-text"><b>Descrizione:</b> <?php echo $ristorante['descrizione'] ?></div>
                <div class="card-text"><b>Indirizzo:</b> <?php echo $ristorante['via'] . " " . $ristorante['civico'] . ", " . $ristorante['citta'] ?></div>
                <div class="card-text"><b>Posti massimi per fascia oraria:</b> <?php echo $ristorante['postiMassimi'] ?></div>
            </div>
        </div>
        <div class="border rounded p-4 pt-0 mt-4">
            <h1 class="mt-4 mb-3">Lista prenotazioni</h1>
            <form method="get" id="locationForm">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="search" placeholder="Cerca prenotazione" aria-label="Cerca prenotazione">
                    <button class="btn btn-danger" id="btn" type="submit">Cerca</button>
                </div>
            </form>
            <div class="card shadow-0 mt-4">
                <div class="card-body p-0">
                    <h3 class="card-title">Prenotazioni</h3>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Posti</th>
                                <th>Data</th>
                                <th>Fascia oraria</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['search'])) {
                                $sql = "SELECT prenotazioni.data, prenotazioni.ora, prenotazioni.numeroPersone, utenti.nome, utenti.cognome FROM prenotazioni
                            INNER JOIN utenti ON utenti.id = prenotazioni.idUtente
                            WHERE prenotazioni.idRistorante = ? AND (prenotazioni.data LIKE ? OR utenti.nome LIKE ? OR utenti.cognome LIKE ? OR prenotazioni.ora LIKE ? OR prenotazioni.numeroPersone LIKE ?)
                            ORDER BY prenotazioni.data DESC";
                                $search = "%" . $_GET['search'] . "%";
                            } else {
                                $sql = "SELECT prenotazioni.data, prenotazioni.ora, prenotazioni.numeroPersone, utenti.nome, utenti.cognome FROM prenotazioni
                            INNER JOIN utenti ON utenti.id = prenotazioni.idUtente
                            WHERE prenotazioni.idRistorante = ?
                            ORDER BY prenotazioni.data DESC";
                            }

                            $stmt = $conn->prepare($sql);
                            if (isset($_GET['search'])) {
                                $stmt->bind_param("isssss", $ristorante['id'], $search, $search, $search, $search, $search);
                            } else {
                                $stmt->bind_param("i", $ristorante['id']);
                            }

                            $stmt->execute();

                            $result = $stmt->get_result();

                            if ($result->num_rows == 0) {
                                echo "<tr><td colspan='5'>Non ci sono prenotazioni al momento</td></tr>";
                            }

                            while ($row = $result->fetch_assoc()) {
                                $data = date_create($row['data']);
                                $oggi = date_create(date("Y-m-d"));

                                date_time_set($data, 0, 0, 0);
                                date_time_set($oggi, 0, 0, 0);

                                if ($data < $oggi) {
                                    $colore = "text-danger";
                                } else {
                                    $colore = "";
                                }

                                echo "<tr>";
                                echo "<td>" . $row['nome'] . "</td>";
                                echo "<td>" . $row['cognome'] . "</td>";
                                echo "<td>" . $row['numeroPersone'] . "</td>";
                                echo "<td class='$colore'>" . $row['data'] . "</td>";
                                echo "<td>" . $row['ora'] . "</td>";
                                echo "</tr>";
                            }

                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>