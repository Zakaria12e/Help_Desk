<?php
session_start();
require_once("config.php");

$id = "";
$description = "";
$name = "";
$subject = "";
$email = "";
$confirmationMessage = "";

if (isset($_POST['submit'])) {
    // Récupérer les données du formulaire soumis
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $status = "En attente"; // Statut par défaut pour les nouveaux tickets

    // Insérer le nouveau ticket dans la base de données
    $insertQuery = "INSERT INTO tickets (id, name, email, subject, description, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insertQuery);
    mysqli_stmt_bind_param($stmt, "isssss", $_SESSION['id'], $name, $email, $subject, $description, $status);

    if (mysqli_stmt_execute($stmt)) {
        $confirmationMessage = "Le ticket a été soumis avec succès.";
    } else {
        $errorMessage = "Erreur lors de la création du ticket.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        footer {
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .bg-silver {
    background-color: rgb(240, 240, 240);
}
.nav-item-bordered {
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 0 2px;
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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-silver">
        <div class="container">
        <a class="navbar-brand desk" href="home.php"><h4>Help Desk</h4></a>
            <ul class="navbar-nav ml-auto">
              
                  <li class="nav-item nav-item-bordered"><a class="nav-link" href="mes_tickets.php"><b>Mes Tickets</b></a></li>
                    <?php 
                    // Afficher le lien "Modifier le Profil" si l'utilisateur est connecté
                    if (isset($_SESSION['valid'])) {
                        echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='edit.php?Id=$id'><b>Modifier le Profil</b></a></li>";
                        echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='logout.php'><b>Déconnexion</b></a></li>";
                    }
                ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Contact Support</h1>
        <?php 
            if (!empty($message)) {
                echo '<div class="alert alert-info">' . $message . '</div>';
            }
        ?>
        <!-- Afficher le message de confirmation s'il est défini -->
        <?php if (!empty($confirmationMessage)) : ?>
            <div class="alert alert-success"><?php echo $confirmationMessage; ?></div>
        <?php endif; ?>
        <form action="home.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" name="name" id="name"  autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" id="email" autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Sujet</label>
                <select class="form-select" name="subject" id="subject" required>
                    <option value="Problème d'accès Internet" <?php if ($subject === "Problème d'accès Internet") echo "selected"; ?>>Problème d'accès Internet</option>
                    <option value="Problème de matériel" <?php if ($subject === "Problème de matériel") echo "selected"; ?>>Problème de matériel</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description du Problème</label>
                <textarea class="form-control" name="description" id="description" rows="5" required><?php echo $description; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Soumettre</button>
        </form>
    </div>

    <!-- Inclure les fichiers JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
<footer class="bg-primary text-white text-center py-2">
        &copy; 2023 Help Desk
    </footer>
</html>
