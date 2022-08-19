<?php
require_once('staff.inc.php');
//require_once(INCLUDE_DIR.'class.export.php');

Export::saveCards();
header("refresh:0");
