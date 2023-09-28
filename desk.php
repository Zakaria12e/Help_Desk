<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['valid']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['submit'])) {
    $ticketId = $_POST['ticket_id'];
    $newStatus = mysqli_real_escape_string($con, $_POST['new_status']);
    $newSolution = mysqli_real_escape_string($con, $_POST['solution']);

    $updateQuery = "UPDATE tickets SET status = '$newStatus', solution = '$newSolution' WHERE ticket_id = $ticketId";

    if (mysqli_query($con, $updateQuery)) {
        $_SESSION['confirmationMessage'] = "Le statut et la solution du ticket ont été mis à jour avec succès.";
        header("Location: desk.php");
        exit;
    } else {
        $errorMessage = "Erreur lors de la mise à jour du statut et de la solution du ticket : " . mysqli_error($con);
    }
}

if (isset($_POST['delete'])) {
    $ticketIdToDelete = $_POST['ticket_id'];

    $deleteQuery = "DELETE FROM tickets WHERE ticket_id = $ticketIdToDelete";
    if (mysqli_query($con, $deleteQuery)) {
        $_SESSION['confirmationMessage'] = "Le ticket a été supprimé avec succès.";
    } else {
        $errorMessage = "Erreur lors de la suppression du ticket : " . mysqli_error($con);
    }
}

// Fonction pour diviser une chaîne en morceaux de longueur donnée
function chunkString($str, $length) {
    return str_split($str, $length);
}

$query = mysqli_query($con, "SELECT * FROM tickets");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Administration - Help Desk</title>
    <style>
        .bg-silver {
            background-color: rgb(240, 240, 240);
        }

        .nav-item-bordered {
            border: 1px solid #ccc;
            border-radius: 10px;
            margin: 0 4px;
            padding: 1px 3px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4)
        }

        .nav-item-bordered:hover {
            background-image: linear-gradient(to right,#2c3e50, #4ca1af);
        }
        .desk:hover{
            background-image: linear-gradient(to right,#2c3e50, #4ca1af);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;

        }
        .date,.subject{
    width: 170px;
}
.description{
    width: 370px;
}
.table-bordered td {
    border: 1px solid #ccc;
    padding: 5px 10px;
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
        <a class="navbar-brand desk" href="desk.php"><h4>Help Desk</h4></a>
        <ul class="navbar-nav ml-auto">
            <?php
            if (isset($_SESSION['valid'])) {
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='desk.php'><b>Accueil</b></a></li>";
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='users.php'><b>Gestion des utilisateurs</b></a></li>";
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='edit_admin.php'><b>Modifier le Profil</b></a></li>";
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='logout.php'><b>Déconnexion</b></a></li>";
            }
            ?>
        </ul>
    </div>
</nav>

<header class="container mt-4">
    <h1 class="mb-4">Tableau de bord de l'administration</h1>
</header>

<div class="container mt-2">
    <?php if (isset($errorMessage)) : ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['confirmationMessage'])) : ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['confirmationMessage'];
            unset($_SESSION['confirmationMessage']); ?>
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="text-center">Nom</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Sujet</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Date de création</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($query) && $query) {
                    while ($row = mysqli_fetch_assoc($query)) {
                        $statusClass = '';

                        switch ($row['status']) {
                            case 'En attente':
                                $statusClass = 'bg-danger text-white';
                                break;
                            case 'En cours':
                                $statusClass = 'bg-warning';
                                break;
                            case 'Terminé':
                                $statusClass = 'bg-success text-white';
                                break;
                            default:
                                $statusClass = '';
                                break;
                        }

                        $description = $row['description'];
                        $descriptionWords = explode(' ', $description);
                        $descriptionChunks = array_chunk($descriptionWords, 5);

                        echo "<tr class='last-ticket hor'>";
                        echo "<td>" . $row['ticket_id'] . "</td>";
                        echo "<td class='text-center hor'>" . $row['name'] . "</td>";
                        echo "<td class='text-center hor'>" . $row['email'] . "</td>";
                        echo "<td class='text-center hor subject'>" . $row['subject'] . "</td>";
                        echo '<td class="text-center hor description">';
                        foreach ($descriptionChunks as $chunk) {
                            echo implode(' ', $chunk) . '<br>';
                        }
                        echo '</td>';
                        echo "<td class='text-center date hor'>" . $row['created_at'] . "</td>";
                        echo "<td class='text-center $statusClass'>" . $row['status'] . "</td>";
                        echo '<td class="text-center">
                                <form action="desk.php" method="post">
                                    <input type="hidden" name="ticket_id" value="' . $row['ticket_id'] . '">
                                    <select name="new_status" class="form-select">
                                        <option value="En attente" ' . ($row['status'] == 'En attente' ? 'selected' : '') . '>En attente</option>
                                        <option value="En cours" ' . ($row['status'] == 'En cours' ? 'selected' : '') . '>En cours</option>
                                        <option value="Terminé" ' . ($row['status'] == 'Terminé' ? 'selected' : '') . '>Terminé</option>
                                    </select>
                                    <textarea name="solution" class="form-control" placeholder="Saisissez la solution du problème">' . htmlspecialchars($row['solution']) . '</textarea>
                                    <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 14px; margin: 10px;" name="submit">Mettre à jour</button>
                                    <button type="submit" class="btn btn-danger" name="delete" style="padding: 6px 20px; font-size: 14px; margin: 5px;">Supprimer</button>
                                </form>
                            </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Aucun ticket trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>