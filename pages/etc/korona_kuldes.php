<?php

require_once "../../backend/db_con.php";

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    if(!isset($_GET["key"]) && !isset($_GET["sender"])) return header("Location: ../../index.php");

    $uniqueID = $_GET["key"];
    $sender = $_GET["sender"];

    $checkSql = "SELECT * FROM utalasok WHERE trade_id = ?";
    $checkSqlPrepare = mysqli_prepare($connection, $checkSql);
    mysqli_stmt_bind_param($checkSqlPrepare, "s", $uniqueID);
    mysqli_stmt_execute($checkSqlPrepare);
    $results = mysqli_stmt_get_result($checkSqlPrepare);
    $rows = mysqli_num_rows($results);

    if($rows <= 0) return header("Location: ../../index.php");

    $details = mysqli_fetch_assoc($results);

    $realSender = $details["utalas_kezdemenyzo"];
    $realReceiver = $details["utalas_megkapo"];
    $currentStatus = $details["statusz"];
    $kuldottKorona = $details["ertek"];

    if($currentStatus != "Függőben") return header("Location: ../../index.php");

    if($realSender != $sender) return header("Location: ../../index.php");

    // nincs benne semmi csalafintaság! (magyarul az nyitotta meg a linket aki requestelte a korona küldést)

    $updateSql = "UPDATE utalasok SET statusz = ? WHERE trade_id = ?";
    $prepareUpdate = mysqli_prepare($connection, $updateSql);
    $statusNewUpdate = "Elfogadva";
    mysqli_stmt_bind_param($prepareUpdate, "ss", $statusNewUpdate, $uniqueID);
    mysqli_stmt_execute($prepareUpdate);

    $senderSql = "SELECT * FROM users WHERE username = ?";
    $senderPrepare = mysqli_prepare($connection, $senderSql);
    mysqli_stmt_bind_param($senderPrepare, "s", $realSender);
    mysqli_stmt_execute($senderPrepare);
    $senderResults = mysqli_stmt_get_result($senderPrepare);
    $senderDetails = mysqli_fetch_assoc($senderResults);

    $receiverSql = "SELECT * FROM users WHERE username = ?";
    $receiverPrepare = mysqli_prepare($connection, $receiverSql);
    mysqli_stmt_bind_param($receiverPrepare, "s", $realReceiver);
    mysqli_stmt_execute($receiverPrepare);
    $receiverResults = mysqli_stmt_get_result($receiverPrepare);
    $receiverDetails = mysqli_fetch_assoc($receiverResults);
    
    $senderSqlReal = "UPDATE users SET korona = korona - ? WHERE username = ?";
    $senderPrepareReal = mysqli_prepare($connection, $senderSqlReal);
    mysqli_stmt_bind_param($senderPrepareReal, "ss", $kuldottKorona, $realSender);
    mysqli_stmt_execute($senderPrepareReal);

    $receiverSqlReal = "UPDATE users SET korona = korona + ? WHERE username = ?";
    $receiverPrepareReal = mysqli_prepare($connection, $receiverSqlReal);
    mysqli_stmt_bind_param($receiverPrepareReal, "ss", $kuldottKorona, $realReceiver);
    mysqli_stmt_execute($receiverPrepareReal);

    header("Location: ../../index.php?successCode=Sikeres korona küldés.");
    exit();

}

?>