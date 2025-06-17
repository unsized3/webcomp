<?php


function appendHTML ( $htmlContent, $htmlContainer, $id = 'content', $method='replace', $fn= 'getElementById', $section='body')
{
  //https://codereview.stackexchange.com/questions/94169/use-dom-and-xpath-to-make-some-changes-in-html-document
  $domContent = new \DOMDocument;
  $domContent->formatOutput = true;
  libxml_use_internal_errors(true);
  $domContent->loadHTML($htmlContent);    //auto adds html and body tags -> see xpath below;
  $xpath = new \DomXPath($domContent);

  $query = '//'.$section.'/*';
  $nodelist = $xpath->query($query);  //load all nodes from html
  $nodeContent = $nodelist->item(0);  //htmlContent is a node
//print_r ($nodelist);
  //$nodeContent = $nodelist;
  //print_r ($nodeContent);
  $domContainer = new \DOMDocument;    // get the source html.
  $domContainer->loadHTML($htmlContainer);

  if ($fn == 'getElementByTagName'){
    $placeholderTag = $domContainer->getElementsByTagName( $id )->item(0);
  }elseif ($fn == 'getElementById'){
    $placeholderTag = $domContainer->getElementById( $id );
  }else {
    echo '<br>***Error  fn must be getElementByTagName or getElementById';
  }
  //echo $id;
  //print_r ($placeholderTag);

  $nodeContent = $domContainer->importNode($nodeContent, true);  //import into container node

  switch ($method)
  {
    case 'replace':
      $placeholderTag->parentNode->replaceChild( $nodeContent, $placeholderTag);
    break;
    case 'append':
      $placeholderTag->appendChild( $nodeContent);
    break;

    case 'prepend':
      $placeholderTag->parentNode->insertBefore( $nodeContent, $placeholderTag);
    break;
  }
  return $outputHtml = html_entity_decode($domContainer->saveHTML());
}


function replaceSlot($id, $content, $container)
{
  $needle =  "<slot id='".$id."'></slot>";
  return str_replace($needle, $content, $container);
}


function appendStyle($style, $htmlContainer, $theme = THEME, $css_file='')
  {
    if (empty ($css_file)){
      //echo $_SERVER['PHP_SELF'];

      //$css_file = VIEWS.'/'.str_replace( '.php', '.css', $_SERVER['PHP_SELF']);
      $css_file = VIEWS.str_replace( '.php', '.css', $_SERVER['PHP_SELF']);
      }

    //append dialogue css
    //$style = $render->concatParts( $partsList->getCss() );
    //file_put_contents( $css_file, addPhpTags($style) );
    file_put_contents( $css_file, $style );

    require $theme;
    ob_start();
      require $css_file;
    $style = ob_get_clean();
    $html = str_replace('</style>', $style.'</style>', $htmlContainer);

    return $html;
  }


function prepend ($html_content, $html_container)
{
 preg_match('/<.+?>/', $html_container, $open_module_tag);
 //echo $open_module_tag[0];
 //replace first occurance of open module tag -> put content just after it.
 $pos = strpos($html_container, $open_module_tag[0]);
 if ($pos !== false) {
     return substr_replace($html_container, $open_module_tag[0].$html_content, $pos, strlen($open_module_tag[0]));
 }

 return str_replace($open_module_tag[0], $open_module_tag[0].$html_content, $html_container);
}


//deprecated as saveHTML gives strange results when using template tags. use replaceSlot instead
function slotInHTML($html, $htmlContainer, $id, $theme = THEME, $html_file='')
{
  if (empty ($html_file)){
    $html_file = VIEWS.'/'.str_replace( '.php', '.htm', $_SERVER['PHP_SELF']);
    }

//    file_put_contents( $html_file, activate_gf(addPhpTags($html)) );
  //  file_put_contents( $html_file, addPhpTags($html) );
      file_put_contents( $html_file, $html );

    $html = file_get_contents( $html_file );
    $html = appendHTML (  $html, $htmlContainer, $id, 'replace', 'getElementById');
    return $html;
}


/*
function replaceStyle($htmlContent, $htmlContainer)
{
  return appendHTML ( $htmlContent, $htmlContainer, 'head','replace', 'getElementByTagName', 'head');

}

function appendStyle($htmlContent, $htmlContainer)
  {
  return $outputHtml = appendHTML ( $htmlContent, $htmlContainer, 'head','append', 'getElementByTagName', 'head');
  }




function parse_format_fn($html)
{
// replace _f('data', 'function' ) --with--  <?= function(data) ? >
//pattern match for formats  _f($data, 'function')
//$pattern ="|_f\(([a-zA-Z|\$|\-|>|\(|\)]*),\s?'([a-zA-Z]*)'\s?\)|iSU";
$pattern ="#_f\(([a-zA-Z|\$|\-|>|\(|\)]*),\s?'([a-zA-Z]*)'\s?\)#iSU";
//$p = preg_quote('')

//enhanced  -  function is optional.
$pattern2 = "|_f\(([a-zA-Z|\$|\-|>|\(|\)|']*)(,\s?'([a-zA-Z]*)')?\s?\)|gm";
$pattern ="#_f\(([a-zA-Z|\$\->\(\)'\[\] ]*)(,\s?'([a-zA-Z_]*)')?\s?\)#iSU";

preg_match_all( $pattern, $html, $matches );

//print_r ($matches);

//1. Match whole function
//2. Match data  [includes $gf->( ) ]
//3. Match function
//4. $replace = <?= function($data) ? >
foreach ($matches[0] AS $k=> $match){
  $replace = ' <?= '.$matches[3][$k].'('.$matches[1][$k].') ?> ';
  $html = str_replace($match, $replace, $html);
  }
return $html;
}
*/
/*
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


function scrape_style($html)  //used to calculate size of css file within html page.
  {
  $style=false;
  libxml_use_internal_errors(true);
  $dom = new DOMDocument;
  $dom->loadHTML($html);
  $finder = new DOMXPath($dom);
  $xpath = '//style';
  $styles = $finder->query($xpath);
  if ($finder->query($xpath)->length > 0 ){
    $style=$styles[0]->nodeValue;
    }
  return $style;
  }
*/

// https://stackoverflow.com/questions/6286362/php-dom-get-nodevalue-html-without-stripping-tags
