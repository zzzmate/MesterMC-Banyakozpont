<?php

require_once "../../../backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

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

if($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if(isset($_FILES["kep-feltoltes"]) && !isset($_POST["delete"])) 
    {
        $uploadedPic = $_FILES["kep-feltoltes"];

        if($_FILES['kep-feltoltes']['error'] != UPLOAD_ERR_OK) return header("Location: kalap.php?errorCode=Hiba történt.");

        $fileType = exif_imagetype($uploadedPic['tmp_name']);

        if($fileType !== IMAGETYPE_PNG) return header("Location: kalap.php?errorCode=Csak PNG típusú képeket fogadunk el.");
        list($width, $height) = getimagesize($uploadedPic['tmp_name']);
        if($width != 64 && $height != 32) return header("Location: kalap.php?errorCode=Csak 64x32-s kalaport lehet feltölteni.");
    
        $fileName = $username . "_kalap.png"; 
        $dir = "../../../assets/media/kellekek/kalapok/";
        $target = $dir . $fileName;

        if(move_uploaded_file($uploadedPic['tmp_name'], $target)) 
        {
            $uploadSkinQuery = "UPDATE kellekek SET kalap = ? WHERE username = ?";
            $uploadSkinQueryPrepare = mysqli_prepare($connection, $uploadSkinQuery);
            mysqli_stmt_bind_param($uploadSkinQueryPrepare, "ss", $fileName, $username);
            mysqli_stmt_execute($uploadSkinQueryPrepare);
            header("Location: kalap.php?successCode=Sikeres kalap feltöltés.");
            exit();
        }
        else
        {
            header("Location: kalap.php?errorCode=Hiba történt a kalap feltöltése során.");
            exit();
        }
    }

    if(isset($_POST["delete"]))
    {

        $fileName = $username . "_kalap.png"; 
        $dir = "../../../assets/media/kellekek/kalapok/";
        $target = $dir . $fileName;

        if(!file_exists($target)) return header("Location: kalap.php?errorCode=Neked nincs feltöltve kalap.");

        try {
            unlink($target);
        } catch (\Throwable $th) {
            header("Location: kalap.php?errorCode=$th.");
            exit();
        }

        $uploadSkinQuery = "UPDATE kellekek SET kalap = NULL WHERE username = ?";
        $uploadSkinQueryPrepare = mysqli_prepare($connection, $uploadSkinQuery);
        mysqli_stmt_bind_param($uploadSkinQueryPrepare, "s", $username);
        mysqli_stmt_execute($uploadSkinQueryPrepare);
        header("Location: kalap.php?successCode=Sikeres kalap törlés.");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalap változtatás</title>
    <link rel="stylesheet" href="../../../assets/css/pages/elements/kellekek/template.css">
</head>
<body>
    <div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h2><span>Kalap</span> feltöltés</h2>
            <?php if($errorCode != ""): ?>
                <p class="error"><?php echo $_GET["errorCode"] ?></p>
            <?php elseif($successCode != ""): ?>
                <p class="success"><?php echo $_GET["successCode"] ?></p>
            <?php else: ?>
                <p>Itt tudod feltölteni a <span>kalapodat</span>.</p>
            <?php endif; ?>
            <img src="../../../assets/media/kellekek/skinek/template.jpg" alt="">
            <input type="file" name="kep-feltoltes" id="">
            <div class="buttons">
                <button type="submit">Kalap feltöltés</button>
                <button type="submit" class="dlt-button" name="delete">Jelenlegi törlése</button>
            </div>
            <a href="../../kellekek.php" class="vissza">Vissza</a>
        </form>
    </div>
</body>
</html>