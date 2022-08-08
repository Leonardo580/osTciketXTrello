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
<h2 style="color: black">Board's Cards: </h2>
<hr>
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


<br>
<div class="cards">

        <article class="card" id="todo">
            <header>

                <h2>
                    To Do
                </h2>
            </header>
            <img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">
            <div class="content">
                <p></p>

            </div>
            <br>
            <button style="text-align: left; " onclick="addActivity('todo')" ><i class="icon-plus icon-2x" style="float: left"></i>

                Add Activity </button>
        </article> <article class="card" id="inprogress">
            <header>

                <h2>
                    In Progress
                </h2>
            </header>
            <img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">
            <div class="content">
                <p></p>

            </div>
            <br>
            <button style="text-align: left; " id="inprogress"><i class="icon-plus icon-2x" style="float: left"></i>

                Add Activity </button>
        </article> <article class="card" >
            <header>

                <h2>
                    Done
                </h2>
            </header>
            <img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">
            <div class="content">
                <p></p>

            </div>
            <br>
            <button style="text-align: left; " id="done"><i class="icon-plus icon-2x" style="float: left"></i>

                Add Activity </button>
        </article>


</div>








<?php

require_once(STAFFINC_DIR.'footer.inc.php');
?>

<script  >
    function addActivity(id) {
        let form = '<form action="" method="post" class="form-add">' +
            '<?php csrf_token(); ?>' +
            '<input type="text" name="content" placeholder="New Activity">' +
            '<br>' +
            '<select>' +
            '<option value=0>To Do</option>' +
            '<option value=1> In Progress</option>' +
            '<option value=2> Done</option>' +
            '</select>' +
            '<br>' +
            '<br>' +
            '<input  type="submit" value="Add">' +
            '<input type="button" name="cancel-add-activity" value="Cancel">' +
            '</form>';
        let f = $(form)
        console.log(f.children('input[name="cancel-add-activity"]'))
        f.children('input[name="cancel-add-activity"]').click(function () {
            $(this).parent().remove();
        });
        f.on("submit", function (e) {
            e.preventDefault();
            let content = $(this).children('input[name="content"]').val();
            let status = $(this).children("select").val();
            $.ajax({
                url: 'ajax.php/activities/add/' + id,
                type: 'post',
                data: {
                    content: content,
                    status: status
                },
                success: function (data) {

                },
                error: function (data) {
                    console.log("could not add an activity");
                }
            })
        })
        $("#" + id).children('.content').append(f)
    }



</script>
