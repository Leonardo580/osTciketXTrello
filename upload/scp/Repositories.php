<?php


require('staff.inc.php');
require_once(INCLUDE_DIR.'class.task.php');
require_once(INCLUDE_DIR.'class.export.php');

$nav->setTabActive('Repositories');
$open_name = _P('queue-name',
    /* This is the name of the open tasks queue */
    'Open');
$nav->addSubMenu(array('desc'=>__('New Repository'),
    'title'=> __('Open a New Repository'),
    'href'=>'addRepositories.php',
    'iconclass'=>'newTicket new-task',
    'id' => 'new-repository',
    'attr' => array(
        'data-dialog-config' => '{"size":"large"}'
    )
),
    ($_REQUEST['a']=='open'));


$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");

$inc="RepoTable.php";
require_once(STAFFINC_DIR.'header.inc.php');
require_once("RepoTable.php");
require_once(STAFFINC_DIR.'footer.inc.php');
?>
