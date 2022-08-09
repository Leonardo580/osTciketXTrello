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

$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
$sql="select id, id_board, title, description from cards where id_board=".$_GET['idb'];
$result= mysqli_query($link, $sql);
$cards=array();
while ($row = mysqli_fetch_array($result)){
    $cards[]=$row;
}
mysqli_close($link);

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
        <?php foreach($cards as $c){

            ?>
        <article class="card" id="<?php echo $c['id']; ?>">
            <header>
                <span  style="float: right; padding-left: 5px" onclick="deleteCard(<?php echo $c['id'];?>)"> <i class="icon-trash" ></i></span>
                <span style="float: right;" onclick="editCard(<?php echo $c['id'];?>)" ><i class="icon-edit" ></i></span>
                <h2><?php echo $c['title']; ?></h2>
            </header>
            <img src="../assets/default/images/generic_image.jfif" alt="Hot air balloons">
            <div class="content">
                <p><?php
                    echo $st=$c["description"];
                    ?></p>
                <label><b>Status: </b></label>
                <select name="activity-status" id="as-<?php echo $c['id']; ?>" onclick="showActivities(<?php echo $c['id']; ?>)">
                    <option value="0">To Do</option>
                    <option value="1">In progress</option>
                    <option value="2">Done</option>
                </select>

            </div>
            <?php
                $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
                $sql = "select id, id_card, content, status, id_user, o.username from activities a
    inner  join ost_staff o on o.staff_id =a.id_user where id_card=?";
                $query = $link->prepare($sql);
                $query->bind_param("i", $c['id']);
                $query->execute();
                $res= $query->get_result();
                $activities =array();
                while ($row = mysqli_fetch_array($res)){
                    $activities[]=$row;
                }
                $query->close();
                $idc=$c['id'];
                $todo="<div class=''  name='todo-$idc'>";
                $inprog="<div class=''  style='display: none' name='inprog-$idc'>";
                $done="<div class=''  style='display: none' name='done-$idc'>";
                foreach ($activities as $a) {
                    $ida=$a['id'];
                    $content=$a['content'];
                    switch ($a["status"]){
                        case 0:
                            $todo.="<div class='activity' onclick='openActivity($ida)'>
<label class='box'>".$content."</label>
<label style='float: right'>"."assigned to : ".$a['username']."</label>
                                    </div><br>";
                            break;
                        case 1:
                            $inprog.="<div class='activity' onclick='openActivity($ida)'>
<label class='box'>".$a['content']."</label>
<label style='float: right'>"."assigned to : ".$content."</label>
                                    </div><br>";
                            break;
                        case 2:
                            $done.="<div class='activity' onclick='openActivity($ida)'>
<label class='box'>".$a['content']."</label>
<label style='float: right'>"."assigned to : ".$a['username']."</label>
                                    </div><br>";
                    }
                }
                echo "<br>".$todo."</div>";
                echo "<br>".$inprog."</div>";
                echo "<br>".$done."</div>";
                ?>
            <br>
            <button style="text-align: left; " onclick="addActivity(<?php echo $c['id'] ?>)"><i class="icon-plus icon-2x" style="float: left"></i>

                Add Activity </button>
        </article>
        <?php } ?>
        <button id="newCard"><i class="icon-plus  icon-5x"> </i> <br>Add New Card </button>

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
            <div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; Complete the form below to add a new activity.</p>
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
                            <td class="multi-line ">Assigned to </td>
                            <td>
                                <select>
                                    <option value="-1">Default</option>
                                    <option>user 1</option>
                                    <option>user 2</option>
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
<div class="dialog draggable ui-draggable size-normal" id="popup-edit" style="top: 107.714px; left: 166px; display: none;">
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
                            <td class="multi-line ">Assigned to </td>
                            <td>
                                <select>
                                    <option value="-1">Default</option>
                                    <option>user 1</option>
                                    <option>user 2</option>
                                </select>
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
            <input type="button" class="warning" style="background-color : #f33535; color: white" value="Delete" id="delete-activity">
        </span>
                        <span class="buttons pull-right">
            <input type="submit" value="Edit Activity">
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

require_once(STAFFINC_DIR.'footer.inc.php');
?>

<script  >
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
                $('.cards').before($(this)).append(form).append(this);
                $('#new-card-form').on("submit", function (e) {
                    let form = this;
                    e.preventDefault();
                    let title = $("#title").val();

                    let description = $("#description").val();
                    $.ajax({
                        url: 'ajax.php/cards/add/' + "<?php echo $_GET['idb']; ?>",
                        type: 'POST',
                        data: {
                            title: title,
                            description: description
                        },
                        success: function (data) {
                            let id = JSON.parse(data)[0]
                            $(form).remove();

                            $('.cards').before($("#newCard")).append(
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
                                '<br>'+
                                '<button style="text-align: left; " onclick="addActivity('+id+')"><i class="icon-plus icon-2x" style="float: left"></i>' +
                                '</article>'
                            ).append($("#newCard"));


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

    let id_card=0;
    function addActivity(id) {
        $("#popup").css('display', 'block');
        id_card=id;
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
    const id_user= <?php echo $thisstaff->getId(); ?>;

    $(document).ready(function (){
        $("#add-activity").on("submit", function (e) {
            e.preventDefault();
            let content = $("#context").val();

            $.ajax({
                url: "ajax.php/activities/add/"+id_card,
                type: "post",
                data: {
                    content: content,
                    id_user: id_user
                },
                success: function (data){

                    $("#popup").css("display", "none");
                    location.reload();
                },
                error: function (data){
                    console.log("could not add an activity");
                }
            })

    })

    })
    function showActivities(id) {

        const st=$("#as-"+id).val();
        let todo=$("div[name='todo-"+id+"']");

        let inprog=$("div[name='inprog-"+id+"']");
        let done=$("div[name='done-"+id+"']");
        todo.css("display", "none");
        inprog.css("display", "none");
        done.css("display", "none");
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
            default:
                console.log("error "+st);

        }
    }
    function openActivity(id) {
        const popup=$("#popup-edit");
        popup.css("display", "block").css("top", "120px");
        $("#cnt").val(content);

        const form =popup.find("form");
        $("#delete-activity").on("click", function (e) {
           e.preventDefault();
           fetch("ajax.php/activities/delete/"+id, {
               method: "post"
           }).then(res => {
               if (res.ok){
                   console.log("res is ok");
               }
               res.json()
           })
               .then (data => {
                   console.log(data);
                   popup.css("display", "none");
                   location.reload();
               }).catch(err => {
                   console.log(err);
               } )
        })
        form.on("submit", function(e){
            e.preventDefault();
            let content =$("#cnt").val();
            let status = $("#sl-status").val();
            $.ajax({
                    url: "ajax.php/activities/edit/" + id,
                    type: 'POST',
                data: {
                        content: content,
                    status: status,
                    id_user: id_user
                },
                success: function (data) {
                    console.log("success");
                    popup.css("display", "none");
                    location.reload();
                },
                error: function (data){
                        console.log("could not edit on activity");
                }
                }

            );
        });
    }
</script>
