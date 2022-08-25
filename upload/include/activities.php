<?php
function activityContainer($a=array(), $class=""): string{
    $ida = $a['id'];
    $content = $a['content'];
    $assignedTo= $a['assignedTo'];
    $expected =$a['expected'];
    $idc=$a['id_user'];
    $username=$a['username'];
    $p='"'.$a['priority'].'"';
    $priority =ucfirst(strtolower($a['priority']));
    $d = date_diff(date_create($expected), date_create())->d;
    if ($d ==1)
        $expected="Tomorrow";
    elseif ($d <1)
        $expected="To Day";
    return "<div class='activity drag $class' draggable='true' onclick='openActivity($ida, this,$assignedTo, $idc ,$p)'>
<label style='float: right'>" . "assigned to : " . $username . "</label>
<p class=''> Assignment: <b>" . $content . "</b></p>
<div>Expected: ".$expected."</div>
<div>Priority :".$priority."</div>
                                    </div><br>";
}