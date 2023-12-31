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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // live time korona kérdezés

    $koronaQuery = "SELECT * FROM users where `username` = ?";
    $koronaPrepare = mysqli_prepare($connection, $koronaQuery);
    mysqli_stmt_bind_param($koronaPrepare, "s", $username);
    mysqli_stmt_execute($koronaPrepare);
    $resultKorona = mysqli_stmt_get_result($koronaPrepare);
    $detailsKorona = mysqli_fetch_assoc($resultKorona);
    $rowsKorona = mysqli_num_rows($resultKorona);

    $ladaQuery = "SELECT * FROM ladak where `lada_username` = ?";
    $ladaPrepare = mysqli_prepare($connection, $ladaQuery);
    mysqli_stmt_bind_param($ladaPrepare, "s", $username);
    mysqli_stmt_execute($ladaPrepare);
    $resultLada = mysqli_stmt_get_result($ladaPrepare);
    $detailsLada = mysqli_fetch_assoc($resultLada);
    $rowsLada = mysqli_num_rows($resultLada);

    $currentKorona = $detailsKorona["korona"];
    
    // alap láda vásárlás
    if(isset($_POST["sarga-chest"]))
    {
        $sargaChestAra = 2; // 2 korona az ára
        $currentAlapLada = $detailsLada["alap_lada"];

        if($currentKorona < $sargaChestAra) return header("Location: aldasok.php?errorCode=Nincs elég koronád.");

        $updateLadaQuery = "UPDATE users, ladak SET korona = ?, lada_username = ?, alap_lada = ? WHERE username = ?";
        $updateLadaPrepare = mysqli_prepare($connection, $updateLadaQuery);
        $realKorona = $currentKorona - $sargaChestAra;
        $realLada = $currentAlapLada + 1;
        mysqli_stmt_bind_param($updateLadaPrepare, "ssss", $realKorona, $username, $realLada, $username);
        mysqli_stmt_execute($updateLadaPrepare);

        header("Location: aldasok.php?successCode=Sikeres alap láda vásárlás.");
        exit();
    }

    // pandora szelencéje vásárlás
    if(isset($_POST["lila-chest"]))
    {
        $lilaChestAra = 5; // 5 korona az ára
        $currentPandoraLada = $detailsLada["pandora_szelenceje"];

        if($currentKorona < $lilaChestAra) return header("Location: aldasok.php?errorCode=Nincs elég koronád.");

        $updateLadaQuery = "UPDATE users, ladak SET korona = ?, lada_username = ?, pandora_szelenceje = ? WHERE username = ?";
        $updateLadaPrepare = mysqli_prepare($connection, $updateLadaQuery);
        $realKorona = $currentKorona - $lilaChestAra;
        $realLada = $currentPandoraLada + 1;
        mysqli_stmt_bind_param($updateLadaPrepare, "ssss", $realKorona, $username, $realLada, $username);
        mysqli_stmt_execute($updateLadaPrepare);

        header("Location: aldasok.php?successCode=Sikeres pandora szelencéje láda vásárlás.");
        exit();
    }

    // kiválaszottak ládája vásárlás
    if(isset($_POST["piros-chest"]))
    {
        $pirosChestAra = 10; // 10 korona az ára
        $currentKivalasztottLada = $detailsLada["kivalasztottak_ladaja"];

        if($currentKorona < $pirosChestAra) return header("Location: aldasok.php?errorCode=Nincs elég koronád.");

        $updateLadaQuery = "UPDATE users, ladak SET korona = ?, lada_username = ?, kivalasztottak_ladaja = ? WHERE username = ?";
        $updateLadaPrepare = mysqli_prepare($connection, $updateLadaQuery);
        $realKorona = $currentKorona - $pirosChestAra;
        $realLada = $currentKivalasztottLada + 1;
        mysqli_stmt_bind_param($updateLadaPrepare, "ssss", $realKorona, $username, $realLada, $username);
        mysqli_stmt_execute($updateLadaPrepare);

        header("Location: aldasok.php?successCode=Sikeres kiválasztottak láda vásárlás.");
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
    <script src="../assets/js/controlPopup.js"></script>
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
    <div class="overlay" id="popupOverlay">
        <div class="popup">
            <p id="popupText"></p>
            <button class="orange-btn" onclick="closePopup()">Bezárás</button>
        </div>
    </div>
    <div id="aldasok-container" class="container">
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
        <div class="al-items">
            <div class="aldasok">
                <h2>Áldások</h2>
                <div class="p-items">
                    <p>Túl <span>sok áldás</span> van ezért lusta vagyok megcsinálni. :/</p>
                    <p>De ha <span>fizetsz</span> érte meggondolom.</p>
                </div>
            </div>
            <div class="ladak">
                <h2>Ládák</h2>
                <?php if($errorCode != ""): ?>
                        <script>
                            openPopup('<?php echo htmlspecialchars($_GET["errorCode"]); ?>');
                        </script>
                <?php elseif($successCode != ""): ?>
                    <script>
                            openPopup('<?php echo htmlspecialchars($_GET["successCode"]); ?>');
                    </script>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="chests">
                <div class="sarga-chest">
                        <p>Alap láda</p>
                        <img src="../assets/media/chest/chest_sarga.png" alt="">
                        <p>2 Korona</p>
                        <button type="submit" name="sarga-chest">Megveszem</button>
                        <a class="show-items" onclick="loadFile('alaplada_tartalma.txt');" href="#">Tartalma</a>
                    </div>
                    <div class="lila-chest">
                        <p>Pandora szelencéje</p>
                        <img src="../assets/media/chest/chest_lila.png" alt="">
                        <p>5 Korona</p>
                        <button type="submit" name="lila-chest">Megveszem</button>
                        <a class="show-items" onclick="loadFile('pandora_szelenceje_tartalma.txt');" href="#">Tartalma</a>
                    </div>
                    <div class="piros-chest">
                        <p>Kiválasztottak ládája</p>
                        <img src="../assets/media/chest/chest_piros.png" alt="">
                        <p>10 Korona</p>
                        <button type="submit" name="piros-chest">Megveszem</button>
                        <a class="show-items" onclick="loadFile('kivalasztottak_ladaja_tartalma.txt');" href="#">Tartalma</a>
                    </div>
                </form>
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