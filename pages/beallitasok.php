<?php

require_once "../backend/db_con.php";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../index.php");

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

$errorCodePFP = isset($_GET["errorCodePFP"]) ? isset($_GET["errorCodePFP"]) : "";
$successCodePFP = isset($_GET["successCodePFP"]) ? isset($_GET["successCodePFP"]) : "";

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
$regisztracio = $details["regisztracio"];


$kepPath = "../assets/media/pictures/" . $username . "_pfp.png";

if(file_exists($kepPath))
    $imgUrl = $kepPath;
else
    $imgUrl = "../assets/media/pictures/default.png";


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // email váltást kért
    if(isset($_POST["newEmail"]) && isset($_POST["email-valtas"]))
    {
        $newEmail = $_POST["newEmail"];

        //backenden is jó ha leteszteled hogy emailt írt-e be mert az inputot megtudja változtatni client side :P
        if(filter_var($newEmail, FILTER_VALIDATE_EMAIL) == false) return header("Location: beallitasok.php?errorCode=Kérlek egy valós emailt írj be.");

        $updateEmail = "UPDATE users SET `new_email_id` = ?, `new_email` = ? WHERE `username` = ?";
        $emailPrepare = mysqli_prepare($connection, $updateEmail);
        $uniqueID = bin2hex(random_bytes(32));
        mysqli_stmt_bind_param($emailPrepare, "sss", $uniqueID, $newEmail, $username);
        mysqli_stmt_execute($emailPrepare);

        $generatedLink = "http://localhost/banyakozpont/pages/etc/elfelejtett_email.php?key=$uniqueID&newEmail=$newEmail";

        file_put_contents("../email_logs/email_valtas.txt", $generatedLink);

        header("Location: beallitasok.php?successCode=Sikeres email váltás kérelem,<br> kérlek nézd meg az emailod.");
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
    <script src="../assets/js/controlPopup.js"></script>
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
        <div class="beallitasok">
            <div class="email-valtas">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <h2><span>Email</span> váltás</h2>
                <?php if($errorCode != ""): ?>
                        <p class="error"><?php echo $_GET["errorCode"] ?></p>
                <?php elseif($successCode != ""): ?>
                        <p class="success"><?php echo $_GET["successCode"] ?></p>
                <?php else: ?>
                        <p class="email-text">Itt tudod az <span>email címedet</span> változtatni.</p>
                <?php endif; ?>
                    <div class="newMail-class">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="newEmail" placeholder="Új email" id="">
                    </div>
                    <button type="submit" name="email-valtas"">Email váltás</button>
                    <p style="margin-top: 15px !important; text-align: center;">Regisztrációd ideje: <span><?php echo htmlspecialchars($regisztracio); ?></span></p>
                </form>
            </div>
            <div class="pfp-valtas">
            <?php if($errorCodePFP != ""): ?>
                    <script>
                        openPopup('<?php echo htmlspecialchars($_GET["errorCodePFP"]); ?>');
                    </script>
            <?php elseif($successCodePFP != ""): ?>
                    <script>
                            openPopup('<?php echo htmlspecialchars($_GET["successCodePFP"]); ?>');
                    </script>
            <?php endif; ?>
                <form action="./etc/kep_valtoztatas.php" method="POST" enctype="multipart/form-data">
                <h2><span>Profilkép</span> váltás</h2>
                    <p class="email-text">Itt tudod a <span>bányaközponti profilképedet</span> változtatni.</p>
                    <div class="newPfp-class">
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="">
                    </div>
                    <input type="file" name="newImage" id="">
                    <button type="submit" name="pfp-valtas">Kép változtatás</button>
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