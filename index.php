<?php

require_once "./backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";
$panelMode = isset($_GET["mode"]) ? isset($_GET["mode"]) : "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // bejelentkezés
    if(isset($_POST["username"]) && isset($_POST["password"]))
    {
        $username = $_POST["username"];
        $password = $_POST["password"];

        if(empty($username)) return header("Location: index.php?errorCode=Üres felhasználónév.");
        if(empty($password)) return header("Location: index.php?errorCode=Üres jelszó.");

        $bejelentkezesQuery = "SELECT * FROM users where `username` = ?";
        $bejelentkezesPrepare = mysqli_prepare($connection, $bejelentkezesQuery);
        mysqli_stmt_bind_param($bejelentkezesPrepare, "s", $username);
        mysqli_stmt_execute($bejelentkezesPrepare);
        $result = mysqli_stmt_get_result($bejelentkezesPrepare);
        $details = mysqli_fetch_assoc($result);
        $rows = mysqli_num_rows($result);

        if($rows < 0) return header("Location: index.php?errorCode=Rossz felhasználónév vagy jelszó.");

        $realJelszo = $details["password"];
        if($password != $realJelszo) return header("Location: index.php?errorCode=Rossz felhasználónév vagy jelszó.");

        // sikeres bejelentkezés ;<

        session_start();

        $banyaszerme = $details["banyaszerme"];
        $forint = $details["forint"];
        $korona = $details["korona"];

        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password;
        $_SESSION["logged_in"] = true;

        // nincs live update a sessionokban csak ha ujra belep >.<

        //$_SESSION["banyaszerme"] = $banyaszerme;
        //$_SESSION["forint"] = $forint;
        //$_SESSION["korona"] = $korona;

        header("Location: ./pages/main.php");
        exit();

    }
    
    // elfelejtett jelszó
    if (isset($_POST["username"]) && isset($_POST["email"])) {
        $username = $_POST["username"];
        $email = $_POST["email"];

        if(empty($username)) return header("Location: index.php?mode=1&errorCode=Üres felhasználónév.");
        if(empty($email)) return header("Location: index.php?mode=1&errorCode=Üres email.");

        $emailQuery = "SELECT `email` FROM users WHERE username = ?";
        $prepare = mysqli_prepare($connection, $emailQuery);
        mysqli_stmt_bind_param($prepare, "s", $username);
        mysqli_stmt_execute($prepare);
        $result = mysqli_stmt_get_result($prepare);
        $rows = mysqli_num_rows($result);

        // van ilyen regisztrált felhasználó
        if($rows < 0) return header("Location: index.php?mode=1&errorCode=Nem találtunk ilyen felhasználót.");
        $details = mysqli_fetch_assoc($result);
        $realEmail = $details["email"];
        
        // email teszt (felhasználóé a beírt email)
        if($realEmail != $email) return header("Location: index.php?mode=1&errorCode=Sajnos nem egyezik az email a felhasználóval.");    
        
        $setACodeQuery = "UPDATE users SET new_password_id = ? WHERE username = ?";
        $prepareCode = mysqli_prepare($connection, $setACodeQuery);
        $uniqueID = bin2hex(random_bytes(32));
        mysqli_stmt_bind_param($prepareCode, "ss", $uniqueID, $username);
        mysqli_stmt_execute($prepareCode);

        $generatedLink = "http://localhost/banyakozpont/pages/etc/elfelejtett_jelszo.php?username=$username&key=$uniqueID";
        file_put_contents("./email_logs/jelszo_valtas.txt", $generatedLink);

        header("Location: index.php?mode=1&successCode=Sikeres kód küldés,<br> kérlek nézd meg az emailod.");
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
    <link rel="stylesheet" href="./assets/css/index.css">
    <script src="https://kit.fontawesome.com/7159432989.js" crossorigin="anonymous"></script>
    <script src="./assets/js/changePanelMode.js"></script>
</head>
<body>
    <div class="container">
        <div class="container-items">
            <div class="bejelentkezes">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <h2>MesterMC <span>Bányaközpont</span></h2>
                    <?php if($panelMode == ""): ?>
                        <div class="username-class">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" placeholder="Felhasználónév">
                        </div>
                        <div class="password-class">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" placeholder="Jelszó">
                        </div>
                        <?php if($errorCode != ""): ?>
                            <p class="error"><?php echo $_GET["errorCode"] ?></p>
                        <?php elseif($successCode != ""): ?>
                            <p class="success"><?php echo $_GET["successCode"] ?></p>
                        <?php else: ?>
                            <p class="welcome-text">Köszöntünk a <span>bányakozöpontban</span>.</p>
                        <?php endif; ?>
                        <div class="buttons">
                            <a href="#" class="forgot-password" onclick="gotoForgotPassword()" style="text-decoration: none;">Elfelejtett jelszó</a>
                            <button type="submit">
                                Bejelentkezés
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </button>
                        </div>
                        <hr>
                        <div class="register-button">
                            <p>Ha nincs <span>fiókod</span>, tudsz <span>regisztrálni</span> a lenti gombbal.</p>
                            <a href="#" target="_blank" class="register" style="text-decoration: none;">Regisztráció<i class="fa-solid fa-user-plus"></i></a>
                        </div>
                    <?php else: ?>
                        <div class="username-class">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" placeholder="Felhasználónév">
                        </div>
                        <div class="email-class">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="E-Mail cím">
                        </div>
                        <?php if($errorCode != ""): ?>
                            <p class="error"><?php echo $_GET["errorCode"] ?></p>
                        <?php elseif($successCode != ""): ?>
                            <p class="success"><?php echo $_GET["successCode"] ?></p>
                        <?php endif; ?>
                        <div class="password-get">
                            <button type="submit">
                                Kód kérés
                                <i class="fa-solid fa-mobile-screen"></i>
                            </button>
                        </div>
                        <p class="email-info">Kódot tudsz igényelni a <span>MesterMC</span> fiókodhoz, ha elfelejtetted a <span>jelszavad</span>.</p>
                        <div class="go-back">
                            <a class="back-button" href="#" onclick="gotoIndexPage()">Vissza</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="kepek">
                <div class="sor-1">
                    <div class="kep-1"><div class="img-items"><a href="https://www.youtube.com/user/szenatordoggy" target="_blank"><img src="./assets/media/youtubers/doggy.jpg" alt=""></a><p>DoggyAndi</p></div></div>
                    <div class="kep-2"><div class="img-items"><a href="https://www.youtube.com/user/luckeYpublic" target="_blank"><img src="./assets/media/youtubers/luckey.jpg" alt=""></a><p>Luckey</p></div></div>
                    <div class="kep-3"><div class="img-items"><a href="https://www.youtube.com/user/OwnMcKendry" target="_blank"><img src="./assets/media/youtubers/own.jpg" alt=""></a><p>OwnMcKendy</p></div></div>
                    <div class="kep-4"><div class="img-items"><a href="https://www.youtube.com/channel/UCsjvWMtSQBi2dXak38E-TRg" target="_blank"><img src="./assets/media/youtubers/sajtos.jpg" alt=""></a><p>Sajt32</p></div></div>
                    <div class="kep-5"><div class="img-items"><a href="https://www.youtube.com/channel/UC6IUish1nuhvf2GCyYJPIoQ" target="_blank"><img src="./assets/media/youtubers/ubi.jpg" alt=""></a><p>Uborcraft</p></div></div>
                </div>
                <div class="sor-2">
                    <div class="kep-6"><div class="img-items"><a href="https://www.youtube.com/Chabinho76" target="_blank"><img src="./assets/media/youtubers/chabinho.jpg" alt=""></a><p>Chabinho</p></div></div>
                    <div class="kep-7"><div class="img-items"><a href="https://www.youtube.com/user/AnettandAncsa" target="_blank"><img src="./assets/media/youtubers/anett.jpg" alt=""></a><p>Anett</p></div></div>
                    <div class="kep-8"><div class="img-items"><a href="https://www.youtube.com/user/AnettandAncsa" target="_blank"><img src="./assets/media/youtubers/ancsa.jpg" alt=""></a><p>Ancsa</p></div></div>
                    <div class="kep-9"><div class="img-items"><a href="https://www.youtube.com/channel/UCy6Oqzz-dfav0uCWHZO7qWg" target="_blank"><img src="./assets/media/youtubers/beni.jpg" alt=""></a><p>BENIIPOWA</p></div></div>
                    <div class="kep-10"><div class="img-items"><a href="https://www.youtube.com/channel/UCAfpm3hE3qtHJB59Ebbd23g" target="_blank"><img src="./assets/media/youtubers/zsdav.jpg" alt=""></a><p>Zsdav</p></div></div>
                </div>
            </div>
            <div class="footer-container footer-container-pc">
                <h2 style="color: white; font-size: 15px; margin-right: 40px;">localhost/<span>banyakozpont</span></h2>
                <div class="links">
                    <a href="#">Kapcsolat</a>
                    <a href="#">ÁSZF</a>
                    <a href="#">Adatvédelmi nyilatkozat</a>
                    <a href="#">Cookiek</a>
                    <a href="#"><span>2011-2023</span> MesterMC.hu</a>
                </div>
            </div>
        </div>
        <div class="footer-container footer-container-mobile">
            <div class="links">
                <h2>localhost/<span>banyakozpont</span></h2>
                <div class="column-1">
                    <a href="#">Kapcsolat</a>
                    <a href="#">ÁSZF</a>
                    <a href="https://instagram.com/_mate666">Instagram</a>
                </div>
                <div class="column-2">
                    <a href="#">Nyilatkozat</a>
                    <a href="https://steamcommunity.com/id/sixhunna">Steam</a>
                    <a href="#">Cookiek</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>