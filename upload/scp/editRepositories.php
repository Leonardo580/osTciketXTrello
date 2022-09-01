<?php
$sql="";
require('staff.inc.php');
/*$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
if (!$link)
die( "Error: Unable to connect to MySQL." . PHP_EOL);*/
$sql = "select * from repos where id = " . $_GET['edit'];
$repo = db_query($sql);
$repo = mysqli_fetch_array($repo);
if (isset($_POST['title']) && isset($_POST['description'])) {
    if (!empty($_POST['title']) && !empty($_POST['description'])) {
        $sql = "update repos set
    title = '" . $_POST["title"] . "',description = '" . $_POST["description"] . "' where id = " . $_GET['edit'];

        db_query($sql);
    }
    //mysqli_close($link);
    header("Location: Repositories.php");

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
        <input type="text" id="title" name="title" value="<?php echo $repo['title'];?>">
        <br>
        <label for="description"><b>Description</b></label>
        <br>
        <textarea id="description" style="width: 90% ; height: 70px" name="description" ><?php echo $repo['description'];?></textarea>
        <br>
        <br>
        <input type="submit" value="update">
        <input type="button" value="cancel" onclick="javascript: window.location.href='http://localhost/osTicket/upload/scp/Repositories.php';">
    </form>
<?php
require_once(STAFFINC_DIR.'footer.inc.php');
?>