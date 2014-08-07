<?php
include_once("class.php");

$tw = new ProgramTV();
echo "<pre>";
print_r($tw->load());
echo "</pre>";
?>