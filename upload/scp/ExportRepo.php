<?php
require('staff.inc.php');

function filterData(&$str){
    $str= preg_replace("/\t/", "\\t",$str);
    $str= preg_replace("/\r?\n/", "\\n",$str);
    if (strstr($str, ""))
        $str='"'.str_replace('"', '""', $str). '"';
}

$filename="activities-data_".date("Y-m-d").".xls";

$fileds=["ID", "Card", "user","content",  "status", "assigned to"];

$excelData=implode("\t", array_values($fileds))."\n";

$link=mysqli_connect("localhost","anas", "22173515", "osticket");
$res=mysqli_query($link, "select a.id, c.title, o.username as  'us', content, status, os.username as 'uss' from activities a
                                inner join cards c on a.id_card = c.id
                                inner join ost_staff o on staff_id=id_user
inner join ost_staff os on os.staff_id=assignedTo;");
$status=[0 => "To Do", 1 => "In Progress", 2 => "Done"];
if ($res->num_rows>0){

    while($row = mysqli_fetch_array($res)){
        $linedata=[$row['id'], $row['title'], $row['us'], $row['content'], $status[$row["status"]], $row["uss"]];
        array_walk($linedata, "filterData");
        $excelData.=implode("\t", array_values($linedata))."\n";
    }
}
else{
    $excelData.="No records were found"."\n";
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
echo $excelData;

exit();