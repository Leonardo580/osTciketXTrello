<?php
class ActivitiesAjaxAPI extends AjaxController{
    public function add($idc)
    {
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query =$link->prepare("insert into activities (id_card, content, status, id_user, assignedTo) values (?,?,?,?,?);");
        $status=0;
        $id_user=intval($_POST['id_user']);
        $query->bind_param("isiii",$idc, $_POST["content"], $status, $id_user, $_POST['assignedTo']);
        $query->execute();
        mysqli_close($link);
        return $this->json_encode("cannot add an activity");

    }
    public function delete($id){
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query=$link->prepare("delete from activities where id=?");
        $query->bind_param("i",$id);
        $query->execute();
        $query->close();
    }
    public function edit($id){
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        $content = $_POST['content'];
        $status = $_POST['status'];
        $query= $link->prepare("update activities set content=?, status=? , id_user=? , assignedTo=? where id=?");
        $id_user=1;
        $query->bind_param("siiii", $content, $status, $id_user,$_POST['assignedTo'], $id);
        $query->execute();
        $query->close();
    }
    public function display($id): array{
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query=$link->prepare("select id, id_card, content, status, assignedTo from activities where id_card=?");
        $query->bind_param("i", $id);
        $res=$query->get_result();
        $query->close();
        $activities = array();
        while ($row= mysqli_fetch_array($res)){
            $activities[]=$row;
        }
        return $activities;

    }

}