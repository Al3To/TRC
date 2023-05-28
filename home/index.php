<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <style>
        .card {
            cursor: pointer;
            background-color: rgba(104, 99, 101, 0.09) !important;
            border-color: rgba(104, 99, 101, 0.14) !important;
        }
    </style>
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                const urlParams = new URLSearchParams(window.location.search);
                const latitude = urlParams.get('latitude');
                const longitude = urlParams.get('longitude');
                if (!latitude || !longitude)
                    navigator.geolocation.getCurrentPosition(showPosition);
            } else {

            }
        }

        function showPosition(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;

            const urlParams = new URLSearchParams(window.location.search);
            const latitude = urlParams.get('latitude');
            const longitude = urlParams.get('longitude');

            if (!(latitude || longitude)) {
                document.getElementById('btn').click();
            }
        }
    </script>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.js"></script>
</head>

<body onload="getLocation()">

    <header class="container-fluid bg-dark text-white py-3">
        <div class="container d-flex flex-row align-items-baseline justify-content-between">
            <a href="." class="text-white text-decoration-none">
                <h1 class="my-0">TRC Ristoranti</h1>
            </a>
            <?php
            echo '<div>';
            if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "Proprietario") {
                echo '<a href="../proprietario" class="text-white text-decoration-none"><h3 class="my-0">Area proprietari</h3></a>';
            }
            if (isset($_SESSION["id"]))
            {
                echo '<a href="../logout.php" class="text-white text-decoration-none">Esci</a>';
            }
            echo '</div>';
            ?>
        </div>
    </header>

    <body class="">
        <main class="container my-5">
            <form method="get" id="locationForm">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="search" placeholder="Cerca ristorante" aria-label="Cerca ristorante">
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    <button class="btn btn-danger" id="btn" type="submit">Cerca</button>
                </div>
            </form>
            <?php

            require_once('../db.php');

            if (!isset($_GET['latitude']) && !isset($_GET['longitude'])) {
                $sql = "SELECT * FROM `ristoranti`";

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card mb-3 shadow p-3 rounded" onclick="window.location.href=\'./ristorante.php?id=' . $row["id"] . '&url=\' + encodeURIComponent(window.location.href)">';
                        echo '<div class="card-body">';
                        echo '<h2 class="card-title text-danger">' . $row["nome"] . '</h2>';
                        echo '<p class="card-text text-dark"><strong>Indirizzo:</strong> ' . $row["citta"] . ", " . $row['via'] . ", " .  $row["civico"] . ", " . $row['cap'] . '</p>';
                        echo '<p class="card-text"><strong>Telefono:</strong> ' . $row["telefono"] . '</p>';
                        echo '<p class="card-text"><strong>Descrizione:</strong> ' . $row["descrizione"] . '</p>';
                        echo '</div>';
                        echo "</div>";
                    }
                } else {
                    echo "<div class='container-fluid bg-dark text-white py-3'>
	                      	<div class='container'>
	                      		<h1 class='my-0'>Nessun ristorante trovato</h1>
	                      	</div>
	                      </div>";
                }
                $conn->close();
                echo "</main>";
                echo "</body>";
            }



            if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
                $latitude = $_GET['latitude'];
                $longitude = $_GET['longitude'];
                if (!isset($_GET['search'])) {
                    $sql = $conn->prepare("SELECT *, SQRT(POWER(? - coordinateX, 2)+POWER(? - coordinateY, 2)) AS distanza FROM `ristoranti` ORDER BY distanza ASC");
                    $sql->bind_param("dd", $latitude, $longitude);
                    $sql->execute();
                } else {
                    $search = $_GET['search'];
                    $sql = $conn->prepare("SELECT *, SQRT(POWER(? - coordinateX, 2)+POWER(? - coordinateY, 2)) AS distanza FROM `ristoranti` WHERE nome LIKE ? OR descrizione LIKE ? OR citta LIKE ? ORDER BY distanza ASC");
                    $searchTerm = "%" . $search . "%";
                    $sql->bind_param("ddsss", $latitude, $longitude, $searchTerm, $searchTerm, $searchTerm);
                    $sql->execute();
                }
                $result = $sql->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card my-3 mb-3 shadow p-3 rounded" onclick="window.location.href=\'./ristorante.php?id=' . $row["id"] . '&url=\' + encodeURIComponent(window.location.href)">';
                        echo '<div class="card-body">';
                        echo '<h2 class="card-title text-danger">' . $row["nome"] . '</h2>';
                        echo '<p class="card-text"><strong>Indirizzo:</strong> ' . $row["citta"] . ", " . $row['via'] . ", " .  $row["civico"] . ", " . $row['cap'] . '</p>';
                        echo '<p class="card-text"><strong>Telefono:</strong> ' . $row["telefono"] . '</p>';
                        echo '<p class="card-text"><strong>Descrizione:</strong> ' . $row["descrizione"] . '</p>';
                        echo '</div>';
                        echo "</div>";
                    }
                } else {
                    echo "<div class='container-fluid rounded border py-3'>
	                      	<div class='container'>
	                      		<h3 class='my-0'>Nessun ristorante trovato</h3>
	                      	</div>
	                      </div>";
                }
                $conn->close();
                echo "</main>";
                echo "</body>";
            }

            ?>



    </body>

</html>