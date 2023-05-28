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
        @media (max-width: 767px) {
            .d-flex.flex-column {
                flex-direction: column;
            }

            .card {
                width: 90% !important;
            }

            .d-flex.justify-content-between.flex-wrap {
                flex-direction: column;
                align-items: center;
            }

            .d-flex.justify-content-between.flex-wrap>div {
                margin-bottom: 10px;
            }
        }

        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active
        {
            color: var(--mdb-danger) !important;
            border-color: var(--mdb-danger-rgb) !important;
        }
    </style>
</head>

<body class="bg-trasparent">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh;">
        <div>
            <img src="./favicon.ico" alt="Image" style="width: 100px; height: 100px;">
        </div>
        <div class="card shadow-5 mx-auto my-5 border rounded" style="width: 30rem;">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="true" href="#" id="btn_accedi">Accedi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="btn_registrati">Registrati</a>
                    </li>
                </ul>
            </div>
            <div id="login" class="card-body">
                <h2 class="fw-bold mb-5 text-center text-danger">Login</h2>
                <form action="login.php" method="post">
                    <div class="mb-3 form-outline">
                        <input type="text" name="email" id="email" class="form-control" required>
                        <label for="email" class="form-label">Email</label>
                    </div>
                    <div class="mb-3 form-outline">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <label for="password" class="form-label">Password</label>
                    </div>

                    <div class="mb-3 d-flex justify-content-between flex-wrap">
                        <div class="text-center">
                            <input type="submit" value="Accedi" class="btn btn-danger">
                        </div>
                        <div class="text-center">
                            <a href="./home" class=""><button type="button" class="btn btn-outline-danger">Entra come ospite</button></a>
                        </div>
                    </div>

                </form>
            </div>


            <div id="register" class="card-body d-none">
                <h2 class="fw-bold mb-5 text-center text-danger">Registrati</h2>
                <form action="register.php" method="post">
                    <div class="mb-3 form-outline">
                        <input type="text" name="nome" id="nome" class="form-control" required>
                        <label for="nome" class="form-label">Nome</label>

                    </div>
                    <div class="mb-3 form-outline">
                        <input type="text" name="cognome" id="cognome" class="form-control" required>
                        <label for="cognome" class="form-label">Cognome</label>
                    </div>
                    <div class="mb-3 form-outline">
                        <input type="text" name="email" id="email_reg" class="form-control" required>
                        <label for="email" class="form-label">Email</label>
                    </div>
                    <div class="mb-3 form-outline">
                        <input type="password" name="password" id="password_reg" class="form-control" required>
                        <label for="password" class="form-label">Password</label>

                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Registrati" class="btn btn-danger w-100">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.form-outline').forEach((formOutline) => {
            new mdb.Input(formOutline).init();
        });
        document.querySelector(".nav-tabs").addEventListener("click", function(e) {
            if (e.target.id == "btn_accedi") {
                document.getElementById("login").classList.remove("d-none");
                document.getElementById("register").classList.add("d-none");

                document.getElementById("btn_accedi").classList.add("active");
                document.getElementById("btn_registrati").classList.remove("active");
            } else if (e.target.id == "btn_registrati") {
                document.getElementById("login").classList.add("d-none");
                document.getElementById("register").classList.remove("d-none");

                document.getElementById("btn_accedi").classList.remove("active");
                document.getElementById("btn_registrati").classList.add("active");
            }
        });
    </script>

</body>

</html>