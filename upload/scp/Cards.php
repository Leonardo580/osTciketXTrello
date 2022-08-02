<?php

require('staff.inc.php');
$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");
$nav->setTabActive('Repositories');
$open_name = _P('queue-name',
    /* This is the name of the open tasks queue */
    'Open');



require_once(STAFFINC_DIR.'header.inc.php');

?>
<link rel="stylesheet" href="../css/myStyle.css">
<!--<div class="row">
    <div class="column">
        <div class="">
            <img src="">
            <div class="container">
                <H4><b>Title</b></H4>
                <p>description</p>
            </div>
        </div>
    </div>

    <div class="column">
        <div class="card">
            <img src="">
            <div class="container">
                <H4><b>Title</b></H4>
                <p>description</p>
            </div>
        </div>
    </div>
</div>-->
    <div class="cards">
        <article class="card">
            <header>
                <h2>A short heading</h2>
            </header>
            <img src="../assets/default/images/generic_card.jpg" alt="Hot air balloons">
            <div class="content">
                <p> The idea of reaching the North Pole by means of balloons appears to have been entertained many
                    years ago. </p>
            </div>

        </article>
    </div>

<?php

require_once(STAFFINC_DIR.'footer.inc.php');
?>