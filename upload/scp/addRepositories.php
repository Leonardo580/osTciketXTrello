<?php

$sql="";

require('staff.inc.php');
if (isset($_POST['title']) && isset($_POST['description'])) {
    if (!empty($_POST['title']) && !empty($_POST['description'])) {
        $sql = "insert into repos 
    (title, description, dateCreated, creator) 
        values ('" . $_POST["title"] . "','" . $_POST["description"] . "', sysdate(),".$thisstaff->getId().")";
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        if (!$link)
            die( "Error: Unable to connect to MySQL." . PHP_EOL);
        mysqli_query($link, $sql);
        mysqli_close($link);
        header("Location: Repositories.php");
    }

}
require('staff.inc.php');
require_once(STAFFINC_DIR.'header.inc.php');
?>

<form method="post" action="">
    <?php
    csrf_token();
    ?>
    <label><b>Title</b></label>
    <br>
    <input type="text" id="title" name="title" value="">
    <br>
    <label for="description"><b>Description</b></label>
    <br>
    <textarea id="description" style="width: 90% ; height: 70px" name="description"></textarea>
    <br>
    <br>
    <input type="submit" value="submit">
    <input type="button" value="cancel" onclick="javascript: window.location.href='http://localhost/osTicket/upload/scp/Repositories.php';">
</form>
<?php
require_once(STAFFINC_DIR.'footer.inc.php');
?>