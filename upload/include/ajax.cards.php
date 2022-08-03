<?php
class CardsAjaxAPI extends AjaxController {
    function add($idb){
        $title= $_POST['title'];
        $descrition= $_POST['description'];
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        $sql= "insert into cards (id_board, title, description) values ($idb, '$title', '$descrition');";
        mysqli_query($link, $sql);
        $res=mysqli_query($link, "SELECT last_insert_id();");
        mysqli_close($link);
        return json_encode(mysqli_fetch_row($res));
    }

    public function delete($id){
        $link =mysqli_connect("localhost", "anas", "22173515", "osticket");
        $sql = "delete from cards where id=$id";
        mysqli_query($link, $sql);
        mysqli_close($link);
    }

    public function edit($id)
    {
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        $title=$_POST['title'];
        $description=$_POST['description'];
        $sql = "update cards set title=?, description=? where id=?";
        $query = mysqli_prepare($link,$sql );
        $query->bind_param("ssi", $title, $description, $id);
        $query->execute();
        $link->close();

    }
}
