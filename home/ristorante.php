<?php
session_start();

if (!isset($_GET['id'])) {
    Header('Location: ./home/index.php');
    exit();
}

require_once('../db.php');

$sql = $conn->prepare("SELECT * FROM ristoranti WHERE id = ?");
$sql->bind_param("i", $_GET['id']);
$sql->execute();
$result = $sql->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows < 1) {
    echo "Errore: ristorante non trovato";
    exit();
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

    <style>
        #card {
            width: 65%;
        }


        @media screen and (max-width: 600px) {
            #card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="container-fluid bg-dark text-white py-3">
        <div class="container">
            <a href="./index.php" class="text-white text-decoration-none">
                <h1 class="my-0">TRC Ristoranti</h1>
            </a>
        </div>
    </header>
    <div class="container my-5 d-flex justify-content-center">
        <div class="card shadow-0" id="card">
            <div class="card-body">
                <h3 class="card-title text-danger mb-2"><?php echo $row["nome"]; ?></h3>
                <h6 class="card-subtitle mb-2 text-muted"><?php echo $row["descrizione"]; ?></h6>
                <p class="card-text mb-2">
                    <strong>Telefono:</strong> <?php echo $row["telefono"]; ?><br>
                <div class="table-responsive">    
                <table class="table table-bordered">
                        <?php
                            $sql = $conn->prepare("SELECT * FROM orari WHERE idRistorante = ?");
                            $sql->bind_param("i", $_GET['id']);
                            $sql->execute();

                            $result = $sql->get_result();

                            $giorni = array("Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato", "Domenica");
                            
                            $i = 0;
                            while ($r = $result->fetch_assoc()) {
                                $r["giornoSettimana"] = $giorni[$r["giornoSettimana"] - 1];
                                $orari[$r["giornoSettimana"]][] = $r["oraApertura"] . " - " . $r["oraChiusura"];
                            }

                            echo "<tr>";
                            foreach ($orari as $giorno => $orariGiorno) {
                                echo "<th>" . $giorno . "</th>";
                            }
                            echo "</tr>";

                            echo "<tr>";
                            foreach ($orari as $giorno => $orariGiorno) {
                                echo "<td>";
                                foreach ($orariGiorno as $orario) {
                                    echo $orario;
                                    if ($orario != end($orariGiorno)) {
                                        echo "<hr>";
                                    }
                                }
                                echo "</td>";
                            }
                            echo "</tr>";

                        ?>
                    </table>
                    </div>
                    <strong>Indirizzo:</strong> <?php echo $row["via"] . " " . $row["civico"] . ", " . $row["cap"] . " " . $row["citta"]; ?>
                    <iframe class="mt-3" width="100%" height="400" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?= $row["coordinateX"] ?>,<?= $row["coordinateY"] ?>&hl=it&z=14&amp;output=embed"></iframe>
                </p>
                
                <div class="d-flex justify-content-md-between justify-content-center flex-wrap">
                    <button class="btn btn-outline-danger mb-md-0 mb-3" type="button" onclick="window.location.href='<?= $_GET['url'] ?>'">Torna indietro</button>
                    <?php if(isset($_SESSION['id'])) : ?>
                    <input type="submit" class="btn btn-danger" name="prenota" value="Effettua una prenotazione" onclick="window.location.href='./prenotazione.php?idRistorante=<?= $_GET['id'] ?>'"></input>
                    <?php else : ?>
                            <a href="../index.php"><button type="button" class="btn btn-outline-danger">Accedi per prenotare</button></a>
                    <?php endif;?>
                </div>
                            
            </div>
        </div>
    </div>
</body>

</html>