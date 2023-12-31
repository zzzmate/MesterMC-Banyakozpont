<?php

require_once "../../backend/db_con.php";

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

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // bé küldés történt ;>
    if(isset($_POST["receiver-be"]) && isset($_POST["ertek-be"]))
    {
        $szemelyzett = $_POST["receiver-be"];
        $ertek = $_POST["ertek-be"];

        // szerver oldali input ellenőrzés, mert vannak kedves emberek akik exploitokat keresnek
        if(!ctype_digit($ertek)) return header("Location: banyaszerme.php?errorCode=Kérlek érvényes számot írj be.");

        // magadnak akarsz utalni???
        if($szemelyzett == $username) return header("Location: banyaszerme.php?errorCode=Magadnak nem utalhatsz bányászérmét.");

        $checkUser = "SELECT * FROM users where `username` = ?";
        $checkUserPrepare = mysqli_prepare($connection, $checkUser);
        mysqli_stmt_bind_param($checkUserPrepare, "s", $szemelyzett);
        mysqli_stmt_execute($checkUserPrepare);
        $resultUser = mysqli_stmt_get_result($checkUserPrepare);
        $detailsUser = mysqli_fetch_assoc($resultUser);
        $rowsUser = mysqli_num_rows($resultUser);

        //kinek küldenél ha nem létezik?
        if($rowsUser <= 0) return header("Location: banyaszerme.php?errorCode=Nincs ilyen játékos.");

        // mit küldenél ha nincs?
        if($banyaszerme < $ertek) return header("Location: banyaszerme.php?errorCode=Nincs elég bányászérméd.");

        // normális ember & normális érték:
        $uniqueID = bin2hex(random_bytes(32));
        $sendBeRequest = "INSERT INTO utalasok (`utalas_kezdemenyzo`, `utalas_megkapo`, `tipus`, `datum`, `ertek`, `statusz`, `trade_id`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sendBeRequestPrepare = mysqli_prepare($connection, $sendBeRequest);
        $currentTipus = "Bányászérme";
        $currentStatusz = "Függőben";
        $currentDate = date('Y-m-d H:i:s');
        mysqli_stmt_bind_param($sendBeRequestPrepare, "sssssss", $username, $szemelyzett, $currentTipus, $currentDate, $ertek, $currentStatusz, $uniqueID);
        mysqli_stmt_execute($sendBeRequestPrepare);
        if (mysqli_stmt_affected_rows($sendBeRequestPrepare) > 0) {
            $generatedLink = "http://localhost/banyakozpont/pages/etc/banyaszerme_kuldes.php?key=$uniqueID&sender=$username";
            file_put_contents("../../email_logs/banyaszerme_kuldes.txt", $generatedLink);
            header("Location: banyaszerme.php?successCode=Sikeres bányászérme küldés,<br>kérlek nézd meg az emailod.");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bányászérme utalás</title>
    <link rel="stylesheet" href="../../assets/css/pages/elements/banyaszerme.css">
    <script src="https://kit.fontawesome.com/7159432989.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="elfelejtett-jelszo">
            <h2><span>MesterMC</span> Bányászérme utalás</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="password-class">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="receiver-be" placeholder="Felhasználó" id="">
                </div>
                <div class="password-again-class">
                    <i class="fa-solid fa-money-bill"></i>
                    <input type="number" name="ertek-be" placeholder="Bányászérme" id="">
                </div>
                <?php if($errorCode != ""): ?>
                    <p class="error"><?php echo $_GET["errorCode"] ?></p>
                <?php elseif($successCode != ""): ?>
                    <p class="success"><?php echo $_GET["successCode"] ?></p>
                <?php else: ?>
                    <p class="info-text">Itt tudsz utalni másnak <span>bányászérmét</span>.</p>
                <?php endif; ?>
                <button type="submit">Bányászérme utalás</button>
                <a href="../vasarlas.php" class="vissza">Vissza</a>
            </form>
        </div>
    </div>
</body>
</html>