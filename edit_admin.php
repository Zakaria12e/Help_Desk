<?php 
session_start();
include("config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
}

$id = $_SESSION['id'];
$username = ""; 
$email = "";
$confirmationMessage = "";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Mettez à jour le profil de l'utilisateur
    $updateQuery = "UPDATE users SET Username='$username', Email='$email' WHERE Id=$id";
    $result = mysqli_query($con, $updateQuery);

    if ($result) {
        $confirmationMessage = "Profil mis à jour avec succès !";
    } else {
        $errorMessage = "Erreur lors de la mise à jour du profil.";
    }
} else {
    $selectQuery = "SELECT * FROM users WHERE Id=$id";
    $result = mysqli_query($con, $selectQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $username = $row['Username'];
        $email = $row['Email'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                        echo "<li class='nav-item nav-item-bordered'><a class='nav-link' href='logout.php'><b>Déconnexion</b></a></li>";
                    }
                ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($confirmationMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $confirmationMessage; ?>
                    </div>
                <?php endif; ?>

                <h2 class="mb-4 text-center">Modification du Profil Admin</h2>
                <form action="edit_admin.php" method="post">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" required>
                    </div>

                    <div class="form-group text-center">
                        <input type="submit" class="btn btn-primary mx-auto" name="submit" value="Mettre à jour">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <footer class="bg-primary text-white text-center py-2">
        &copy; 2023 Help Desk
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqFjcJ6pajs/rfdfs3SO+kCG5L4M5e18nC5MQn5f5F5v5F5" crossorigin="anonymous"></script>
</body>
</html>
