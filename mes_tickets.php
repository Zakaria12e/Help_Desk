<?php
session_start();
require_once("config.php");

$id = $_SESSION['id'];
$confirmationMessage = "";

if (isset($_SESSION['confirmationMessage'])) {
    $confirmationMessage = $_SESSION['confirmationMessage'];
    unset($_SESSION['confirmationMessage']);
}
$tickets = [];
$query = mysqli_query($con, "SELECT * FROM tickets WHERE id = $id");

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $tickets[] = $row;
    }
} else {
    $errorMessage = "Erreur lors de la récupération des tickets.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mes Tickets - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .bg-en-attente {
            background-color: rgb(241, 48, 48);
            color: black;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .bg-en-cours {
            background-color: #f7ff00;
            color: black;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .bg-termine {
            background-color: rgb(37, 166, 108);
            color: black;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 5px;
         
        }
        .status {
    vertical-align: middle;
        }
        .table-container {
            text-align: center;
        }
      

        th {
            vertical-align: middle;
            text-align: center;
        }
        .nav-item-bordered {
            border: 1px solid #ccc;
            border-radius: 10px;
            margin: 0 4px;
            padding: 1px 3px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }
        .nav-item-bordered:hover {
            background-image: linear-gradient(to right,#2c3e50, #4ca1af);
        }
        .bg-silver {
            background-color: rgb(240, 240, 240);
        }
        .table-bordered td {
    border: 1px solid #ccc;
    padding: 5px 10px;
}
.date{
    width: 170px;
}
.desk:hover{
            background-image: linear-gradient(to right,#2c3e50, #4ca1af);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;

        }
        .hor{
            vertical-align: middle;
             text-align: center
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-silver">
    <div class="container">
        <a class="navbar-brand desk" href="home.php"><h4>Help Desk</h4></a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item nav-item-bordered"><a class="nav-link" href="home.php"><b>Accueil</b></a></li>
            <?php 
            if (isset($_SESSION['valid'])) {
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='edit.php?Id=$id'><b>Modifier le Profil</b></a></li>";
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='logout.php'><b>Déconnexion</b></a></li>";
            }
            ?>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <a href="mes_tickets.php" style="text-decoration: none; color:black"> <h1 class="mb-4">Mes Tickets</h1></a>

    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <?php if (!empty($confirmationMessage)) : ?>
        <div class="alert alert-success"><?php echo $confirmationMessage; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-rounded table-bordered " >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th style="width: 200px;">Service</th>
                    <th style="width: 200px;">Sujet</th>
                    <th class="description-column" style="width: 900px;">Description</th>
                    <th style="width: 200px;">Status</th>
                    <th class="date"style="width: 400px;">Date de création</th>
                    <th class="solution-column" style="width: 900px;">Solution</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket) : ?>
                    <tr>
                        <td class="text-center hor"><?php echo $ticket['ticket_id']; ?></td>
                        <td class="text-center hor"><?php echo $ticket['name']; ?></td>
                        <td class="text-center hor"><?php echo $ticket['email']; ?></td>
                        <td class="text-center hor"><?php echo $ticket['service']; ?></td>
                        <td class="text-center hor"><?php echo $ticket['subject']; ?></td>
                        <td>
                            <?php
                            $description = $ticket['description'];
                            $descriptionWords = explode(' ', $description);
                            $descriptionChunks = array_chunk($descriptionWords, 5);

                            foreach ($descriptionChunks as $chunk) {
                                echo implode(' ', $chunk) . '<br>';
                            }
                            ?>
                        </td>
                        <td class="text-center status">
                            <?php
                            $status = $ticket['status'];
                            $statusClass = '';

                            switch ($status) {
                                case 'En attente':
                                    $statusClass = 'bg-en-attente';
                                    break;
                                case 'En cours':
                                    $statusClass = 'bg-en-cours';
                                    break;
                                case 'Terminé':
                                    $statusClass = 'bg-termine';
                                    break;
                                default:
                                    $statusClass = '';
                                    break;
                            }

                            echo '<span class="' . $statusClass . '">' . $status . '</span>';
                            ?>
                        </td>
                        <td class="text-center hor "><?php echo $ticket['created_at']; ?></td>
                        <td>
                            <?php
                            $solution = $ticket['solution'];
                            $descriptionWords = explode(' ', $solution);
                            $descriptionChunks = array_chunk($descriptionWords, 7);

                            foreach ($descriptionChunks as $chunk) {
                                echo implode(' ', $chunk) . '<br>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
