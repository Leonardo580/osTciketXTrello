<?php
require "staff.inc.php";
require_once(INCLUDE_DIR . 'class.task.php');

require_once(INCLUDE_DIR . 'class.export.php');

$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");

?>
<form method="post" action="DetailedRepo.php">


</form>
<?php
require_once(STAFFINC_DIR . 'footer.inc.php');
?>