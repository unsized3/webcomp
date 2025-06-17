<?php

function load_container_parts ( $serialised_file,  $tpl_ext= '.tpl')
{

$html='';

  $containerParts = unserialize( file_get_contents($serialised_file) );

  $partsList = \unsized\webcomp\PartsList::getInstance();//load partsList Singleton
  //load container parts
  foreach ($containerParts AS $type => $record){
    foreach ($record AS $key => $value){
      $addFn = 'add'.$type;
      $partsList->$addFn($key, $value );
      }
    }

$template = substr($serialised_file, 0, strrpos($serialised_file, '.', '-1')).$tpl_ext;
$html = file_get_contents($template);
return $html;
}


//loads container parts (previously serialized)
function containerPartsHtml ( $gf,   string $filename, $theme = THEME, $dir = VIEWS, $minified = true)
{
echo '<p>containerPartsHtml is Deprecated - use load_container_parts($serialized_file) instead.   ***<p/>';


  $min='';
  if ($minified){
    $min='.min';
    }

//echo $dir.'/'.$filename.'.ser<br>';
  $containerParts = unserialize( file_get_contents($dir.'/'.$filename.'.ser') );

  $partsList = \unsized\webcomp\PartsList::getInstance();//load partsList Singleton
  //load container parts
  foreach ($containerParts AS $type => $record){
    foreach ($record AS $key => $value){
      $addFn = 'add'.$type;
      $partsList->$addFn($key, $value );
      }
    }

  //render blank_layout
  //require $theme;
  //ob_start();//output the template
  //    require($dir.'/'.$filename.$min.'.tpl');
  //return ob_get_clean();
}
