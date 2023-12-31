<?php

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    if(!isset($_GET["key"]) && !isset($_GET["newEmail"])) return header("Location: ../../index.php");

    $newEmail = $_GET["newEmail"];
    $key = $_GET["key"];

    require_once "../../backend/db_con.php";

    $checkQuery = "SELECT * FROM users WHERE new_email_id = ?";
    $checkPrepare = mysqli_prepare($connection, $checkQuery);
    mysqli_stmt_bind_param($checkPrepare, "s", $key);
    mysqli_stmt_execute($checkPrepare);
    $results = mysqli_stmt_get_result($checkPrepare);
    $rows = mysqli_num_rows($results);

    // oké tehát nincs ilyen key az adatbázisban ;<
    if($rows <= 0) return header("Location: ../../index.php");

    $details = mysqli_fetch_assoc($results);
    
    $currentNewEmail = $details["new_email"];

    // megszerezték a keyt viszont az email nem ugyan az mint ami kérve lett tehát vissza :)
    if($newEmail != $currentNewEmail) return header("Location: ../../index.php");

    $updateQuery = "UPDATE users SET new_email_id = NULL, email = ?, new_email = NULL WHERE new_email_id = ?";
    $updatePrepare = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updatePrepare, "ss", $newEmail, $key);
    mysqli_stmt_execute($updatePrepare);

    header("Location: ../../index.php?successCode=Sikeres email váltás.");
    exit();
}

?>