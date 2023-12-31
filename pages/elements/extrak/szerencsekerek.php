<?php

require_once "../../../backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../../../index.php");


$username = $_SESSION["username"];

$adatokQuery = "SELECT * FROM users WHERE `username` = ?";
$adatokPrepare = mysqli_prepare($connection, $adatokQuery);
mysqli_stmt_bind_param($adatokPrepare, "s", $username);
mysqli_stmt_execute($adatokPrepare);
$result = mysqli_stmt_get_result($adatokPrepare);
$details = mysqli_fetch_assoc($result);
$rows = mysqli_num_rows($result);

$banyaszerme = $details["banyaszerme"];
$forint = $details["forint"];
$korona = $details["korona"];

$extrakQuery = "SELECT * FROM extrak WHERE `username` = ?";
$extrakQueryPrepare = mysqli_prepare($connection, $extrakQuery);
mysqli_stmt_bind_param($extrakQueryPrepare, "s", $username);
mysqli_stmt_execute($extrakQueryPrepare);

$resultsExtrak = mysqli_stmt_get_result($extrakQueryPrepare);
$detailsExtrak = mysqli_fetch_assoc($resultsExtrak);

$udvozloCheck = $detailsExtrak["nevelo_aktivalva"];
$currentMode = "Névelő";
$currentSqlMode = "nevelo_aktivalva";

$extrakAraQuery = "SELECT * FROM extrak_ara";
$extrakAraQueryPrepare = mysqli_prepare($connection, $extrakAraQuery);
mysqli_stmt_execute($extrakAraQueryPrepare);

$resultsAraExtrak = mysqli_stmt_get_result($extrakAraQueryPrepare);
$detailsAraExtrak = mysqli_fetch_assoc($resultsAraExtrak);

$udvozloErtekBe = $detailsAraExtrak["nevelo_be"];
$udvozloErtekForint = $detailsAraExtrak["nevelo_ft"];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentMode); ?></title>
    <link rel="stylesheet" href="../../../assets/css/pages/elements/extrak/template.css">
</head>
<body>
    <div class="container">
        <!-- nincs neki aktiválva -->
        <div class="szerencse-kerek">
            <p>Nem fogok neked <span>pörgő kereket</span> csinálni, ne legyél <span>gambling</span> függő.</p>
            <a href="../../kellekek.php" class="vissza">Vissza</a>
        </div>
    </div>
</body>
</html>