<?php

function addPhpTags($template)
{
  //return template with php tags around variables.
  $template = str_replace('endforeach', '<?php endforeach; ?>', $template );
  $line = $template = str_replace('endif', '<?php endif ?>', $template );

  //regex easy way to ignore unwanted patterns https://www.rexegg.com/regex-best-trick.html#typical
  $var_pattern = '/foreach\(.{1,100}\):|if\(.{1,100}\):|(\s\$\w[\w|\d\-\>\[\]\(\)\']*\s)/isU';

  // match a variable
  $template = preg_replace_callback( $var_pattern,
        function ($matches) {
            if (!empty($matches[1])){
              return ( "<?=$matches[1]?>");
            }else {
              return ( "<?php $matches[0] ?>");
            }
          }, $template);

  return $template;
  }

function activate_theme_vars($html)
{
  //$var_pattern = '/foreach\(.{1,100}\):|if\(.{1,100}\):|(\s\$\w[\w|\d\-\>\[\]\(\)\']*\s)/isU';
  $pattern = '/\{fn.*\}|<!--.*-->|{{.*}}|(\s\$\w[\w|\d\-\>\[\]\(\)\'_]*[\s|;|}]+)/isU';  //match for vars in css
  preg_match_all( $pattern, $html, $matches );

  //$unique_matches = array_filter(array_unique($matches[1]));
  //print_r ($unique_matches);

  foreach ($matches[1] AS $k=> $match){

    $replace =' <?='.trim(mb_substr($match, 0, -1)).'?>'.substr($match, -1);

    $html = str_replace($match, $replace, $html);
    }
  return $html;
}

function activate_data_vars($html)
  {
    //omit fn and <!--
    //regex easy way to ignore unwanted patterns https://www.rexegg.com/regex-best-trick.html#typical
      $pattern = '/\{fn.*\}|<!--.*-->|\{(\$\w[\w|\d\-\>\[\]\(\)\'\?\"\s:\$\.]*)\}/isU';

      preg_match_all( $pattern, $html, $matches );

      $unique_matches = array_filter(array_unique($matches[1]));
      //print_r ($unique_matches);

      foreach ($unique_matches AS $k=> $match){
        $replace = '<?= '.$matches[1][$k].' ?>';
        $html = str_replace($matches[0][$k], $replace, $html);
        }
      return $html;
      }

function activate_cntl_fn($html)
{
  //echo $html;
  $pattern = '/<!--(.*)-->/isU';  // pattern for matching {$tpl-> }
  if (preg_match_all( $pattern, $html, $matches ) ){
  //  echo "Matches";
  //  print_r ($matches);

  $unique_matches = array_filter(array_unique($matches[1]));
  //print_r ($unique_matches);
  foreach ($unique_matches AS $k=> $match){
    $replace = '<?php '.$matches[1][$k].' ?>';
    $html = str_replace($matches[0][$k], $replace, $html);
    }
  }
  return $html;
}

function activate_fn($html)
{
  $pattern = '|\{fn(.*)\}|isU';  // pattern for matching {$gf-> }
  preg_match_all( $pattern, $html, $matches );
  //print_r ($matches);
  foreach ($matches[0] AS $k=> $match){
    $replace = '<?= '.$matches[1][$k].' ?>';
    $html = str_replace($match, $replace, $html);
    //$html = str_replace('$fn ',' ', $html);
    }
  return $html;
  }


function strip_mustache($html)
    {
      //echo $html;
      $pattern = '/{{(.*)}}/isU';  // pattern for matching {$tpl-> }
      if (preg_match_all( $pattern, $html, $matches ) ){
      //  echo "Matches";
      //  print_r ($matches);

      $unique_matches = array_filter(array_unique($matches[1]));
      //print_r ($unique_matches);
      foreach ($unique_matches AS $k=> $match){
        $replace = $matches[1][$k];
        $html = str_replace($matches[0][$k], $replace, $html);
        }
      }
      return $html;
    }



/* deprecated using activate data_vars_instead
function activate_gf($html)
{
  $pattern = '|\{(\$gf.*)\}|isU';  // pattern for matching {$gf-> }
  preg_match_all( $pattern, $html, $matches );
  //print_r ($matches);
  foreach ($matches[0] AS $k=> $match){
    $replace = '<?= '.$matches[1][$k].' ?>';
    $html = str_replace($match, $replace, $html);
    }
  return $html;
  }
*/




  function saveTemplate(string $template, string $template_file='')
    {
      //echo $template_file;
    /*
    if (empty($template_dir)){
      $template_dir = TEMPLATE_DIR;
      }
    */

    $dir_permission = 0774;
    //$template_file =  $template_dir.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
//echo "<h2>Dir</h2>";
     $dir = pathinfo( $template_file, PATHINFO_DIRNAME);
    //echo "<br>";
    if (!is_dir ($dir)){
    //  mkdir($dir, $dir_permission, true);
      }

    if ( substr(sprintf('%o', fileperms($dir)), -4) != decoct($dir_permission) ) {
      echo substr(sprintf('%o', fileperms($dir)), -4);
      echo "<br>CHANGING PERMISSIONS TO $dir $dir_permission<br>";
      chmod($dir, $dir_permission);
      }

    file_put_contents($template_file, $template);
    }

//$script_dir = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
//$dir = BASE.$script_dir.'/ListDetail';
//file_put_contents($dir.'/layout.css', compile_theme_vars($this->buildCss()) );

/* deprecated */
function outputTemplate($gf, $theme='', $template_dir='')
  {
    if (empty($template_dir)){
      $template_dir = TEMPLATE_DIR;
      }
    if (empty($theme)){
      $theme = THEME;
      }

    require(FRONT_END_DESIGN.'/variables/colours.php'); // loads $red $blue etc
    require(FRONT_END_DESIGN.'/variables/elevation.php'); // loads $z[ ]
    require(FRONT_END_DESIGN.'/mixins/typescale.php'); // loads tme to ypescale
    require($theme);
    extract ($t);
    $template_file = $template_dir.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
    ob_start();
    require $template_file;
    return $output = ob_get_clean();
    }


/*
function containerPartsHtml ( $gf,   string $filename, $theme = THEME, $dir = VIEWS, $minified = true)
{
  $min='';
  if ($minified){
    $min='.min';
    }
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
  require $theme;
  ob_start();//output the template
      require($dir.'/'.$filename.$min.'.tpl');
  return ob_get_clean();
}
*/


//prob deprecated
function compile_theme_vars($template, $theme='')
          {
          if (empty($theme)){
            $theme = THEME;
            }
          require_once(FRONT_END_DESIGN.'/variables/colours.php'); // loads $red $blue etc
          require_once(FRONT_END_DESIGN.'/variables/elevation.php'); // loads $z[ ]
          require_once(FRONT_END_DESIGN.'/mixins/typescale.php'); // loads tme to ypescale
          require_once($theme);
          extract ($t);//echo "***Theme***"; //echo THEME;
          $template = addPhpTags($template);
          $template = activate_gf($template); //replace {$gf }
          //save file
          //require file
          $compiled_template = require $template;
          return $compiled_template;
          }
