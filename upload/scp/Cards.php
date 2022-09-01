<?php

require('checkaccount.php');
$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");
$nav->setTabActive('Repositories');
$open_name = _P('queue-name',
    /* This is the name of the open tasks queue */
    'Open');

require(INCLUDE_DIR . "activities.php");

require_once(STAFFINC_DIR . 'header.inc.php');
$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
$link->query("call updateStatus();");
$link->close();
$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
$sql = "select id, id_board, title, description from cards where id_board=" . $_GET['idb'];
$result = mysqli_query($link, $sql);
$cards = array();
while ($row = mysqli_fetch_array($result)) {
    $cards[] = $row;
}
$query = $link->prepare("select c.ticket_id, subject, priority from ost_ticket__cdata c
inner join pending_tickets p on c.ticket_id = p.ticket_id
where (p.isActivity=false);");
$query->execute();
$res = $query->get_result();
$tickets = [];
while ($row = $res->fetch_assoc())
    $tickets[] = $row;

mysqli_close($link);


?>
<link rel="stylesheet" href="../css/myStyle.css">
<a href="ExportRepo.php" class="button action-button" style="float: right;"><i class="icon-download"></i> Export</a>
<h2 style="color: black">Board's Cards: </h2>
<hr>
<h3>Awaiting tickets:</h3>
<div class="cards tick">
    <?php foreach ($tickets as $t) { ?>

        <div>
            <article class="tt ">
                <header>
                    <?= $t['subject'] ?>
                </header>

                <div class="content">
                    <a href="tickets.php?id=<?= $t['ticket_id'] ?>" style="float: right"></a>
                </div>
            </article>
            <a href="tickets.php?id=<?= $t['ticket_id'] ?>" style="float: right">see more</a>
        </div>
    <?php } ?>
</div>
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
    <?php foreach ($cards as $c) {

        ?>
        <article class="card" id="<?php echo $c['id']; ?>">
            <header>
                <span style="float: right; padding-left: 5px" onclick="deleteCard(<?php echo $c['id']; ?>)"> <i
                            class="icon-trash"></i></span>
                <span style="float: right;" onclick="editCard(<?php echo $c['id']; ?>)"><i class="icon-edit"></i></span>
                <h2><?php echo $c['title']; ?></h2>
            </header>
            <img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">
            <div class="content">
                <p><?php
                    echo $st = $c["description"];
                    ?></p>
                <label><b>Status: </b></label>
                <select name="activity-status" id="as-<?php echo $c['id']; ?>"
                        onclick="showActivities(<?php echo $c['id']; ?>)">
                    <option value="0">To Do</option>
                    <option value="1">In progress</option>
                    <option value="2">Done</option>
                    <option value="3">Overdue</option>
                </select>

            </div>
            <?php
            $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
            $sql = "select id, id_card, content,assignedTo as 'assignedTo', status, id_user, o.username, expected, priority from activities a
    inner  join ost_staff o on o.staff_id =a.assignedTo where id_card=? order by priority desc";
            $query = $link->prepare($sql);
            $query->bind_param("i", $c['id']);
            $query->execute();
            $res = $query->get_result();
            $activities = array();
            while ($row = mysqli_fetch_array($res)) {
                $activities[] = $row;
            }

            $query->close();
            $idc = $c['id'];
            $todo = "<div class='cont'  name='todo-$idc' >";
            $inprog = "<div class='cont'  style='display: none' name='inprog-$idc' >";
            $done = "<div class='cont'  style='display: none' name='done-$idc' >";
            $overdue = "<div class='cont'  style='display: none' name='over-$idc' >";
            foreach ($activities as $a) {

                switch ($a["status"]) {
                    case 0:
                        $todo .= activityContainer($a, "todo");
                        break;
                    case 1:
                        $inprog .= activityContainer($a, "in-progress");
                        break;
                    case 2:
                        $done .= activityContainer($a, "done");
                    case 3:
                        $overdue .= activityContainer($a, "overdue");
                }
            }
            echo "<br>" . $todo . "</div>";
            echo $inprog . "</div>";
            echo $done . "</div>";
            echo $overdue . "</div>";
            ?>
            <br>
            <button style="text-align: left; " onclick="addActivity(<?php echo $c['id'] ?>)"><i
                        class="icon-plus icon-2x" style="float: left">Add Activity</i>
            </button>
        </article>
    <?php } ?>
    <button id="newCard"><i class="icon-plus  icon-5x"> </i> <br>Add New Card</button>

</div>

<div class="dialog draggable ui-draggable size-normal" id="popup" style="top: 107.714px; left: 166px; display: none;">
    <div id="popup-loading" style="display: none;">
        <h1 style="margin-bottom: 20px; margin-top: 6px;"><i class="icon-spinner icon-spin icon-large"></i>
            Loading ...</h1>
    </div>
    <div class="body">
        <div id="the-lookup-form">
            <h3 class="drag-handle">Add New Activity</h3>
            <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>
            <hr>
            <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; Complete the form below to add a new activity.
                </p>
            </div>
            <div id="selected-org-info" style="display:none;margin:5px;">
                <form method="post" class="org" action="">
                    <input type="hidden" id="org-id" name="orgid" value="0">
                    <i class="icon-group icon-4x pull-left icon-border"></i>
                    <a class="action-button pull-right" style="overflow:inherit" id="unselect-org" href="#"><i
                                class="icon-remove"></i>
                        Add New Activity</a>
                    <div><strong id="org-name"></strong></div>
                    <div class="clear"></div>
                    <hr>
                    <p class="full-width">
    <span class="buttons pull-left">
        <input type="button" name="cancel" class="close" value="Cancel">
    </span>
                        <span class="buttons pull-right">
        <input type="submit" value="Continue">
    </span>
                    </p>
                </form>
            </div>
            <div id="new-org-form" style="display:block;">
                <form method="post" class="" action="" id="add-activity">
                    <?php
                    csrf_token();
                    ?>
                    <table width="100%" class="fixed">
                        <tbody>
                        <tr>
                            <td style="width:150px;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <em>
                                    <strong>Create New Activity</strong>:
                                    <div>Details on user activity</div>
                                </em>
                            </th>
                        </tr>
                        <tr>
                            <td class="multi-line required" style="min-width:120px;">
                                content:
                            </td>
                            <td>
                                <div id="boardinput" style="position:relative"><textarea name="context" id="context"
                                                                                         style="width:100%;height:100px;"></textarea>
                                    <span class="error">*</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="multi-line ">Assigned to</td>
                            <td>
                                <?php
                                $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
                                $query = $link->prepare("select username, staff_id, creator from ost_staff
inner join members m on ost_staff.staff_id = m.id_user
inner join repos r on m.id_repo = r.id
where (m.id_repo in (select r.id from repos r inner join boards b on b.id_repo =r.id
                                 where (b.id=?)))");
                                $query->bind_param("i", $_GET['idb']);
                                $query->execute();
                                $mem = $query->get_result();
                                $members = [];
                                while ($row = $mem->fetch_array(PDO::FETCH_LAZY))
                                    $members[] = $row;
                                $query->close();
                                ?>
                                <select title="assigned to" id="assigned-idd"
                                        disabled="<?php $creator = $members[0]['creator'];
                                        echo $thisstaff->getId() == $creator ?>">

                                    <?php
                                    if ($thisstaff->getId() == $creator)
                                        for ($i = 0; $i < count($members); $i++) {
                                            ?>
                                            <option value="<?php echo $members[$i]['staff_id']; ?>">
                                                <?php echo $members[$i]['username']; ?>
                                            </option>
                                        <?php } ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="required">Expected to be delivered :</td>
                            <td>
                                <input type="date" min="<?= date("Y-m-d"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="required">Priority:</td>
                            <td>
                                <select id="priority">
                                    <option value="LOW">Low</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="HIGH">High</option>
                                </select>
                            </td>
                        </tr>


                        </tbody>
                    </table>
                    <hr>
                    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="Reset">
            <input type="button" name="cancel" class="close" value="Cancel">
        </span>
                        <span class="buttons pull-right">
            <input type="submit" value="Add Activity">
        </span>
                    </p>
                </form>
            </div>
            <div class="clear"></div>
        </div>
        <script type="text/javascript">
            $(function () {
                var last_req;
                $('#org-search').typeahead({
                    source: function (typeahead, query) {
                        if (last_req) last_req.abort();
                        last_req = $.ajax({
                            url: "ajax.php/orgs/search?q=" + query,
                            dataType: 'json',
                            success: function (data) {
                                typeahead.process(data);
                            }
                        });
                    },
                    onselect: function (obj) {
                        $('#the-lookup-form').load(
                            'ajax.php/orgs/select/' + encodeURIComponent(obj.id)
                        );
                    },
                    property: "/bin/true"
                });

                $('a#unselect-org').click(function (e) {
                    e.preventDefault();
                    $('div#selected-org-info').hide();
                    $('div#new-org-form').fadeIn({
                        start: function () {
                            $('#org-search').focus();
                        }
                    });
                    return false;
                });

                $(document).on('click', 'form.org input.cancel', function (e) {
                    e.preventDefault();
                    $('div#new-org-form').hide();
                    $('div#selected-org-info').fadeIn({
                        start: function () {
                            $('#org-search').focus();
                        }
                    });
                    return false;
                });
            });
        </script>
    </div>
</div>
<div class="dialog draggable ui-draggable size-normal" id="popup-ticket"
     style="top: 107.714px; left: 166px; display: none;">
    <div id="popup-loading" style="display: none;">
        <h1 style="margin-bottom: 20px; margin-top: 6px;"><i class="icon-spinner icon-spin icon-large"></i>
            Loading ...</h1>
    </div>
    <div class="body">
        <div id="the-lookup-form">
            <h3 class="drag-handle">Add New Activity</h3>
            <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>
            <hr>
            <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; Complete the form below to add a new activity.
                </p>
            </div>
            <div id="selected-org-info" style="display:none;margin:5px;">
                <form method="post" class="org" action="">
                    <input type="hidden" id="org-id" name="orgid" value="0">
                    <i class="icon-group icon-4x pull-left icon-border"></i>
                    <a class="action-button pull-right" style="overflow:inherit" id="unselect-org" href="#"><i
                                class="icon-remove"></i>
                        Add New Activity</a>
                    <div><strong id="org-name"></strong></div>
                    <div class="clear"></div>
                    <hr>
                    <p class="full-width">
    <span class="buttons pull-left">
        <input type="button" name="cancel" class="close" value="Cancel">
    </span>
                        <span class="buttons pull-right">
        <input type="submit" value="Continue">
    </span>
                    </p>
                </form>
            </div>
            <div id="new-org-form" style="display:block;">
                <form method="post" class="" action="" id="">
                    <?php
                    csrf_token();
                    ?>
                    <table width="100%" class="fixed">
                        <tbody>
                        <tr>
                            <td style="width:150px;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <em>
                                    <strong>Create New Activity</strong>:
                                    <div>Details on user activity</div>
                                </em>
                            </th>
                        </tr>

                        <tr>
                            <td class="multi-line ">Assigned to</td>
                            <td>
                                <?php
                                $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
                                $query = $link->prepare("select username, staff_id, creator from ost_staff
inner join members m on ost_staff.staff_id = m.id_user
inner join repos r on m.id_repo = r.id
where (m.id_repo in (select r.id from repos r inner join boards b on b.id_repo =r.id
                                 where (b.id=?)))");
                                $query->bind_param("i", $_GET['idb']);
                                $query->execute();
                                $mem = $query->get_result();
                                $members = [];
                                while ($row = $mem->fetch_array(PDO::FETCH_LAZY))
                                    $members[] = $row;
                                $query->close();
                                ?>
                                <select title="assigned to" id="assigned-iddd"
                                        disabled="<?php $creator = $members[0]['creator'];
                                        echo $thisstaff->getId() == $creator ?>">

                                    <?php
                                    if ($thisstaff->getId() == $creator)
                                        for ($i = 0; $i < count($members); $i++) {
                                            ?>
                                            <option value="<?php echo $members[$i]['staff_id']; ?>">
                                                <?php echo $members[$i]['username']; ?>
                                            </option>
                                        <?php } ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="required">Card:</td>
                            <td>
                                <select title="" id="sl-cards">
                                    <?php foreach ($cards as $c) { ?>
                                        <option value="<?= $c['id'] ?>"> <?= $c['title'] ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="required">Expected to be delivered :</td>
                            <td>
                                <input type="date" min="<?= date("Y-m-d"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="required">Priority: </td>
                            <td class="required">
                                <select id="priority-ticket">
                                    <option value="LOW">Low</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="HIGH">High</option>
                                </select>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <hr>
                    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="Reset">
            <input type="button" name="cancel" class="close" value="Cancel">
        </span>
                        <span class="buttons pull-right">
            <input type="submit" value="Add Activity">
        </span>
                    </p>
                </form>
            </div>
            <div class="clear"></div>
        </div>
        <script type="text/javascript">
            $(function () {
                var last_req;
                $('#org-search').typeahead({
                    source: function (typeahead, query) {
                        if (last_req) last_req.abort();
                        last_req = $.ajax({
                            url: "ajax.php/orgs/search?q=" + query,
                            dataType: 'json',
                            success: function (data) {
                                typeahead.process(data);
                            }
                        });
                    },
                    onselect: function (obj) {
                        $('#the-lookup-form').load(
                            'ajax.php/orgs/select/' + encodeURIComponent(obj.id)
                        );
                    },
                    property: "/bin/true"
                });

                $('a#unselect-org').click(function (e) {
                    e.preventDefault();
                    $('div#selected-org-info').hide();
                    $('div#new-org-form').fadeIn({
                        start: function () {
                            $('#org-search').focus();
                        }
                    });
                    return false;
                });

                $(document).on('click', 'form.org input.cancel', function (e) {
                    e.preventDefault();
                    $('div#new-org-form').hide();
                    $('div#selected-org-info').fadeIn({
                        start: function () {
                            $('#org-search').focus();
                        }
                    });
                    return false;
                });
            });
        </script>
    </div>
</div>
<div class="dialog draggable ui-draggable size-normal" id="popup-edit"
     style="top: 107.714px; left: 166px; display: none;">
    <div id="popup-loading" style="display: none;">
        <h1 style="margin-bottom: 20px; margin-top: 6px;"><i class="icon-spinner icon-spin icon-large"></i>
            Loading ...</h1>
    </div>
    <div class="body">
        <div id="the-lookup-form">
            <h3 class="drag-handle">Edit Activity</h3>
            <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>
            <hr>
            <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; Complete the form below to edit the activity.</p>
            </div>
            <div id="selected-org-info" style="display:none;margin:5px;">
                <form method="post" class="org" action="">
                    <input type="hidden" id="org-id" name="orgid" value="0">
                    <i class="icon-group icon-4x pull-left icon-border"></i>
                    <a class="action-button pull-right" style="overflow:inherit" id="unselect-org" href="#"><i
                                class="icon-remove"></i>
                        Edit Activity</a>
                    <div><strong id="org-name"></strong></div>
                    <div class="clear"></div>
                    <hr>
                    <p class="full-width">
    <span class="buttons pull-left">
        <input type="button" name="cancel" class="close" value="Cancel">
    </span>
                        <span class="buttons pull-right">
        <input type="submit" value="Continue">
    </span>
                    </p>
                </form>
            </div>
            <div id="new-org-form" style="display:block;">
                <form method="post" class="" action="" id="add-activity">
                    <?php
                    csrf_token();
                    ?>
                    <table width="100%" class="fixed">
                        <tbody>
                        <tr>
                            <td style="width:150px;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <em>
                                    <strong>Edit Activity</strong>:
                                    <div>Details on user activity</div>
                                </em>
                            </th>
                        </tr>
                        <tr>
                            <td class="multi-line required" style="min-width:120px;">
                                content:
                            </td>
                            <td>
                                <div id="boardinput" style="position:relative"><textarea name="context" id="cnt"
                                                                                         style="width:100%;height:100px;"></textarea>
                                    <span class="error">*</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="multi-line ">Assigned to</td>
                            <td>

                                <select title="assigned to" id="assigned-id" style="width: 10rem">
                                    <?php
                                    if ($thisstaff->getId() == $creator)
                                        for ($i = 0; $i < count($members); $i++) {
                                            ?>
                                            <option value="<?php echo $members[$i]['staff_id']; ?>">
                                                <?php echo $members[$i]['username']; ?>
                                            </option>
                                        <?php }
                                     ?>

                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td>Expected to be delivered :</td>
                            <td>
                                <input type="date" min="<?= date("Y-m-d"); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="">
                                Status:
                            </td>
                            <td>
                                <select id="sl-status">
                                    <option value="0">To Do</option>
                                    <option value="1">In Progress</option>
                                    <option value="2">Done</option>
                                    <option value="3">Overdue</option>
                                </select>
                            </td>

                        </tr>

                        <td class="required">Priority:</td>
                        <td>
                            <select id="priority-edit">
                                <option value="LOW">Low</option>
                                <option value="MEDIUM">Medium</option>
                                <option value="HIGH">High</option>
                            </select>
                        </td>
                        </tbody>
                    </table>
                    <hr>
                    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="Reset">
            <input type="button" name="cancel" class="close" value="Cancel">
            <input type="button" class="warning" style="background-color : #f33535; color: white" value="Delete"
                   id="delete-activity">
        </span>
                        <span class="buttons pull-right">
            <input type="submit" value="Edit Activity">
        </span>
                    </p>
                </form>
            </div>
            <div class="clear"></div>
            <hr>
            <div style="padding: 1rem; font-size: larger"> Comments:</div>
            <i id="addComment" class="icon-comment" style="float: right"></i>
            <div class="comments">
                <div style="font-weight: bold">Samullel</div>
                <div style="padding: 0.8rem;">this is the ciodfn dsjssjsjdk</div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                var last_req;
                $('#org-search').typeahead({
                    source: function (typeahead, query) {
                        if (last_req) last_req.abort();
                        last_req = $.ajax({
                            url: "ajax.php/orgs/search?q=" + query,
                            dataType: 'json',
                            success: function (data) {
                                typeahead.process(data);
                            }
                        });
                    },
                    onselect: function (obj) {
                        $('#the-lookup-form').load(
                            'ajax.php/orgs/select/' + encodeURIComponent(obj.id)
                        );
                    },
                    property: "/bin/true"
                });

                $('a#unselect-org').click(function (e) {
                    e.preventDefault();
                    $('div#selected-org-info').hide();
                    $('div#new-org-form').fadeIn({
                        start: function () {
                            $('#org-search').focus();
                        }
                    });
                    return false;
                });

                $(document).on('click', 'form.org input.cancel', function (e) {
                    e.preventDefault();
                    $('div#new-org-form').hide();
                    $('div#selected-org-info').fadeIn({
                        start: function () {
                            $('#org-search').focus();
                        }
                    });
                    return false;
                });
            });
        </script>
    </div>
</div>
<?php

require_once(STAFFINC_DIR . 'footer.inc.php');
?>

<script>
    function deleteCard(id) {
        $.ajax({
            url: "ajax.php/cards/delete/" + id,
            type: "POST",
            success: function (data) {
                $("#" + id).remove();
            },
            error: function (data) {
                console.log("couldn't delete the card");
            }
        });


    }

    function createCardTemplate(id = 0, title = "", description = "") {
        return '<article class="card" id="' + id + '">' +
            '<h2>' + title + '</h2>' +
            '<header>' +
            '</header>' +
            '<img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">' +
            '<div class="content">' +
            '<p>' + description + '</p>' +
            '</div>' +
            '</article>';
    }

    function createFormTemplate(title = "", description = "") {
        return '<form id="new-card-form" method="post" action="">' +
            '<?php csrf_token(); ?>' +
            '<label for="title">Title: </label>' +
            '<br>' +
            ' <input type="text" name="title" id="title" placeholder="Title" value="' + title + '" >' +
            '<br>' +
            '<label for="description">Description</label>' +
            '<br>' +
            '<textarea name="description" style="width: 250px; height: 150px" id="description" placeholder="Description">' + description + '</textarea>' +
            '<input type="submit" value="Update">' +
            '<input type="button" id="cancel-add-card" value="Cancel">' +
            ' </form>';
    }

    function editCard(id) {
        let title = $("#" + id).children("header").children("h2").text();
        let decription = $("#" + id).children("div").children("p").text();
        let form = createFormTemplate(title, decription);
        let card = $("#" + id)
        let f = $(form)
        if (f.exists()) {
            f.children("#cancel-add-card").click(function () {
                f.replaceWith(card);
            })
            f.on("submit", function (e) {
                let form = this;
                e.preventDefault();
                let title = $("#title").val();
                let description = $("#description").val();
                console.log(title, description);
                $.ajax({
                    url: 'ajax.php/cards/edit/' + id,
                    type: 'POST',
                    data: {
                        title: title,
                        description: description
                    },
                    success: function (data) {
                        f.replaceWith(
                            '<article class="card" id="' + id + '">' +
                            '<header>' +
                            '<span  style="float: right; padding-left: 5px" onclick="deleteCard(' + id + ')"> <i class="icon-trash" ></i></span>' +
                            '<span style="float: right;" onclick=""><i class="icon-edit" onclick="editCard(' + id + ')" ></i></span>' +
                            '<h2>' + title + '</h2>' +
                            '</header>' +
                            '<img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">' +
                            '<div class="content">' +
                            '<p>' + description + '</p>' +
                            '</div>' +
                            '</article>'
                        );
                        location.reload();


                    },
                    error: function (data) {
                        console.log("could not add a card");
                    }
                });
            });
        }

        $("#" + id).replaceWith(f);
    }

    $(document).ready(() => {

        let form = '<form id="new-card-form" method="post" action="">' +
            '<?php csrf_token(); ?>' +
            '<label for="title">Title: </label>' +
            '<br>' +
            ' <input type="text" name="title" id="title" placeholder="Title">' +
            '<br>' +
            '<label for="description">Description</label>' +
            '<br>' +
            '<textarea name="description" style="width: 250px; height: 150px" id="description" placeholder="Description"></textarea>' +
            '<input type="submit" value="Add">' +
            '<input type="button" id="cancel-add-card" value="Cancel">' +
            ' </form>';
        $('#newCard').click(function () {
            if (!$("#new-card-form").exists()) {
                $('.cards:not(.tick)').before($(this)).append(form).append(this);
                $('#new-card-form').on("submit", function (e) {
                    let form = this;
                    e.preventDefault();
                    let title = $("#title").val();

                    let description = $("#description").val();
                    $.ajax({
                        url: 'ajax.php/cards/add/' + "<?php echo $_GET['idb']; ?>",
                        type: 'POST',
                        ContentType: 'application/x-www-form-urlencoded',
                        data: {
                            title: title,
                            description: description
                        },
                        success: function (data) {
                            let id = JSON.parse(data)[0]
                            $(form).remove();

                            $('.cards:not(.tick)').before($("#newCard")).append(
                                '<article class="card" id="' + id + '">' +
                                '<header>' +
                                '<span  style="float: right; padding-left: 5px" onclick="deleteCard(' + id + ')"> <i class="icon-trash" ></i></span>' +
                                '<span style="float: right;" onclick=""><i class="icon-edit" onclick="editCard(' + id + ')" ></i></span>' +
                                '<h2>' + title + '</h2>' +
                                '</header>' +
                                '<img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">' +
                                '<div class="content">' +
                                '<p>' + description + '</p>' +
                                '</div>' +
                                '<br>' +
                                '<button style="text-align: left; " onclick="addActivity(' + id + ')"><i class="icon-plus icon-2x" style="float: left">Add Activity</i></button>' +
                                '</article>'
                            ).append($("#newCard"));
                            console.log({
                                title: title,
                                description: description
                            });
                            location.reload();

                        },
                        error: function (data) {
                            console.log("could not add a card");
                        }
                    })
                });
            }
            $("#cancel-add-card").click(() => {
                console.log();
                $(this).parent().children("#new-card-form").remove();
            })

        });


    });

    /* function addActivity(id) {
         let form = '<form action="" method="post" class="form-add">' +
             '' +
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
     }*/

    let id_card = 0;

    function addActivity(id) {
        $("#popup").css('display', 'block').css("top", "120px");
        id_card = id;
    }

    /*$("select[name='activity-status']").onload(function () {
        let id=(this);
        console.log(id);
        $.ajax({
            url: "ajax.php/activities/display/"+id,
            type: "get",
            success: function (data) {
                let data_json= JSON.parse(data);
                console.log(data_json);
            },
            error: function (data){
                console.log("could not display activities");
            }
        })
    })*/
    const id_user = <?php echo $thisstaff->getId(); ?>;

    $(document).ready(function () {
        $("#add-activity").on("submit", function (e) {
            e.preventDefault();
            const content = $("#context").val();

            const form = $(this);
            const select = form.find("select");
            let assignedTo = id_user;
            const priority = form.find("#priority").val();
            console.log();
            if (!select.is("[disabled]"))
                assignedTo = select.val();
            const expected = form.find("input[type='date']").val();
            $.ajax({
                url: "ajax.php/activities/add/" + id_card,
                type: "post",
                data: {
                    content: content,
                    id_user: id_user,
                    assignedTo: assignedTo,
                    expected: expected,
                    priority: priority
                },
                success: function (data) {

                    $("#popup").css("display", "none")
                    //  console.log(data)
                    location.reload();
                },
                error: function (data) {
                    console.log("could not add an activity");
                }
            })

        })

    })

    function showActivities(id) {

        const st = $("#as-" + id).val();
        let todo = $("div[name='todo-" + id + "']");

        let inprog = $("div[name='inprog-" + id + "']");
        let done = $("div[name='done-" + id + "']");
        let overdue = $("div[name='over-" + id + "']");
        todo.css("display", "none");
        inprog.css("display", "none");
        done.css("display", "none");
        overdue.css("display", "none")
        switch (parseInt(st)) {
            case 0:
                todo.css("display", "block");
                break;
            case 1:
                inprog.css("display", "block");
                break;
            case 2:
                done.css("display", "block");
                break;
            case 3:
                overdue.css("display", "block");
                break;
            default:
                console.log("error " + st);

        }
    }

    const deleteComment = (id) => {
        fetch("ajax.php/comments/delete/" + id)
            .then(res => res.ok)
    }
    const creator = <?php echo $creator; ?>;
    const addCommentContainer = (data) => {
        const name = document.createElement("div");
        const comment = document.createElement("div");
        const cnt = document.createElement("div")
        const trash = document.createElement("i");
        const edit = document.createElement("i");
        cnt.id = "comment-" + data['id'];
        name.style.fontWeight = "bold";
        trash.classList.add("icon-trash");
        edit.classList.add("icon-edit");
        trash.style.float = "right";
        trash.style.margin = "0.5rem";
        edit.style.margin = "0.5rem";
        edit.style.float = "right"
        comment.style.padding = "0.8rem";
        comment.style.border = "solid"
        name.textContent = data['username'];
        comment.textContent = data['comment'];
        trash.onclick = function () {
            deleteComment(data['id']);
            $(this).parent().fadeOut(function (e) {
                $(this).remove();
            })
        };
        edit.onclick = function (e) {
            e.preventDefault();
            const {form, textarea} = createFormComment();

            form.children.item(3).onclick = function () {
                form.replaceWith(cnt);
            }
            form.children.item(0).textContent = (comment.innerText)
            form.onsubmit = function (e) {
                e.preventDefault();
                const com = form.children.item(0).value;
                data = {
                    id: data['id'],
                    comment: com,
                    id_user: id_user
                }

                fetch("ajax.php/comments/edit", {
                    method: "post",
                    headers: {
                        "content-type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                    .then(res => res.ok)
                    .then(data => {
                        cnt.children.item(3).textContent = com;
                        form.replaceWith(cnt)
                    })
                    .catch(err => console.log(err));
            }
            cnt.replaceWith(form);
        }
        if (data['id_user'] == id_user)
            cnt.append(name, edit, trash, comment)
        else
            cnt.append(name, comment);
        return cnt;
    }

    function createFormComment() {
        const form = document.createElement("form");
        const textarea = document.createElement("textarea");
        const submit = document.createElement("input")
        const cancel = document.createElement("input")

        textarea.classList.add("text-area-comment");
        submit.type = "submit";
        cancel.type = "button";
        cancel.value = "Cancel";
        form.appendChild(textarea);
        form.appendChild(document.createElement("br"))
        form.appendChild(submit);
        form.appendChild(cancel);
        cancel.onclick = function () {
            $(this).parent().remove();
        }
        return {form, textarea};
    }

    const commentForm = (ida, comments) => {
        const {form, textarea} = createFormComment();
        form.onsubmit = (e) => {
            data = {
                id_user: id_user,
                id_activity: ida,
                comment: textarea.value
            };

            e.preventDefault();
            fetch("ajax.php/comments/add", {
                method: "POST",
                headers: {
                    "content-type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(res => res.ok)
                .then(data => {
                    form.remove();
                    refreshComments(ida, comments)
                })
                .catch(err => console.log(err));
        }
        return form;

    }

    const refreshComments = (id, comments) => {
        comments.empty();
        fetch("ajax.php/comments/getComments/" + id, {
            method: 'get'
        }).then(res => res.json())
            .then(data => {

                for (let i = 0; i < data.length; i++) {
                    const div = addCommentContainer(data[i])
                    comments.append(div)

                }
            });

    }


    function openActivity(id, element, assignedTo, idc, priority) {
        //if (id_user == idc || id_user == creator)
        {
            const popup = $("#popup-edit");
            popup.css("display", "block").css("top", "120px");
            //$("#cnt").val(content);
            const p = $(element).children("p").text().replace(/^( Assignment: )/, "");
            const form = popup.find("form");
            $("#assigned-id").children("option[value='" + assignedTo + "']")
            const dt = $(element).find("div").text();
            let date = '';
            if (dt.indexOf("To Day") !== -1)
                date = new Date(Date.now()).toISOString().split("T")[0];
            else if (dt.indexOf("Tomorrow") !== -1) {
                date = new Date(Date.now() + 1).toISOString().split("T")[0];
            } else
                date = dt.match(/\d{4}-\d{2}-\d{2}/)[0];
            form.find("input[type='date']").val(date);
            const st = getStatus($(element))
            $("#sl-status").val(st);
            $("#priority-edit").val(priority)
            $("#cnt").text(p);

            $("#delete-activity").on("click", function (e) {
                e.preventDefault();
                fetch("ajax.php/activities/delete/" + id, {
                    method: "post"
                }).then(res => {
                    if (res.ok) {
                        console.log("res is ok");
                    }
                    res.json()
                })
                    .then(data => {
                        console.log(data);
                        popup.css("display", "none");
                        location.reload();
                    }).catch(err => {
                    console.log(err);
                })
            })

            const comments = $(".comments").empty();
            refreshComments(id, comments);
            $("#addComment").on("click", function (e) {
                e.preventDefault();
                //here

                if (!comments.find("form").exists())
                    comments.append(commentForm(id, comments))

            })
            form.on("submit", function (e) {
                e.preventDefault();
                let content = $("#cnt").val().replace(/^( Assignment: )/, "");
                let status = $("#sl-status").val();
                const assignedto = $("#assigned-id").val();
                const expected = $(this).find("input[type='date']").val();
                const priority = form.find("#priority-edit").val();
                console.log(priority);
                $.ajax({
                        url: "ajax.php/activities/edit/" + id,
                        type: 'POST',
                        data: {
                            content: content,
                            status: status,
                            //id_user: id_user,
                            assignedTo: assignedto,
                            expected: expected,
                            priority: priority
                        },
                        success: function (data) {
                            console.log(data);
                            popup.css("display", "none");
                            location.reload();
                        },
                        error: function (data) {
                            console.log("could not edit on activity" + data);
                        }
                    }
                );
            });
        }
    }


    const draggables = document.querySelectorAll('.drag')
    const containers = document.querySelectorAll('.cont')

    const getStatus = (d) => {
        const name = d.parent().attr("name");
        let status;
        if (name.match(/(todo)-\w*/))
            status = 0;
        else if (name.match(/(inprog)-\w*/))
            status = 1;
        else if (name.match(/(done)-\w*/))
            status = 2;
        else
            status = 3;
        return status;
    }
    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', () => {
            draggable.classList.add('dragging')
        })

        draggable.addEventListener('dragend', () => {
            const d = $(draggable);
            const idc = d.parent().parent().attr("id");
            const st = d.attr('onclick');
            const status = getStatus(d);
            const cl = {
                0: "todo",
                1: "in-progress",
                2: "done",
                3: "overdue"
            }
            const ida = st.substring(st.indexOf("(") + 1, st.indexOf(','));
            $.ajax({
                url: "ajax.php/activities/changeCard",
                type: "post",
                data: {
                    id_card: idc,
                    ida: ida,
                    status: status
                }
            });
            draggable.classList.remove('dragging')
            draggable.classList.remove("todo", "in-progress", "done", "overdue")
            draggable.classList.add(cl[status])

        })
    })

    containers.forEach(container => {
        container.addEventListener('dragover', e => {
            e.preventDefault()
            const afterElement = getDragAfterElement(container, e.clientY)
            const draggable = document.querySelector('.dragging')
            if (afterElement == null) {
                container.appendChild(draggable)
            } else {
                container.insertBefore(draggable, afterElement)
            }
        })
    })

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.drag:not(.dragging)')]

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect()
            const offset = y - box.top - box.height / 2
            if (offset < 0 && offset > closest.offset) {
                return {offset: offset, element: child}
            } else {
                return closest
            }
        }, {offset: Number.NEGATIVE_INFINITY}).element
    }

    $(document).ready(() => {
        $(".tick").on("click", function (e) {
            e.preventDefault();
            const popup = $("#popup-ticket");
            popup.css("display", "block").css("top", "150px");
            const form = popup.find("form")
            const div = $(this);
            const content = div.find('header').text().trim();
            const ticket_id = div.find("a").attr("href").match(/\d+/)[0];
            const priority= $("#priority-ticket").val();
            form.on("submit", e => {
                e.preventDefault();
                const assignedto = $("#assigned-iddd").val();
                const card = $("#sl-cards option:selected").val();
                const expected = form.find("input[type='date']").val();
                $.ajax({
                    url: "ajax.php/activities/add/" + card,
                    type: "post",
                    data: {
                        content: content,
                        assignedTo: assignedto,
                        expected: expected,
                        id_user: id_user,
                        ticket_id: ticket_id,
                        priority: priority
                    },
                    success: data => {
                        popup.css("display", "none");
                        console.log(data)
                        location.reload();
                    },
                    error: err => console.log(err)
                });


            })
        })
    })


</script>
<!--<script type="module">
    import interact from 'https://cdn.interactjs.io/v1.10.17/interactjs/index.js'
    interact('.container').dropzone({
        accept: ".drag",
        overlap: 0.7,
        ondropactiviate: (e)=> {
            e.target.classList.add('.dragging')
        },
        ondragenter: e => {
            var draggableElement = e.relatedTarget;
            var dropzoneElement = e.target;
            dropzoneElement.classList.add('drop-target');
            draggableElement.classList.add('can-drop');

        },
        ondragleave: e => {
            e.target.classList.remove('drop-target');
            e.relatedTarget.classList.remove('can-drop');
        },
        ondrop : e => {
            e.relatedTarget.text='Dropped';
        },
        ondropdeactivate: e => {
            e.target.classList.remove('drop-active');
            e.target.classList.remove('drop-target');
        }
    })
    interact('.drag-drop').draggable({
        inertia: true,
        modifier: [
            interact.modifier.restrictRect({
                restriction: 'parent',
                endOnly: true
            })
        ],
        autoScroll: true,
        listeners: {move : dragMoveListener }
    })
</script>-->