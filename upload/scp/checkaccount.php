<?php
require('staff.inc.php');
$link=mysqli_connect("localhost", "anas", "22173515", "osticket");
if (isset($_GET['idr'])){
    $query = $link->prepare("select id_user, id_repo from members where id_repo=? and id_user=?");
    $query->bind_param("ii", $_GET['idr'], $thisstaff->getId());
    $query->execute();
    if (!is_array($query->get_result()->fetch_array(PDO::FETCH_LAZY)))
        Http::redirect("dashboard.php");

}
elseif (isset($_GET['idb'])) {
    $query= $link->prepare("select id_repo , id_user from members 
                         where (id_user=? and id_repo in (select id_repo from boards where id=?))");
    $query->bind_param("ii", $thisstaff->getId(), $_GET["idb"]);
    $query->execute();
    if (! is_array($query->get_result()->fetch_array(PDO::FETCH_LAZY)))
        Http::redirect("dashboard.php");
}