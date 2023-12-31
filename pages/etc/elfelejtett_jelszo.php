<?php

require_once "../../backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

if(!isset($_GET["username"]) || !isset($_GET["key"])) return header("Location: ../../index.php");

session_start();

$_SESSION['username'] = trim($_GET["username"]);
$_SESSION['key'] = trim($_GET["key"]);

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$key = isset($_SESSION['key']) ? $_SESSION['key'] : '';

$checkKeySql = "SELECT * FROM users WHERE `username` = ? AND `new_password_id` = ?";
$prepare = mysqli_prepare($connection, $checkKeySql);
mysqli_stmt_bind_param($prepare, "ss", $username, $key);
mysqli_stmt_execute($prepare);
$results = mysqli_stmt_get_result($prepare);
$rows = mysqli_num_rows($results);
$details = mysqli_fetch_assoc($results);

if($rows <= 0) return header("Location: ../../index.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["newPassword"]) && isset($_POST["newPasswordAgain"])) {
        $newPassword = $_POST["newPassword"];            
        $newPasswordAgain = $_POST["newPasswordAgain"];
        
        $username = $details["username"];

        if($newPassword != $newPasswordAgain) return header("Location: elfelejtett_jelszo.php?errorCode=A két jelszó nem egyezik.");

        $changeQuery = "UPDATE users SET `password` = ?, `new_password_id` = NULL WHERE `username` = ?";
        $prepareChange = mysqli_prepare($connection, $changeQuery);
        mysqli_stmt_bind_param($prepareChange, "ss", $newPassword, $username);
        mysqli_stmt_execute($prepareChange);

        header("Location: ../../index.php?successCode=Sikeres jelszó váltás.");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elfelejtett Jelszó</title>
    <link rel="stylesheet" href="../../assets/css/pages/etc/elfelejtett_jelszo.css">
    <script src="https://kit.fontawesome.com/7159432989.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="elfelejtett-jelszo">
            <h2><span>MesterMC</span> Jelszó Változtatás</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?username=' . urlencode($username) . '&key=' . urlencode($key); ?>" method="POST">
                <div class="password-class">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="newPassword" placeholder="Jelszó" id="">
                </div>
                <div class="password-again-class">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="newPasswordAgain" placeholder="Jelszó Újra" id="">
                </div>
                <?php if($errorCode != ""): ?>
                    <p class="error"><?php echo $_GET["errorCode"] ?></p>
                <?php elseif($successCode != ""): ?>
                    <p class="success"><?php echo $_GET["successCode"] ?></p>
                <?php else: ?>
                    <p class="info-text">Kérlek írd be a <span>jelszavadat kétszer</span>, hogy megtudd változtatni.</p>
                <?php endif; ?>
                <button type="submit">Jelszó változtatás</button>
            </form>
        </div>
    </div>
</body>
</html>