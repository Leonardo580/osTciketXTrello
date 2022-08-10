<?php
class Members  {
   static function getMembers($id){
        $sql= "select u.id, u.default_email_id, u.status, u.name, u.created, u.updated from ost_user u 
inner join members on u.id = members.id_user
inner join repos on members.id_repo = repos.id";
        $link=mysqli_connect("localhost", "anas", "22173515", "osticket");
        $result = mysqli_query($link, $sql);
        $members = array();
        while($row = mysqli_fetch_array($result)){
            $members[] = $row;
        }

        mysqli_close($link);

        return $members;
    }


}