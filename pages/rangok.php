<?php

require_once "../backend/db_con.php";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../index.php");

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
$rangexpire = $details["rang_expire"];

$currentDate = date("Y-m-d");
$daysLeft = "";
$dateDiff = "";

if($rangexpire != "Örök")
{
    $dateDiff = strtotime($rangexpire) - strtotime($currentDate);
    $daysLeft = floor($dateDiff / (60 * 60 * 24));
}

if($daysLeft == 0)
{
    $updateRankSql = "UPDATE users SET rang = ?, rang_expire = NULL WHERE username = ?";
    $updateRankPrepare = mysqli_prepare($connection, $updateRankSql);
    $newRank = "Default";
    mysqli_stmt_bind_param($updateRankPrepare, "ss", $newRank, $username);
    mysqli_stmt_execute($updateRankPrepare);
}

$kepPath = "../assets/media/pictures/" . $username . "_pfp.png";

if(file_exists($kepPath))
    $imgUrl = $kepPath;
else
    $imgUrl = "../assets/media/pictures/default.png";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bányaközpont</title>
    <link rel="stylesheet" href="../assets/css/pages/main.css">
    <script src="https://kit.fontawesome.com/7159432989.js" crossorigin="anonymous"></script>
</head>
<body>
    <nav>
        <a href="main.php" class="title"><span>MesterMC</span> Bányaközpont</a>
        <ul>
            <div class="currency">
            <li><a href="./vasarlas.php" class="forint"><?php echo htmlspecialchars($forint) ?> FT</a></li>
                <li><a href="./vasarlas.php"><?php echo htmlspecialchars($banyaszerme) ?> BÉ</a></li>
                <li><a href="./vasarlas.php"><?php echo htmlspecialchars($korona) ?> Korona</a></li>
                <li><a class="rank-display" href="rangok.php"><?php echo htmlspecialchars($rang) ?></a></li>
            </div>
            <div class="logout">
                <a href="./etc/kijelentkezes.php"><img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="">Kilépés</a>
            </div>
            <input type="checkbox" id="check">
            <label for="check" class="checkbtn">
                <i class="fa-solid fa-bars"></i>
            </label>
        </ul>
    </nav>
    <div id="ranks-container" class="container">
    <div class="sidebar">
            <a class="rank-display-mobile" href="rangok.php"><?php echo htmlspecialchars($rang) ?></a>
            <a class="first-child" href="rangok.php">Rangok</a>
            <a href="vasarlas.php">Vásárlás</a>
            <a href="aldasok.php">Láda/Áldások</a>
            <a href="korona.php">Korona</a>
            <a href="piac.php">Piac</a>
            <a href="kellekek.php">Kellékek/Extrák</a>
            <a href="beallitasok.php">Beállítások</a>
            <a class="last-child" href="segitseg.php">Segítség</a>
            <a href="#" class="logout-phone"><img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="">Kilépés</a>
        </div>
        <div class="ranks">
            <?php 
                if($rangexpire != NULL && $rangexpire != "Örök"):
            ?>
                <p class="rank-expires-in">A <span>rangod</span> lejár: <span><?php echo htmlspecialchars($daysLeft); ?></span> nap múlva.</p>
            <?php
                elseif($rangexpire != NULL && $rangexpire == "Örök"):
            ?>
                <p class="rank-expires-in">A <span>rangod</span> lejár: <span>örökre</span> vetted a rangodat.</p>
            <?php endif; ?>
            <div class="display-ranks">
                <a href="./elements/rangvasarlas.php?key=1"><img src="../assets/media/ranks/vip.png"></a>
                <a href="./elements/rangvasarlas.php?key=2"><img src="../assets/media/ranks/elit.png"></a>
                <a href="./elements/rangvasarlas.php?key=3"><img src="../assets/media/ranks/zsk.png"></a>
                <a href="./elements/rangvasarlas.php?key=4"><img src="../assets/media/ranks/titan.png"></a>
                <a href="./elements/rangvasarlas.php?key=5"><img src="../assets/media/ranks/felisten.png"></a>
                <a href="./elements/rangvasarlas.php?key=6" class="small-img"><img src="../assets/media/ranks/mindenhato.png"></a>
                <a href="./elements/rangvasarlas.php?key=7"><img src="../assets/media/ranks/troll.png"></a>
                <a href="./elements/rangvasarlas.php?key=8"><img src="../assets/media/ranks/trollplusz.png"></a>
                <a href="./elements/rangvasarlas.php?key=9" class="small-img"><img src="../assets/media/ranks/mindenhatoplusz.png"></a>
                <a href="./elements/rangvasarlas.php?key=13"><img src="../assets/media/ranks/jedi.png"></a>
                <a href="./elements/rangvasarlas.php?key=12"><img src="../assets/media/ranks/sith.png"></a>
                <a href="./elements/rangvasarlas.php?key=10" class="small-img"><img src="../assets/media/ranks/mutans.png"></a>
                <a href="./elements/rangvasarlas.php?key=11" class="small-img"><img src="../assets/media/ranks/bosszuallo.png"></a>
            </div>
            <div class="hosszabbitas">
                <a href="./elements/hosszabbitas/egy_honap.php">1 Hónapos hosszabbítás</a>
                <a href="./elements/hosszabbitas/orokre.php">Örökre való hosszabbítás</a>
            </div>
            <div class="rang-info">
                <p>Azért nincsenek <span>"középen"</span> a képek mert mindegyik mérete más mert a gecis sárkányokat úgy kellett megoldani.</p>
                <p>Ha <span>rangot cserélsz</span>, akkor <span>1 hónapot kapsz csak</span>, ez nyílván való, <span>tök mindegy</span> hogy a mostani rangod örökre van-e.</p>
            </div>
        </div>
    </div>
</body>
<script>
            document.getElementById('check').addEventListener('click', function() {
            var sidebar = document.querySelector('.container .sidebar');
            sidebar.style.left = sidebar.style.left === '0%' ? '-100%' : '0%';
        });
</script>
</html>