<?php
class MembersAjaxAPI extends AjaxController {
    public function invite()
    {
        if (!isset($_POST['email']))
            die("an error occurred");
        if (!empty($_POST['email'])){
            $email=$_POST['email'];
            $subject = "you have been invited to a repository";
            $message = "You have been invited to a repository. Please click the link below to accept the invitation.\n\n";
            $token = sha1(uniqid($email, true));
            $message .= "http://localhost/osTicket/upload/scp/acceptInvitation.php?token=" . $token."&idr=".$_POST['idr'];
            $headers = "From: " . "noreply@osticket.com" . "\r\n";
            $link= mysqli_connect("localhost", "anas", "22173515", "osticket");
            $sql="select staff_id from ost_staff where email='".$email."'";
            $res=mysqli_query($link, $sql);
            $id=array();
            while ($row = mysqli_fetch_array($res)){
                $id[] =$row;
            }
            if (!$res){
                mysqli_close($link);
                return $this->json_encode("an error");
            }
        else {

                $idstaff=$id[0]['staff_id'];
                $query=$link->prepare("insert into pending_members (token, id_user, id_repo, tmstmp) values (?, ?, ?, ?)");
                $query->bind_param("siii", $token, $idstaff, $_POST['idr'], $_SERVER['REQUEST_TIME']);
                $query->execute();
                $query->close();
                //mail($email, $subject, $message, $headers);
                return $this->json_encode("done");
            }
        }
        return $this->json_encode("failed");
    }
}