<?php

function activate_gf($html)
{
$pattern = '|\{(\$gf.*)\}|isU';
preg_match_all( $pattern, $html, $matches );
//print_r ($matches);
foreach ($matches[0] AS $k=> $match){
  $replace = '<?= '.$matches[1][$k].' ?>';
  $html = str_replace($match, $replace, $html);
  }
return $html;
}


function reverse_string($array=[])
{
$string='';
$arr_rev = array_reverse($array);

foreach ($arr_rev as $k => $v){
   $string.= $v;
   }
return $string;
}

function cache_output($html_output, $file, $version='0.1', $dir=ROOT.'/Test/Views' )
{
  $success=false;
  $filename=$dir.'/v'.$version.'/'.$file.'.tpl.php';
  $success=file_put_contents ( $filename, $html_output );
//  chmod('/var/www/unsized/community.estate/Test/Views/v0.1', 0664);
  return $success;
  }


//Performance measurement tools below here

function performance_snapshot($webpage)
{
  $p['total_execution_time'] = number_format((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 4, '.', '');
  $p['memory']=round(memory_get_usage()/1024);
  $p['page_size']=string_KiB($webpage);
  $p['css_size']=string_KiB(scrape_css($webpage));
  return ($p);
}

function string_KiB($string)
{
  if (function_exists('mb_strlen')) {
      $size = mb_strlen( $string, '8bit');
    } else {
      $size = strlen($string);
      }
  $k_size= $size / 1024;
  $f_size = number_format($k_size, 1, '.', ',').'KiB';
  return $f_size;
  }

function scrape_css($html)  //used to calculate size of css file within html page.
  {
  libxml_use_internal_errors(true);
  $dom = new DOMDocument;
  $dom->loadHTML($html);
  $finder = new DOMXPath($dom);
  $styles = $finder->query('//style');
  $style=$styles[0]->nodeValue;
  return $style;
  }
