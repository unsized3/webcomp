<?php namespace unsized\webcomp;

require_once('_view_builder.php');
require_once('_minify.php');

/***Componentisation of Html pages
/** Creates pure web componens for reuse (no javascript)
/** A Dependancy injector for the CSS_builder and HTML_builder classes.
/** Not loaded in production. We use autogeneraed html and css. A cache of the page.
/***********************************************************************************/
class Web_comp
{
public $component_dir= COMPONENT;
public $svg_symbols=array();

function __construct ($html, $css )
{
$this->html=$html;
$this->css=$css;
$this->logout = 30;
//$this->data=$data;
//$this->gf=$gf;
}

function build($component, $dir='')
{
if (empty($dir)){
  $dir=$this->component_dir;
  }

$css=$this->css;
$html=$this->html;
//$gf=$this->gf;
//make css theme variables available directly to the template.
$t=$css->t;
$z=$css->z;
extract ($t);

ob_start();
//if (is_array($dir)){print_r($dir);}
include_once($dir.'/'.$component.'.tpl.php');
return ob_get_clean();
}

function set($comp, $var=array())
{
  $this->$comp = (array)$var;
  }

//1. like build except the $gf data is not inserted.
//2. gf <?= $gf is replaced with {{$gf  }} and placed in a template cache directory.
//3. view template function [get html from cache dir] and show in browser (test)
//4. minify... and replace {{$gf  }} with <?= $gf->
//5. run code in production template.

//6.  three forms of templates
//a. Dev templates - split into COMPONENTS
//b. Tmp template - compiled html, css and {{ gf->   }} - humnan readable
//c. Test template - compiled template for a page - human readable
//d. Production template - minified.


//do something similar for modules? Eg Nav.
//authorised pages!
function buildPage($content){
  $this->set('head', ['logout' => $this->logout ]);
  $this->html->belowTF();
  $page[] = $this->build('html_close');

  //if ($this->gf->is_public() ){
  //  $page[] = $this->build('sign_in');
  //  }

  $page[] = 'fold';  // will be substituted with  ...   //$html->outputSvgSymbols('belowTF');
  $this->html->aboveTF();

  $page[] = $content;
  $page[] = $this->build('nav_auth');
  $page[] = $this->html->outputSvgSymbols('aboveTF');  //create a build for that
  $page[] = $this->build('head');
  $page[array_search('fold', $page)] = $this->html->outputSvgSymbols('belowTF');
  $html_output=reverse_string($page);
  $html_output=activate_gf($html_output);
return $html_output;
}



//do something similar for modules? Eg Nav.
function buildPublicPage($content){
  $this->html->belowTF();
  $page[] = $this->build('html_close');

  //if ($this->gf->is_public() ){
    $page[] = $this->build('sign_in');
  //  }

  $page[] = 'fold';  // will be substituted with  ...   //$html->outputSvgSymbols('belowTF');
  $this->html->aboveTF();

  $page[] = $content;
  $page[] = $this->build('nav_public');
  $page[] = $this->html->outputSvgSymbols('aboveTF');  //create a build for that
  $page[] = $this->build('head');
  $page[array_search('fold', $page)] = $this->html->outputSvgSymbols('belowTF');
  $html_output=reverse_string($page);
  $html_output=activate_gf($html_output);
return $html_output;
}

//deprecated - use buildPage / publicPage instead
function buildForm_old($component, $data=[] ){
  $this->html->belowTF();
  $page[] = $this->build('html_close');

  if ($this->gf->is_public() ){
    $page[] = $this->build('sign_in');
    }

  $page[] = 'fold';  // will be substituted with  ...   //$html->outputSvgSymbols('belowTF');
  $this->html->aboveTF();

  $page[] = $content;
  $page[] = $this->build('nav');
  $page[] = $this->html->outputSvgSymbols('aboveTF');  //create a build for that
  $page[] = $this->build('head');
  $page[array_search('fold', $page)] = $this->html->outputSvgSymbols('belowTF');
  $html_output=reverse_string($page);
return $html_output;
}

}//end class

//helpers




function filterArray ($record, $filterKeys=array() )
	{
    //echo nl2br (print_r($filterKeys, 1));
		$output=array();
		//$record = (array)$record;
		//$filterKeys = (array)$filterKeys;


	if (!empty($filterKeys)){
	//echo nl2br(print_r($filterKeys,1));
	foreach ($filterKeys AS $k)
		{
		if (isset ($record[$k]))
			{
			$output[$k]=$record[$k];
			}
		}
	}
	return $output;
	}
