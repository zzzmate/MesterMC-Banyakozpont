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

$kepPath = "../assets/media/pictures/" . $username . "_pfp.png";

if(file_exists($kepPath))
    $imgUrl = $kepPath;
else
    $imgUrl = "../assets/media/pictures/default.png";

$extrakQuery = "SELECT * FROM extrak WHERE `username` = ?";
$extrakQueryPrepare = mysqli_prepare($connection, $extrakQuery);
mysqli_stmt_bind_param($extrakQueryPrepare, "s", $username);
mysqli_stmt_execute($extrakQueryPrepare);

$resultsExtrak = mysqli_stmt_get_result($extrakQueryPrepare);

// nem kell megkoszonni x
// egyebkent ez nem szukseges ami most jon ha van regisztracio, de az nem a banyakozpont resze originally ezert nem csinalom meg

if($row = mysqli_num_rows($resultsExtrak) <= 0)
{
    $insertExtrak = "INSERT INTO extrak (username, udvozlo_aktivalva, nevelo_aktivalva, chat_prefix_aktivalva, nev_utotag_aktivalva, ultra_szint_aktivalva) VALUES (?, 0, 0, 0, 0, 0)";
    $insertExtrakPrepare = mysqli_prepare($connection, $insertExtrak);
    mysqli_stmt_bind_param($insertExtrakPrepare, "s", $username);
    mysqli_stmt_execute($insertExtrakPrepare);
}

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
    <div class="container">
    <div class="sidebar">
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
        <div class="kellekek">
            <div class="items">
                <h2>Kellékek</h2>
                <div class="item-childs">
                    <a href="./elements/kellekek/skin.php">Skin<i class="fa-solid fa-person"></i></a>
                    <a href="./elements/kellekek/hd_skin.php">HD Skin<i class="fa-solid fa-shirt"></i></a>
                    <a href="./elements/kellekek/szarny.php">Szárny<i class="fa-solid fa-layer-group"></i></a>
                    <a href="./elements/kellekek/kalap.php">Kalap<i class="fa-solid fa-hat-wizard"></i></a>
                    <a href="./elements/kellekek/farok.php">Farok<i class="fa-brands fa-squarespace"></i></a>
                    <a href="./elements/kellekek/karkoto.php">Karkötő<i class="fa-solid fa-stethoscope"></i></a>
                </div>
            </div>
            <div class="items">
                <h2>Extrák</h2>
                <div class="item-childs">
                    <a href="./elements/extrak/udvozlouzenet.php">Üdvözlő üzenet<i class="fa-solid fa-message"></i></a>
                    <a href="./elements/extrak/nevelo.php">Névelő<i class="fa-solid fa-signature"></i></a>
                    <a href="./elements/extrak/szerencsekerek.php">Szerencsekerék<i class="fa-solid fa-arrows-spin"></i></a>
                    <a href="./elements/extrak/ultraszint.php">Ultra szint<i class="fa-solid fa-turn-up"></i></a>
                </div>
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