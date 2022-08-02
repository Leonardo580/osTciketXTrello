<?php
class BoardsAjaxAPI extends AjaxController {
    function delete($id){
        $link= mysqli_connect("localhost", "anas", "22173515", "osticket");
        if (!$link)
            die( "Error: Unable to connect to MySQL." . PHP_EOL);
        $sql="delete from boards where id=$id";
        mysqli_query($link, $sql);

        mysqli_close($link);
    }
    function edit($id){
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        if (!$link)
            die( "Error: Unable to connect to MySQL." . PHP_EOL);
        if (!isset($_POST['title']))
            die( "Error: No title specified." . PHP_EOL);
        if (empty($_POST['title']))
            die( "Error: title is empty." . PHP_EOL);
        $title=$_POST['title'];
        $sql="update boards set title='$title' where id=$id";
        mysqli_query($link, $sql);
        mysqli_close($link);
        //return HttpResponse::status($res ? 200 : 500);
    }
    function add(){
        if (isset($_POST['title'])) {
            if (!empty($_POST['title'])) {
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        $idr = $_POST['idr'];
        $title = $_POST['title'];
        $sql = "insert into boards (id_repo, title) values ($idr,'$title' )";
        mysqli_query($link, $sql);
        mysqli_close($link);
        unset($_POST['board_title']);
    }}
}
}