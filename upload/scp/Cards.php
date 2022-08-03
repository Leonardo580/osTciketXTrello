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
$sql="select id, id_board, title, description from cards";
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
    <div class="cards">
        <?php foreach($cards as $c){

            ?>
        <article class="card" id="<?php echo $c['id']; ?>">
            <header>
                <span  style="float: right; padding-left: 5px" onclick="deleteCard(<?php echo $c['id'];?>)"> <i class="icon-trash" ></i></span>
                <span style="float: right;" onclick="editCard(<?php echo $c['id'];?>)" ><i class="icon-edit" ></i></span>
                <h2><?php echo $c['title']; ?></h2>
            </header>
            <img src="../assets/default/images/generic_card.jpg" alt="Hot air balloons">
            <div class="content">
                <p><?php
                    echo $st=$c["description"];
                    ?></p>
            </div>

        </article>
        <?php } ?>
        <button id="newCard"><i class="icon-plus  icon-5x"> </i> <br>Add New Card </button>

    </div>



<?php

require_once(STAFFINC_DIR.'footer.inc.php');
?>

<script>
    function deleteCard(id){
            $.ajax({
                url : "ajax.php/cards/delete/"+id,
                type: "POST",
                success: function(data){
                    $("#"+id).remove();
                },
                error: function(data){
                    console.log("couldn't delete the card");
                }
            });




    }
    function createCardTemplate(id=0, title="", description="") {
        return  '<article class="card" id="'+id+'">' +
            '<header>' +
            '<h2>' + title + '</h2>' +
            '</header>' +
            '<img src="../assets/default/images/generic_card.jpg" alt="Hot air balloons">' +
            '<div class="content">' +
            '<p>' + description + '</p>' +
            '</div>' +
            '</article>';
    }
    function createFormTemplate(title = "", description = "") {
        return '<form id="new-card-form" method="post" action="">'+
            '<?php csrf_token(); ?>'+
            '<label for="title">Title: </label>' +
            '<br>' +
            ' <input type="text" name="title" id="title" placeholder="Title" value="'+title+'" >' +
            '<br>' +
            '<label for="description">Description</label>' +
            '<br>' +
            '<textarea name="description" style="width: 250px; height: 150px" id="description" placeholder="Description">'+description+'</textarea>' +
            '<input type="submit" value="Update">' +
            '<input type="button" id="cancel-add-card" value="Cancel">' +
            ' </form>';
    }

    function editCard(id) {
        let title=$("#"+id).children("header").children("h2").text();
        let decription=$("#"+id).children("div").children("p").text();
        let form=createFormTemplate(title, decription) ;
        let card=$("#"+id)
        let f=$(form)
        if (f.exists()){
            f.children("#cancel-add-card").click(function (){
                f.replaceWith(card);
            })
            f.on("submit", function (e){
                let form=this;
                e.preventDefault();
                let title = $("#title").val();
                let description = $("#description").val();
                console.log(title, description);
                $.ajax({
                    url: 'ajax.php/cards/edit/'+id,
                    type: 'POST',
                    data: {
                        title: title,
                        description: description
                    },
                    success: function (data) {
                        f.replaceWith(
                            '<article class="card" id="'+id+'">' +
                            '<header>' +
                            '<span  style="float: right; padding-left: 5px" onclick="deleteCard('+id+')"> <i class="icon-trash" ></i></span>'+
                            '<span style="float: right;" onclick=""><i class="icon-edit" onclick="editCard('+id+')" ></i></span>'+
                            '<h2>' + title + '</h2>' +
                            '</header>' +
                            '<img src="../assets/default/images/generic_card.jpg" alt="Hot air balloons">' +
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

        $("#"+id).replaceWith(f);
    }
$(document).ready(()=> {

    let form='<form id="new-card-form" method="post" action="">'+
        '<?php csrf_token(); ?>'+
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
    $('#newCard').click(function (){
        if (!$("#new-card-form").exists()) {
            $('.cards').before($(this)).append(form).append(this);
            $('#new-card-form').on("submit", function (e)  {
                let form=this;
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
                        let id=JSON.parse(data)[0]
                        $(form).remove();
                        // TODO: add id to card
                        $('.cards').before($("#newCard")).append(
                            '<article class="card" id="'+id+'">' +
                            '<header>' +
                            '<span  style="float: right; padding-left: 5px" onclick="deleteCard('+id+')"> <i class="icon-trash" ></i></span>'+
                            '<span style="float: right;" onclick=""><i class="icon-edit" onclick="editCard('+id+')" ></i></span>'+
                            '<h2>' + title + '</h2>' +
                            '</header>' +
                            '<img src="../assets/default/images/generic_card.jpg" alt="Hot air balloons">' +
                            '<div class="content">' +
                            '<p>' + description + '</p>' +
                            '</div>' +
                            '</article>'
                        ).append($("#newCard"));



                    },
                    error: function (data) {
                        console.log("could not add a card");
                    }
                })
            });
        }
        $("#cancel-add-card").click(() =>{
            console.log();
            $(this).parent().children("#new-card-form").remove();
        })

    });


    });


</script>
