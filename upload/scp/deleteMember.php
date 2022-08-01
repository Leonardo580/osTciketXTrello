<?php
$link= mysqli_connect("localhost", "anas", "22173515", "osticket");
if (!$link)
    die( "Error: Unable to connect to MySQL." . PHP_EOL);
$id=$_GET['id'];
$idr=$_GET['idr'];
$sql = "delete from members where id_user = '$id' and id_repo = '$idr'";
 mysqli_query($link, $sql);
mysqli_close($link);
header("Location: DetailedRepo.php?idr=$idr");
?>
