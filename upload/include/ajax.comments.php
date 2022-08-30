<?php

class CommentsAjaxAPI extends AjaxController
{
    public function getComments($id_activity)
    {
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query = $link->prepare("select id, username,  comment, id_user from comments 
                            inner join ost_staff os on comments.id_user = os.staff_id where  id_activity=? order by dateCreated");
        $query->bind_param("i", $id_activity);
        $query->execute();
        $res = $query->get_result();
        $comments = [];
        while ($row = $res->fetch_assoc()) {
            $comments[] = $row;
        }
        $query->close();
        return $this->json_encode($comments);
    }

    public function add()
    {
        $data=json_decode(file_get_contents("php://input"), true);
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query = $link->prepare("insert into comments (id_user, id_activity, comment) value(?,?,?)");
        $query->bind_param('iis', $data['id_user'], $data['id_activity'],$data['comment']);
        $query->execute();
        $query->close();
    }

    public function delete($id)
    {
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query = $link->prepare("delete from comments where id=?");
        $query->bind_param('i', $id);
        $query->execute();
        $query->close();
    }

    public function edit()
    {
        $data=json_decode(file_get_contents("php://input") , true);
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        $query=$link->prepare("update comments set comment=? where id=? and id_user=?");
        $query->bind_param("sii", $data['comment'], $data['id'], $data['id_user']);
        $query->execute();
    }

}