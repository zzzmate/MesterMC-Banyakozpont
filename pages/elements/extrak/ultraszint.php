<?php

require_once "../../../backend/db_con.php";

$errorCode = isset($_GET["errorCode"]) ? isset($_GET["errorCode"]) : "";
$successCode = isset($_GET["successCode"]) ? isset($_GET["successCode"]) : "";

session_start();

if($_SESSION["logged_in"] == false || $_SESSION["logged_in"] != true) return header("Location: ../../../index.php");


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

$extrakQuery = "SELECT * FROM extrak WHERE `username` = ?";
$extrakQueryPrepare = mysqli_prepare($connection, $extrakQuery);
mysqli_stmt_bind_param($extrakQueryPrepare, "s", $username);
mysqli_stmt_execute($extrakQueryPrepare);

$resultsExtrak = mysqli_stmt_get_result($extrakQueryPrepare);
$detailsExtrak = mysqli_fetch_assoc($resultsExtrak);

$udvozloCheck = $detailsExtrak["ultra_szint_aktivalva"];
$currentMode = "Ultra szint";
$currentSqlMode = "ultra_szint_aktivalva";

$extrakAraQuery = "SELECT * FROM extrak_ara";
$extrakAraQueryPrepare = mysqli_prepare($connection, $extrakAraQuery);
mysqli_stmt_execute($extrakAraQueryPrepare);

$resultsAraExtrak = mysqli_stmt_get_result($extrakAraQueryPrepare);
$detailsAraExtrak = mysqli_fetch_assoc($resultsAraExtrak);

$udvozloErtekBe = $detailsAraExtrak["nevelo_be"];
$udvozloErtekForint = $detailsAraExtrak["nevelo_ft"];

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    //béből veszi
    if(isset($_POST["activate-item-be"])) 
    {
        if($banyaszerme < $udvozloErtekBe) return header("Location: ultraszint.php?errorCode=Neked nincs elég bányászérméd erre.");
        
        $updateItem = "UPDATE extrak SET $currentSqlMode = 1 WHERE username = ?";
        $updateItemPrepare = mysqli_prepare($connection, $updateItem);
        mysqli_stmt_bind_param($updateItemPrepare, "s", $username);
        mysqli_stmt_execute($updateItemPrepare);
        header("Location: ultraszint.php");
        exit();
    }

    //ftből veszi
    if(isset($_POST["activate-item-ft"]))
    {
        if($forint < $udvozloErtekForint) return header("Location: ultraszint.php?errorCode=Neked nincs elég forintod erre.");
        
        $updateItem = "UPDATE extrak SET $currentSqlMode = 1 WHERE username = ?";
        $updateItemPrepare = mysqli_prepare($connection, $updateItem);
        mysqli_stmt_bind_param($updateItemPrepare, "s", $username);
        mysqli_stmt_execute($updateItemPrepare);
        header("Location: ultraszint.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentMode); ?></title>
    <link rel="stylesheet" href="../../../assets/css/pages/elements/extrak/template.css">
</head>
<body>
    <div class="container">
        <!-- nincs neki aktiválva -->
        <?php if ($udvozloCheck === 0): ?>
            <div class="currency">
                <h2>Egyenleged</h2>
                <div class="current-currency">
                    <p><?php echo htmlspecialchars($banyaszerme); ?> Bé</p>
                    <p><?php echo htmlspecialchars($forint); ?> Forint</p>
                </div>
            </div>
            <div class="not-activated">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <h2><?php echo htmlspecialchars($currentMode); ?></h2>
                    <?php if($errorCode != ""): ?>
                        <p class="error"><?php echo $_GET["errorCode"] ?></p>
                    <?php elseif($successCode != ""): ?>
                            <p class="success"><?php echo $_GET["successCode"] ?></p>
                    <?php else: ?>
                            <p>Neked <span>nincs aktiválva</span> az <span style="text-transform: lowercase;"><?php echo htmlspecialchars($currentMode); ?></span> funkció.</p>                
                    <?php endif; ?>
                    <p><span><?php echo htmlspecialchars($udvozloErtekBe); ?></span> Bé / <span><?php echo htmlspecialchars($udvozloErtekForint); ?></span> Forint</p>
                    <div class="buttons">
                        <!-- ha valaki használni akarja jó lenne ha itt lenne egy megerősítés mert ha véletlenül nyom rá valaki, és megveszi olyan pert indítanak a cég ellen -->
                        <button type="submit" name="activate-item-be">Bányászérme</button>
                        <button type="submit" name="activate-item-ft" style="padding-inline: 32px !important;">Forint</button>
                    </div>
                    <a href="../../kellekek.php" class="vissza">Vissza</a>
                </form> 
            </div>
        <?php elseif($udvozloCheck === 1): ?>
            <!-- aktiválva van neki -->
            <div class="activated">
                <div class="activated-infos">
                    <p>Már sikeresen <span>ultra szinted</span> van.</p>
                    <a href="../../kellekek.php" class="vissza">Vissza</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>