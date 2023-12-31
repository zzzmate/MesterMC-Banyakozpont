<?php

require_once "../../../backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../../index.php");


$username = $_SESSION["username"];

$adatokQuery = "SELECT * FROM users where `username` = ?";
$adatokPrepare = mysqli_prepare($connection, $adatokQuery);
mysqli_stmt_bind_param($adatokPrepare, "s", $username);
mysqli_stmt_execute($adatokPrepare);
$result = mysqli_stmt_get_result($adatokPrepare);
$details = mysqli_fetch_assoc($result);
$rows = mysqli_num_rows($result);

$banyaszerme = $details["banyaszerme"];
$forint = $details["forint"];
$korona = $details["korona"];
$rang = $details["rang"];
$rangExpire = $details["rang_expire"];

$checkRankPrice = "SELECT * FROM rangok WHERE `rang` = ?";
$checkRankPricePrepare = mysqli_prepare($connection, $checkRankPrice);
mysqli_stmt_bind_param($checkRankPricePrepare, "s", $rang);
mysqli_stmt_execute($checkRankPricePrepare);
$resultsRank = mysqli_stmt_get_result($checkRankPricePrepare);
$detailsRank = mysqli_fetch_assoc($resultsRank);

$userRankPriceBe = $detailsRank["be_ertek_havonta"];
$userRankPriceForint = $detailsRank["forint_ertek_havonta"];


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    //forinttal akarja megvenni #rich
    if(isset($_POST["forint-buy"])) {
        if($userRankPriceForint === 0) return header("Location: egy_honap.php?errorCode=Ezt nem aktiválhatod forintból.");
        if($userRankPriceForint > $forint) return header("Location: egy_honap.php?errorCode=Neked nincs elég forintod erre.");
        if($rangExpire == "Örök") return header("Location: egy_honap.php?errorCode=A te rangodat már örökre megvásároltad.");
        // van forintja, nem örök a rangja akkor vegye meg a rangot még 1 hónapra

        $rangExpireDate = new DateTime($rangExpire);
        $rangExpireDate->add(new DateInterval('P31D'));
        $resultTime = $rangExpireDate->format("Y-m-d");

        $updateRankSql = "UPDATE users SET forint = forint - ?, rang_expire = ? WHERE username = ?";
        $updateRankSqlPrepare = mysqli_prepare($connection, $updateRankSql);
        mysqli_stmt_bind_param($updateRankSqlPrepare, "sss", $userRankPriceForint, $resultTime, $username);
        mysqli_stmt_execute($updateRankSqlPrepare);

        header("Location: egy_honap.php?successCode=Sikeresen meghosszabbítottad a rangodat még egy hónapra.");
        exit();
    }

    //bevel akarja megvenni #nerd
    if(isset($_POST["banyaszerme-buy"])) {
        if($userRankPriceBe === 0) return header("Location: egy_honap.php?errorCode=Ezt nem aktiválhatod bányászérméből.");
        if($userRankPriceBe > $banyaszerme) return header("Location: egy_honap.php?errorCode=Neked nincs elég bányászérméd erre.");
        if($rangExpire == "Örök") return header("Location: egy_honap.php?errorCode=A te rangodat már örökre megvásároltad.");
        
        // van béje, nem örök a rangja akkor vegye meg a rangot még 1 hónapra

        $rangExpireDate = new DateTime($rangExpire);
        $rangExpireDate->add(new DateInterval('P31D'));
        $resultTime = $rangExpireDate->format("Y-m-d");

        $updateRankSql = "UPDATE users SET banyaszerme = banyaszerme - ?, rang_expire = ? WHERE username = ?";
        $updateRankSqlPrepare = mysqli_prepare($connection, $updateRankSql);
        mysqli_stmt_bind_param($updateRankSqlPrepare, "sss", $userRankPriceBe, $resultTime, $username);
        mysqli_stmt_execute($updateRankSqlPrepare);
        header("Location: egy_honap.php?successCode=Sikeresen meghosszabbítottad a rangodat még egy hónapra.");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rang hosszabbítás</title>
    <link rel="stylesheet" href="../../../assets/css/pages/elements/hosszabbitas/template.css">
    <script src="https://kit.fontawesome.com/7159432989.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="elfelejtett-jelszo">
            <h2><span>MesterMC</span> Rang hosszabbítás</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <p>Jelenlegi rangod: <span><?php echo htmlspecialchars($rang); ?></span></p>
                <div class="price-rank">
                    <p><span><?php echo htmlspecialchars($userRankPriceBe); ?></span> Bé</p>
                    <p>vagy</p>
                    <p><span><?php echo htmlspecialchars($userRankPriceForint) ?></span> FT</p>
                </div>
                <div class="buttons">
                    <button type="submit" style="padding-inline: 40px !important;" name="forint-buy">Forint</button>
                    <button type="submit" name="banyaszerme-buy">Bányászérme</button>
                </div>
                <a href="../../rangok.php" class="vissza">Vissza</a>
            </form>
        </div>
        <div class="rank-infos">
            <?php if($errorCode != ""): ?>
                <p class="error"><?php echo $_GET["errorCode"] ?></p>
            <?php elseif($successCode != ""): ?>
                <p class="success"><?php echo $_GET["successCode"] ?></p>
            <?php else: ?>
                <p>Nem minden <span>rang képessége</span> használható minden szerveren, mert a játékélmény elrontása nem lehet senkinek sem a <span>célja</span>.</p>
            <p>Amennyiben nem vagy benne biztos, hogy egy <span>adott szerveren</span> egy képesség használható, <span>keress fel minket</span>!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>