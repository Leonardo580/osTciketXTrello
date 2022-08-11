<?php
require "staff.inc.php";
$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");

require_once(STAFFINC_DIR . 'header.inc.php');

$link=mysqli_connect("localhost", "anas", "22173515", "osticket");
$idr=$_GET['idr'];
$token= $_GET['token'];
$query="select token, tmstmp as 'tmstmp', id_repo, id_user from pending_members where token='".$token."'";
$res=mysqli_query($link, $query);


if (!$res){
    echo "<h1>Unknown error</h1>";
}else {
    $pm=mysqli_fetch_array($res);

    $timestamp=date($pm['tmstmp']);
    $current_time=date(time());


    if ( $_SERVER["REQUEST_TIME"] - $pm['tmstmp'] < 86400){
        $idr =$pm['id_repo'];
        $q=$link->prepare("insert into members (id_repo, id_user) VALUES (? , ?)");
        $q->bind_param("ii", $idr, $pm['id_user']);
        $q->execute();
        $q->close();
        echo "<h1>the invite has been accepted</h1>";
        echo "<a href='DetailedRepo.php?idr=$idr' >Click here</a> to proceed to the repository  </h2>";



    }
}

?>

<?php
require_once(STAFFINC_DIR . 'footer.inc.php');
?>