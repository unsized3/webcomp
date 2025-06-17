<?php

//common template functions..
//eg layout has a function which creates html for selected menu item based upun script name / url

function is_last_seg($seg, $output='selected')
{
$result='';
$segs =  explode ( '/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)  );
$last_seg = end($segs);
if ($seg == $last_seg){
  $result = ' '.$output;
  }
return $result;
}
