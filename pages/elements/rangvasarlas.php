<?php

require_once "../../backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../../index.php");
if(!isset($_GET["key"])) return header("Location: ../rangok.php");

$username = $_SESSION["username"];
$currentRankID = trim($_GET["key"]);

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
$rangexpire = $details["rang_expire"];

$kepPath = "../assets/media/pictures/" . $username . "_pfp.png";

if(file_exists($kepPath))
    $imgUrl = $kepPath;
else
    $imgUrl = "../assets/media/pictures/default.png";

$checkRankPrice = "SELECT * FROM rangok WHERE `id` = ?";
$checkRankPricePrepare = mysqli_prepare($connection, $checkRankPrice);
mysqli_stmt_bind_param($checkRankPricePrepare, "s", $currentRankID);
mysqli_stmt_execute($checkRankPricePrepare);
$resultsRank = mysqli_stmt_get_result($checkRankPricePrepare);
$detailsRank = mysqli_fetch_assoc($resultsRank);
$rowsRank = mysqli_num_rows($resultsRank);

$currentRank = $detailsRank["rang"];
$currentLeiras = $detailsRank["leiras"];
$currentErtekForint = $detailsRank["forint_ertek"];
$currentErtekBe = $detailsRank["be_ertek"];

// nincs ilyen rang =(
if($rowsRank <= 0) return header("Location: ../rangok.php");

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // bé vásárlás
    if(isset($_POST["banyaszerme-buy"]))
    {
        if($currentErtekBe === 0) return header("Location: rangvasarlas.php?errorCode=Ezt nem tudod megvenni bányászérméért.&key=$currentRankID");
        if($currentErtekBe > $banyaszerme) return header("Location: rangvasarlas.php?errorCode=Nincs elég bányászérméd erre.&key=$currentRankID");
        if($currentRank == $rang) return header("Location: rangvasarlas.php?errorCode=Jelenleg ilyen rangod van.&key=$currentRankID");

        $rangExpireDate = new DateTime();
        $rangExpireDate->add(new DateInterval('P31D'));
        $resultTime = $rangExpireDate->format("Y-m-d");

        $updateRankQuery = "UPDATE users SET rang = ?, banyaszerme = banyaszerme - ?, rang_expire = ? WHERE username = ?";
        $updateRankPrepare = mysqli_prepare($connection, $updateRankQuery);
        mysqli_stmt_bind_param($updateRankPrepare, "ssss", $currentRank, $currentErtekBe, $resultTime, $username);
        mysqli_stmt_execute($updateRankPrepare);

        header("Location: rangvasarlas.php?successCode=Sikeresen megvásároltad a rangodat.&key=$currentRankID");
        exit();
    }

    // forint vásárlás
    if(isset($_POST["forint-buy"]))
    {
        if($currentErtekForint === 0) return header("Location: rangvasarlas.php?errorCode=Ezt nem tudod megvenni forintért.&key=$currentRankID");
        if($currentErtekForint > $forint) return header("Location: rangvasarlas.php?errorCode=Nincs elég forintod erre.&key=$currentRankID");
        if($currentRank == $rang) return header("Location: rangvasarlas.php?errorCode=Jelenleg ilyen rangod van.&key=$currentRankID");

        $rangExpireDate = new DateTime();
        $rangExpireDate->add(new DateInterval('P31D'));
        $resultTime = $rangExpireDate->format("Y-m-d");

        $updateRankQuery = "UPDATE users SET rang = ?, forint = forint - ?, rang_expire = ? WHERE username = ?";
        $updateRankPrepare = mysqli_prepare($connection, $updateRankQuery);
        mysqli_stmt_bind_param($updateRankPrepare, "ssss", $currentRank, $currentErtekForint, $resultTime, $username);
        mysqli_stmt_execute($updateRankPrepare);

        header("Location: rangvasarlas.php?successCode=Sikeresen megvásároltad a rangodat.&key=$currentRankID");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentRank; ?> vásárlása</title>
    <link rel="stylesheet" href="../../assets/css/pages/elements/rangvasarlas.css">
    <script src="https://kit.fontawesome.com/7159432989.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div id="rank-informations" class="rank-informations">
            <h2><?php echo $currentRank ?></h2>
            <div class="other-infos">
                <?php if($errorCode != ""): ?>
                    <p class="error"><?php echo $_GET["errorCode"] ?></p>
                <?php elseif($successCode != ""): ?>
                    <p class="success"><?php echo $_GET["successCode"] ?></p>
                <?php else: ?>
                    <p>Jelenleg készülsz megvenni a <span><?php echo htmlspecialchars($currentRank); ?></span> rangot. </p>
                <?php endif; ?>
                <?php if($rangexpire == "Örök"): ?>
                    <p><span>Figyelem!</span> A jelenlegi rangod örökre szól.<br>Ha ezt a rangot megveszed, <span>1 hónapig</span> lesz nálad csak.</p>
                <?php endif; ?>
            </div>
            <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?key=' . urlencode($currentRankID) ?>" method="POST">
                <div class="row">
                    <div class="column-1"><?php echo htmlspecialchars($currentLeiras); ?></div>
                    <div class="column-2">
                        <p class="payment-info"><span><?php echo htmlspecialchars($currentErtekBe); ?></span> Bé</p>
                        <p>vagy</p>
                        <p class="payment-info"><span><?php echo htmlspecialchars($currentErtekForint) ?></span> FT</p>
                        <div class="payment-choose">
                            <button type="submit" name="banyaszerme-buy">Bányászérme</button>
                            <button type="submit" name="forint-buy" style="padding-inline: 40px !important;">Forint</button>
                        </div>
                        <a href="../rangok.php" class="vissza">Vissza</a>
                    </div>
                    <div class="column-3"><?php echo htmlspecialchars($currentLeiras); ?></div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>