<?php

require_once "../../backend/db_con.php";

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") 
{
    //pfp váltás
    if(isset($_FILES["newImage"])) {
        $newImage = $_FILES["newImage"];
    
        //png check
        $fileExtension = exif_imagetype($uploadedPic['tmp_name']);
    
        if($fileExtension !== IMAGETYPE_PNG) return header("Location: ../beallitasok.php?errorCodePFP=Csak PNG formátumú képeket tölthetsz fel.");
        
        $username = $_SESSION["username"];

        // azért hardcodeolt a '.png' mert ha valaki kijátszaná a file extensiont valamilyen oknál fokva,
        // akkor ne bírja azt lefuttatni ;>
        $fileName = $username . "_pfp.png"; 
        $dir = "../../assets/media/pictures/";
        $target = $dir . $fileName;

        if(move_uploaded_file($newImage["tmp_name"], $target))
        {
            $uploadNameQuery = "UPDATE users SET picture = ? WHERE username = ?";
            $uploadNamePrepare = mysqli_prepare($connection, $uploadNameQuery);
            mysqli_stmt_bind_param($uploadNamePrepare, "ss", $username, $username);
            mysqli_stmt_execute($uploadNamePrepare);
            header("Location: ../beallitasok.php?successCodePFP=Sikeres kép feltöltés.");
            exit();
        }
        else
        {
            header("Location: ../beallitasok.php?errorCodePFP=Hiba történt a kép feltöltés közben.");
            exit();
        }
    }
}

?>