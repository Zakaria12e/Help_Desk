<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Inclure Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .gradient-title {
            background-image: linear-gradient(to right, #FA8BFF,#2BD2FF,#2BFF88);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
       
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="container">
        <div class="card mx-auto p-4 custom-form">
            <div class="card-body">
            <h5 class="card-title text-left mb-4">Help Desk</h5>
                <?php
session_start();
include("config.php");

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $usertype = $_POST['usertype'];

    $result = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'") or die(mysqli_error($con));
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $hashpass = $row['Password'];

        if (password_verify($password, $hashpass)) {
            $_SESSION['valid'] = $row['Email'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['id'] = $row['Id'];
            $_SESSION['usertype'] = $usertype;

            if ($_SESSION['usertype'] === 'admin') {
                header("Location: desk.php");
            } else {
                header("Location: home.php");
            }
        } else {
            echo "<div class='alert alert-danger'>
                    <p>Identifiant ou mot de passe incorrect</p>
                  </div>";
            echo "<a href='login.php' class='btn btn-primary'>Retour</a>";
        }
    } else {
        echo "<div class='alert alert-danger'>
                <p>Identifiant ou mot de passe incorrect</p>
              </div>";
        echo "<a href='login.php' class='btn btn-primary'>Retour</a>";
    }
}
?>
               <h1 class="card-title text-center mb-4 gradient-title">CONNEXION</h1>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" id="email" class="form-control" autocomplete="off" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="off" required>
                    </div>

                    <div class="mb-3">
                        <label for="usertype" class="form-label">Type d'utilisateur</label>
                        <select name="usertype" id="usertype" class="form-select">
                            <option value="user">Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>

                    <div class="mb-3" id="verification-code" style="display: none;">
                        <label for="verification" class="form-label">Code de vérification (Admin uniquement)</label>
                        <input type="text" name="verification" id="verification" class="form-control" autocomplete="off">
                    </div>

                    <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary btn-lg" name="submit">connexion</button>

                    </div>
                    <div class="text-center">
                    Pas encore de compte ? <a href="register.php">Inscrivez-vous</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Écoutez les changements dans le menu déroulant "Type d'utilisateur"
        document.getElementById('usertype').addEventListener('change', function() {
            // Récupérez la valeur sélectionnée dans le menu déroulant
            var usertype = this.value;

            // Sélectionnez l'élément div du code de vérification
            var verificationDiv = document.getElementById('verification-code');

            // Si l'utilisateur est un administrateur, affichez le code de vérification
            if (usertype === 'admin') {
                verificationDiv.style.display = 'block';
            } else {
                // Sinon, masquez-le
                verificationDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
