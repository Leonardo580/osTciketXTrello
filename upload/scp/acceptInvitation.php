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
$query=$link->prepare("select token, tmstmp, id_repo, id_user from pending_members where token=?");
$query->bind_param("s", $token);
$res=$query->get_result();
if (!$res){
    echo "<h1>Unknown error</h1>";
}else {
    $pm=$res->fetch_array();

}

?>

<?php
require_once(STAFFINC_DIR . 'footer.inc.php');
?>