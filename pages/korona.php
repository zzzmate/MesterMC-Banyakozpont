<?php

require_once "../backend/db_con.php";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../index.php");

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

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

if($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // korona vásárlás
    if(isset($_POST["korona"])) 
    {
        $realKorona = $_POST["korona"];

        // szerver oldali input ellenőrzés, mert vannak kedves emberek akik exploitokat keresnek
        if(!ctype_digit($realKorona)) return header("Location: korona.php?errorCode=Kérlek érvényes számot írj be.");

        // csekkolni kell az értékét is, mert még mindig vannak okos emberek
        $realValue = $realKorona * 156;

        if($banyaszerme < $realValue) return header("Location: korona.php?errorCode=Nincs elég bányászérméd erre.");
    
        $buyKoronaQuery = "UPDATE users SET banyaszerme = ?, korona = ? WHERE username = ?";
        $buyKoronaPrepare = mysqli_prepare($connection, $buyKoronaQuery);

        // nem real time, hanem csak post time bányászérme, ha több dolgot rakunk erre az oldalra, akkor itt fusson le egy query
        // ami ellenőrzi azt, hogy mennyi bányászérméje van a srácnak, regards
        $afterBanyaszerme = $banyaszerme - $realValue;
        $afterKorona = $korona + $realKorona;

        mysqli_stmt_bind_param($buyKoronaPrepare, "sss", $afterBanyaszerme, $afterKorona, $username);
        mysqli_stmt_execute($buyKoronaPrepare);

        header("Location: korona.php?successCode=Sikeres korona vásárlás.");
        exit();
    }
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
    <script src="../assets/js/multiplyKorona.js"></script>
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
        <div class="korona-exchange">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <h2><span>Korona</span> Vásárlás</h2>
                <?php if($errorCode != ""): ?>
                        <p class="error" style="margin-bottom: 15px !important;"><?php echo $_GET["errorCode"] ?></p>
                <?php elseif($successCode != ""): ?>
                        <p class="success" style="margin-bottom: 15px !important;"><?php echo $_GET["successCode"] ?></p>
                <?php else: ?>
                        <p class="korona-text" style="margin-bottom: 15px !important;">Itt tudsz venni a fiókodra <span>koronát</span>.</p>
                <?php endif; ?>
                <div class="crown">
                    <i class="fa-solid fa-crown"></i>
                    <input type="number" id="korona-ertek" name="korona" oninput="multiplyKorona()" placeholder="Korona" id="">
                </div>
                <div class="price">
                    <i class="fa-solid fa-tag"></i>
                    <input type="text" id="korona-placeholder" disabled placeholder="Ár">
                </div>
                <button type="submit">Vásárlás</button>
            </form>
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