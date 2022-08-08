<?php
require('staff.inc.php');
require_once(INCLUDE_DIR . 'class.task.php');

require_once(INCLUDE_DIR . 'class.export.php');
include "class.member.php";
//include "../include/class.email.php";

if (isset($_POST['email'])) {
    if (!empty($_POST['email'])) {
        // send email to user
        $email = $_POST['email'];
        $subject = "you have been invited to a repository";
        $message = "You have been invited to a repository. Please click the link below to accept the invitation.\n\n";
        $message .= "http://localhost/osTicket/upload/scp/acceptInvitation.php?email=" . $email;
        $headers = "From: " . "noreply@osticket.com" . "\r\n";
        //mail($email, $subject, $message, $headers);
        //mail("anasbenbrahim9@gmail.com", "eeee", "ddd", "From: noreply@test.tn");
        Mailer::sendmail($email, $subject, $message, $headers);
    }
}


//if (isset($_POST['']))
$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");

$inc = "RepoTable.php";
$nav->setTabActive('Repositories');
$open_name = _P('queue-name',
    /* This is the name of the open tasks queue */
    'Open');
require_once(STAFFINC_DIR . 'header.inc.php');
$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
if (!$link)
    die("Error: Unable to connect to MySQL." . PHP_EOL);
$sql = "select * from boards where id = " . $_GET['idr'];
$result = mysqli_query($link, $sql);
$repository = mysqli_fetch_array($result);
mysqli_close($link);
?>
<!--<a href="AddBoard.php?idr=--><?php //echo $_GET['idr']?><!--"> <button style="float: right"><i class="icon-plus"></i></button></a>-->

<!--<button type="button" class="btn btn-secondary" id="addBoard" style="float: left;" data-container="body"
        data-toggle="popover" data-placement="bottom" data-content="">
    <i class="icon-plus"></i>
</button>-->
<button id="addnew" style="float: right;"><i class="icon-plus"></i></button>
<h1>Title: <b><?php echo $repository['title']; ?></b></h1>
<div class="content">
    <p>Description :
        <?php echo $repository['description']; ?>
    </p>
</div>
<form method="post" action="">
    <?php
    csrf_token();
    ?>
    <label>Invite members via email</label>
    <input id="id_staff" type="hidden" name="id_staff" value="<?php echo $thisstaff->getId(); ?>">
    <input id="email" name="email" type="email" placeholder="email">
    <input type="submit" value="Invite">
</form>
<br>
<link rel="stylesheet" href="css/Members.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css"
      integrity="sha256-2XFplPlrFClt0bIdPgpz8H7ojnk10H69xRqd9+uTShA=" crossorigin="anonymous"/>
<div class="container mt-3 mb-4">
    <div class="col-lg-9 mt-4 mt-lg-0">
        <div class="row">
            <div class="col-md-12">
                <div class="user-dashboard-info-box table-responsive mb-0 bg-white p-4 shadow-sm">
                    <!--                        change starts here -->
                    <table class="table manage-candidates-top mb-0">
                        <thead>
                        <tr>
                            <th>Member Name</th>
                            <th class="text-center">Status</th>
                            <th class="action text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        //$members = Members::getMembers($_GET['idr']);
                        $sql = "select u.id, u.default_email_id, u.status, u.name, u.created, u.updated from ost_user u 
inner join members on u.id = members.id_user
inner join repos on members.id_repo = repos.id";
                        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
                        $result = mysqli_query($link, $sql);
                        $members = array();
                        while ($row = mysqli_fetch_array($result)) {
                            $members[] = $row;
                        }

                        mysqli_close($link);


                        foreach ($members as $m) { ?>
                            <tr class="candidates-list">

                                <td class="title">
                                    <div class="thumb">
                                        <img class="img-fluid" src="https://bootdey.com/img/Content/avatar/avatar7.png"
                                             alt="">
                                    </div>
                                    <div class="candidate-list-details">
                                        <div class="candidate-list-info">
                                            <div class="candidate-list-title">
                                                <h5 class="mb-0"><a href="#"><?php echo $m['name'] ?></a></h5>
                                            </div>
                                            <div class="candidate-list-option">
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-filter pr-1"></i>Information Technology</li>
                                                    <li><i class="fas fa-map-marker-alt pr-1"></i>Rolling Meadows, IL
                                                        60008
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="candidate-list-favourite-time text-center">
                                    <a class="candidate-list-favourite order-2 text-danger" href="#"><i
                                                class="fas fa-heart"></i></a>
                                    <span class="candidate-list-time order-1"><?php echo $m['status'] ?></span>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0 d-flex justify-content-end">
                                        <li><a href="#" class="text-primary" data-toggle="tooltip" title=""
                                               data-original-title="view"><i
                                                        class="far fa-eye"></i></a></li>
                                        <li>
                                            <a href="deleteMember.php?id=<?php echo $m['id']; ?>&idr=<?php echo $_GET['idr']; ?>"
                                               class="text-danger" data-toggle="tooltip" title=""
                                               data-original-title="Delete"><i
                                                        class="far fa-trash-alt"></i></a></li>
                                    </ul>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <!--                        changes ends here-->
                    <!--<div class="text-center mt-3 mt-sm-3">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item disabled"> <span class="page-link">Prev</span> </li>
                            <li class="page-item active" aria-current="page"><span class="page-link">1 </span> <span class="sr-only">(current)</span></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">25</a></li>
                            <li class="page-item"> <a class="page-link" href="#">Next</a> </li>
                        </ul>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
</div>
<div class="dialog draggable ui-draggable size-normal" id="popup" style="top: 107.714px; left: 166px; display: none;">
    <div id="popup-loading" style="display: none;">
        <h1 style="margin-bottom: 20px; margin-top: 6px;"><i class="icon-spinner icon-spin icon-large"></i>
            Loading ...</h1>
    </div>
    <div class="body">
        <div id="the-lookup-form">
            <h3 class="drag-handle">Add New Board</h3>
            <b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>
            <hr>
            <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; Complete the form below to add a new Board.</p>
            </div>
            <div id="selected-org-info" style="display:none;margin:5px;">
                <form method="post" class="org" action="">
                    <input type="hidden" id="org-id" name="orgid" value="0">
                    <i class="icon-group icon-4x pull-left icon-border"></i>
                    <a class="action-button pull-right" style="overflow:inherit" id="unselect-org" href="#"><i
                                class="icon-remove"></i>
                        Add New Board</a>
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
                <form method="post" class="" action="">
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
                                    <strong>Create New Board</strong>:
                                    <div>Details on user Board</div>
                                </em>
                            </th>
                        </tr>
                        <tr>
                            <td class="multi-line required" style="min-width:120px;">
                                Title:
                            </td>
                            <td>
                                <div id="boardinput" style="position:relative"><input type="text" id="_159b9ba6d25cf8"
                                                                                      size="40"
                                                                                      maxlength="64" placeholder=""
                                                                                      name="board_title">
                                    <span class="error">*</span>
                                </div>
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
            <input type="submit" value="Add Board">
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
<?php
$link = mysqli_connect("localhost", "root", "", "osticket");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$idr = $_GET['idr'];
$sql = "select * from boards where id_repo=$idr;";
$result = mysqli_query($link, $sql);
$boards = array();
while ($row = mysqli_fetch_array($result)) {
    $boards[] = $row;
}

?>
<br>
<br>
<section class="">Boards</section>
<hr>
<br> <div class="album py-5 bg-light">
<div class="container">
    <div class="row" >
    <?php foreach ($boards as $b) { ?>

            <div class="two-column" style="display: flex;">

                    <div class="card shadow-sm" id="<?php echo $b['id'] ?>">
                        <!--<svg class="bd-placeholder-img card-img-top" width="100%" height="225"
                             xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail"
                             preserveAspectRatio="xMidYMid slice" focusable="false"><title> Placeholder</title>
                            <rect width="100%" height="100%" fill="#55595c" data-darkreader-inline-fill=""
                                  style="--darkreader-inline-fill:#43484b;"></rect>
                            <text x="50%" y="50%" fill="#eceeef" dy=".3em" data-darkreader-inline-fill=""
                                  style="--darkreader-inline-fill:#dddad6;">Board
                            </text>
                        </svg>-->
                        <img src="../assets/default/images/generic_image.jfif" alt="board image" class="img-board">

                        <div class="card-body">
                            <p class="card-text"><strong><?php echo $b['title']; ?></strong></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="Cards.php?idb=<?php echo $b['id'] ?>"><button type="button" class="btn btn-sm btn-outline-secondary">View</button></a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" name="editBoard">Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="deleteBoard(<?php echo $b['id']; ?>)">Delete
                                    </button>
                                </div>
                                <small class="text-muted"><?php
                                    $since = new DateTime($b['created']);
                                    echo $since->diff(new DateTime())->format('%d days ago ');
                                    echo $b['dateCreated'] ?></small>
                            </div>
                        </div>
                    </div>

            </div>


    <?php } ?>
    </div>
    </div>

</div>
<?php
require_once(STAFFINC_DIR . 'footer.inc.php');
?>

<script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js"></script>
<script>
    /*tippy('#addnew',
        {
            content: "" +
                "<form method='post' action=''>" +
                "<label>Title of the card</label>" +
                '<input type="text" name="title" placeholder="title">' +
                "<input type='submit' value='Add Board'>" +
                "<input type='button' value='Cancel' onclick=''>" +
                "</form>",
            placement: 'bottom',
            arrow: true,
            trigger: 'click',
            allowHTML: true,
            hide: false
        }
    );*/

    $(document).ready(function () {
        $('#addnew').click(function () {
            let idr =<?php echo $_GET['idr']; ?>;
            $("#popup").css("display", "block");
            let form = $("#popup").children(".body").children("#the-lookup-form")
                .children("#new-org-form").children("form");
            form.on("submit", function () {
                let title = $(this).children("table").children("tbody")
                    .children("tr").children("td")
                    .children("#boardinput")
                    .children("#_159b9ba6d25cf8")
                    .val();

                $.ajax({
                    url: "ajax.php/boards/add",
                    type: "POST",
                    data: {title: title, idr: idr},
                    success: function (data) {
                        $("#popup").css("display", "none");

                    },
                    error: function (data) {
                        console.log("add board error");
                    },
                });
                return false;
            });
        })


        $("button[name='editBoard']").click(function () {
            let titleElement = $(this).parent().parent().parent().children("p").children();
            let title = titleElement.text();
            $("#popup").css("display", "block");
            let form = $("#popup").children(".body").children("#the-lookup-form")
                .children("#new-org-form").children("form");
            t = form.children("table").children("tbody")
                .children("tr").children("td")
                .children("#boardinput")
                .children("#_159b9ba6d25cf8");
            t.attr("value", title);
            id = $(this).parent().parent().parent().parent().attr("id");
            form.on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                    url: "ajax.php/boards/edit/" + id,
                    type: "POST",
                    cache: false,
                    data: {
                        title: t.val()
                    },
                    success: function (data) {
                        $("#popup").css("display", "none");
                        console.log(data);
                        titleElement.text(t.val());
                    },
                    error: function (data) {
                        console.log("edit failed");
                    }
                })
            });


        });

    });

    /*let content= document.getElementById("addBoard");
    content.addEventListener("click", function(){
        let form ="" +
            "<form method='post' action=''>" +
            "<label>Title of the card</label><br>" +
            '<input type="text" name="title" placeholder="title">' +
            "<input type='submit' value='Add Board'>"+
            "<input type='button' value='Cancel' onclick=''>"+
            "</form>"
        this.setAttribute("data-content", form);
    });
*/
</script>
<!-- JavaScript Bundle with Popper -->

<script>
    function deleteBoard(id) {

        $(document).ready(function () {
            $.ajax({
                url: "ajax.php/boards/" + id + "/delete/",
                type: "POST",
                success: function (data) {
                    $('#' + id).fadeOut("normal", function () {
                        $(this).remove();
                    })

                },
                error: function () {
                    console.log("task failed successfully !!!");
                }

            });


        });
    }
</script>