<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "zakariadb";

$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
    die("Échec de la connexion à la base de données : " . mysqli_connect_error());
}
?>