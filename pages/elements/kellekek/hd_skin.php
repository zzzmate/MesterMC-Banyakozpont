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

        if($_FILES['kep-feltoltes']['error'] != UPLOAD_ERR_OK) return header("Location: hd_skin.php?errorCode=Hiba történt.");
        

        $fileType = exif_imagetype($uploadedPic['tmp_name']);

        if($fileType !== IMAGETYPE_PNG) return header("Location: hd_skin.php?errorCode=Csak PNG típusú képeket fogadunk el.");
        list($width, $height) = getimagesize($uploadedPic['tmp_name']);
        if($width != 512 && $height != 256) return header("Location: hd_skin.php?errorCode=Csak 512x256-s skint lehet feltölteni.");
    
        $fileName = $username . "_hd_skin.png"; 
        $dir = "../../../assets/media/kellekek/hd_skinek/";
        $target = $dir . $fileName;

        if(move_uploaded_file($uploadedPic['tmp_name'], $target)) 
        {
            $uploadSkinQuery = "UPDATE kellekek SET skin = NULL, hd_skin = ? WHERE username = ?";
            $uploadSkinQueryPrepare = mysqli_prepare($connection, $uploadSkinQuery);
            mysqli_stmt_bind_param($uploadSkinQueryPrepare, "ss", $fileName, $username);
            mysqli_stmt_execute($uploadSkinQueryPrepare);
            header("Location: hd_skin.php?successCode=Sikeres HD skin feltöltés.");
            exit();
        }
        else
        {
            header("Location: hd_skin.php?errorCode=Hiba történt a HD skin feltöltése során.");
            exit();
        }
    }

    if(isset($_POST["delete"]))
    {

        $fileName = $username . "_hd_skin.png"; 
        $dir = "../../../assets/media/kellekek/hd_skinek/";
        $target = $dir . $fileName;

        if(!file_exists($target)) return header("Location: hd_skin.php?errorCode=Neked nincs feltöltve HD skin.");

        try {
            unlink($target);
        } catch (\Throwable $th) {
            header("Location: hd_skin.php?errorCode=$th.");
            exit();
        }

        $uploadSkinQuery = "UPDATE kellekek SET hd_skin = NULL WHERE username = ?";
        $uploadSkinQueryPrepare = mysqli_prepare($connection, $uploadSkinQuery);
        mysqli_stmt_bind_param($uploadSkinQueryPrepare, "s", $username);
        mysqli_stmt_execute($uploadSkinQueryPrepare);
        header("Location: hd_skin.php?successCode=Sikeres HD skin törlés.");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HD Skin változtatás</title>
    <link rel="stylesheet" href="../../../assets/css/pages/elements/kellekek/template.css">
</head>
<body>
    <div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h2><span>HD Skin</span> feltöltés</h2>
            <?php if($errorCode != ""): ?>
                <p class="error"><?php echo $_GET["errorCode"] ?></p>
            <?php elseif($successCode != ""): ?>
                <p class="success"><?php echo $_GET["successCode"] ?></p>
            <?php else: ?>
                <p>Itt tudod feltölteni a <span>HD skinedet</span>.</p>
            <?php endif; ?>
            <img src="../../../assets/media/kellekek/skinek/template.jpg" alt="">
            <input type="file" name="kep-feltoltes" id="">
            <div class="buttons">
                <button type="submit">HD Skin feltöltés</button>
                <button type="submit" class="dlt-button" name="delete">Jelenlegi törlése</button>
            </div>
            <a href="../../kellekek.php" class="vissza">Vissza</a>
        </form>
    </div>
</body>
</html>