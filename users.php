<?php
session_start();
require_once("config.php");

// Vérifiez si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['valid']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Traitement de la suppression d'utilisateur
if (isset($_GET['delete']) && isset($_GET['user_id'])) {
    $userIdToDelete = $_GET['user_id'];

    // Vérifiez si l'utilisateur tente de supprimer son propre compte
    if ($_SESSION['id'] == $userIdToDelete) {
        $_SESSION['errorMessage'] = "Vous ne pouvez pas supprimer votre propre compte.";
        header("Location: users.php");
        exit;
    }

    // Supprimer d'abord les tickets associés dans la table 'tickets'
    $deleteTicketsQuery = "DELETE FROM tickets WHERE id = $userIdToDelete";
    if (mysqli_query($con, $deleteTicketsQuery)) {
        // Ensuite, supprimez l'utilisateur de la table 'users'
        $deleteUserQuery = "DELETE FROM users WHERE Id = $userIdToDelete";
        if (mysqli_query($con, $deleteUserQuery)) {
            $_SESSION['confirmationMessage'] = "L'utilisateur a été supprimé avec succès.";
        } else {
            $errorMessage = "Erreur lors de la suppression de l'utilisateur : " . mysqli_error($con);
        }
    } else {
        $errorMessage = "Erreur lors de la suppression des tickets associés : " . mysqli_error($con);
    }
}

// Récupérez la liste des utilisateurs depuis la base de données
$query = mysqli_query($con, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Style CSS personnalisé pour les boutons "Supprimer" */
        .delete-button {
            background-color: #ff5555;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .action-column {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .delete-button:hover {
            background-color: #ff0000;
        }
        .bg-silver {
            background-color: rgb(240, 240, 240);
        }
        th {
            text-align: center;
        }
        .nav-item-bordered {
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 0 4px;
        padding: 1px 3px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4)
      
    }
    .nav-item-bordered:hover{
        background-image: linear-gradient(to right,#2c3e50, #4ca1af);
    }
    .desk:hover{
            background-image: linear-gradient(to right,#2c3e50, #4ca1af);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;

        }
    footer {
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
    <title>Gestion des Utilisateurs - Help Desk</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-silver">
    <div class="container">
        <a class="navbar-brand desk" href="desk.php"><h4>Help Desk</h4></a>
        <ul class="navbar-nav ml-auto">
            <?php 
            if (isset($_SESSION['valid'])) {
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='desk.php'><b>Accueil</b></a></li>";
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='edit_admin.php'><b>Modifier le Profil</b></a></li>";
                echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='logout.php'><b>Déconnexion</b></a></li>";
            }
            ?>
        </ul>
    </div>
</nav>
<div class="container">
    <header>
        <h1 class="text-center">Gestion des Utilisateurs</h1>
    </header>

    <div class="row justify-content-center">
        <div class="col-md-8">
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
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($query) && $query && mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo "<tr>";
                            echo "<td>" . $row['Id'] . "</td>";
                            echo "<td>" . $row['Username'] . "</td>";
                            echo "<td>" . $row['Email'] . "</td>";
                            // Empêchez l'administrateur connecté de supprimer son propre compte
                            if ($_SESSION['id'] != $row['Id']) {
                                echo '<td class="action-column">
                                        <a href="users.php?delete=true&user_id=' . $row['Id'] . '" class="delete-button">Supprimer</a>
                                    </td>';
                            } else {
                                echo '<td class="action-column"><b>Impossible de supprimer</b></td>';
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Aucun utilisateur trouvé.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<footer class="bg-primary text-white text-center py-2">
        &copy; 2023 Help Desk
    </footer>
</body>
</html>
