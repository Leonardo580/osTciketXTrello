<?php
include_once("../include/mysqli.php");
$sql="";
if (isset($_POST['title']) && isset($_POST['description'])) {
    if (!empty($_POST['title']) && !empty($_POST['description'])) {
        $sql = "insert into repositories 
    (title, description, dateCreated) 
        values (" . $_POST["title"] . "," . $_POST["description"] . ", sysdate())";
        db_query($sql);
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
    <label><b>Description</b></label>
    <br>
    <textarea id="description" style="width: 90% ; height: 70px" name="description"></textarea>
    <br>
    <br>
    <input type="submit" value="submit">
</form>
<?php
require_once(STAFFINC_DIR.'footer.inc.php');
?>