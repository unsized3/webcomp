<?php namespace unsized\webcomp;

require_once  ROOT.'/vendor/unsized/webcomp/src/helpers/_render.php';
require_once  ROOT.'/vendor/unsized/webcomp/src/helpers/_minify.php';
require_once  ROOT.'/vendor/unsized/webcomp/src/helpers/_view_builder.php'; //performance functions
//deprecated use url_helper in grapefruit instead
//require_once (ROOT.'/vendor/unsized/webcomp/src/helpers/_template_fns.php');

class Render
{
  public  $component;
  public  $template;
  public  $templateThemed;
  public  $miniTemplate;
  public  $miniTemplateThemed;
  public  $partsListFile;


function __construct( AbstractComponent $component, $template_file='', $theme = THEME )
{
$this->component =  $component;

if (empty($template_file)){
  $this->template = VIEWS.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
} else {
  $this->template = VIEWS.$template_file;
  }

  $this->miniTemplate = str_replace ('.tpl', '.min.tpl', $this->template);

//themed templates
  $theme_pathinfo = pathinfo(THEME);
  $theme_name = '_theme_'.$theme_pathinfo['filename'];

  $this->templateThemed = str_replace ('.tpl', $theme_name.'.tpl', $this->template);
  $this->miniTemplateThemed = str_replace ('.tpl', $theme_name.'.min.tpl', $this->template);
  //$this->miniTemplateThemed = str_replace ('.tpl', '_themed.min.tpl', $this->template);
  $this->partsListFile = VIEWS.str_replace( '.php', '.ser', $_SERVER['PHP_SELF']);
}



//do not activate gf as <? will be modified when slotInHtml
function cacheTemplate ( $theme = THEME )
{
//echo  "\n";

  $partsList = \unsized\webcomp\PartsList::getInstance();

  $this->component->html();

  //add in style and svg
  $this->component->style = $this->concatParts( $partsList->getCss() );
  $this->component->svg   = $this->concatParts( $partsList->getSvg(), false );
  //create and save template

  $template = $this->component->html();
  //$template = addPhpTags($this->component->html());
  //$template = activate_fn($template); // pregmatch {$fn   }

  //save raw, no theme, no gf just theme vars
  saveTemplate(activate_theme_vars($template), $this->template);

//  $minified = minify_html($template);
  saveTemplate(minify_html(activate_theme_vars($template)), $this->miniTemplate);
/*
require $theme;
  ob_start();//output the template
    require $this->template;
  $themed_template = ob_get_clean();
  //$gf_themed_template = activate_gf($themed_template);
  saveTemplate($themed_template, $this->templateThemed);
  $themed_minified = minify_html($themed_template);
  saveTemplate($themed_minified, $this->miniTemplateThemed);
*/
}

//deprecate - not working - use serialize in partslist.
function cachePartsList()
{
    //wow this is nuts! Needs to be in dev script.
    //$snackbar = new \unsized\cardboard3\leaf\SnackBar(['id'=>'performance','type'=> 'twoline']);
    $partsList = \unsized\webcomp\PartsList::getInstance();
    //print_r($partsList);
//echo $this->partsListFile;
    file_put_contents($this->partsListFile, serialize($partsList));
}

function insertModule(string $container, $slotId)
{
  $moduleHtml = $this->component->html();
  $partsList = \unsized\webcomp\PartsList::getInstance();
  $moduleParts = $partsList->isRequiredBy ( get_class ($this->component));

//print_r($moduleParts);

if (isset($moduleParts['svg'])){
  $svg =   '<svg xmlns="http://www.w3.org/2000/svg" style ="display:none;">'
              .$this->concatParts( $moduleParts['svg'], false ).
           '</svg>';
  $moduleHtml = prepend($svg, $moduleHtml );
  }

  if (isset($moduleParts['css'])){
    $style = $this->concatParts( $moduleParts['css'] );
    $container =  appendStyle($style, $container);
  }
  //$html =  appendStyle($style, $container);

  $html = replaceSlot($slotId, $moduleHtml, $container); //simple str_replace of slot
  //$html = slotInHTML($moduleHtml, $html, $slotId); // render dialogue html
  return $html;
  }


function concatParts( array $css, $show_comment = true  )
{
  $comment='';
  $concat_strings = '';
  foreach ($css AS $key => $params)
    {
      if ($show_comment){
        $generated_by   = PHP_EOL.'/*** Generated from %s:  %s  - initiated_by : %s ***/'.PHP_EOL;
        $comment = sprintf( $generated_by, $params['type'], $key,  $params['required_by']);
        }
      $concat_strings .= $comment.$params['string'];
      }
  return $concat_strings;
  }


function outputTemplate($gf )
{
  ob_start();//output the template
      require $this->miniTemplate;
    return ob_get_clean();
  }


function unThemed($template, $save = false )
{
  $template = addPhpTags($template);
  //$template = activate_gf($template); //replace {$gf }
    if ($save){  //change to overwrite
      saveTemplate($template);
    }
  }

function showTemplate($gf, $content='')
{
  echo $this->outputTemplate($gf, $content='');
  }

function applyFunctions($template_file,  $html )
{

$html = activate_fn($html);
file_put_contents($template_file, $html);
ob_start();
  require $template_file;
$html = $this->output = ob_get_clean();
file_put_contents($template_file, $html);
return $html;
}

function applyData( $gf, $template)
{
  ob_start();
    require $template_file;
  return $this->output = ob_get_clean();
}


function applyTheme( $theme='', $template_file='')
{

if (empty($template_file)){
    //$template_file = TEMPLATE_DIR;
    $template_file = TEMPLATE_DIR.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
} else {
  $template_file = TEMPLATE_DIR.$template_file;
}

if (empty($theme)){
    $theme = THEME;
    }

    //require(FRONT_END_DESIGN.'/variables/colours.php'); // loads $red $blue etc
    //require(FRONT_END_DESIGN.'/variables/elevation.php'); // loads $z[ ]
    //require(FRONT_END_DESIGN.'/mixins/typescale.php'); // loads tme to ypescale
    require($theme);
    extract ($t);
    //echo $_SERVER['PHP_SELF'];
    //echo $template_file = $template_dir.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
    ob_start();
    require $template_file;
    return $this->output = ob_get_clean();
}





//performance
//Performance measurement tools below here


function theme($template)
  {
  // https://snook.ca/archives/html_and_css/css-concerns
  //smaccs
  //base
  //layout
  //module
  //states
  //Themes

  //md3
    //@import url(palette.css);
    //@import url(typography.css);
    //@import url(colors.css);
    //@import url(shape.css);
    //@import url(motion.css);
    //@import url(state.css);
    //@import url(elevation.css);
    //@import url(theme/light.css) screen and (prefers-color-scheme: light);
    //@import url(theme/dark.css) screen and (prefers-color-scheme: dark);
  //There are three kinds of tokens: reference, system, and component.
  //Material Design currently uses reference and system tokens;
  //component tokens are in development.
  //reference - seems 1:1 with css
  //use system and rules!!


  //load variables
  //pallette - https://github.com/material-foundation/material-tokens/blob/main/css/palette.css
  //

  require(FRONT_END_DESIGN.'/variables/colours.php'); // loads $red $blue etc
  require(FRONT_END_DESIGN.'/variables/elevation.php'); // loads $z[ ]
  //shapes
  //motion
  //typography
  //prob need theme to load these (so you can choose at theme level);
  require(FRONT_END_DESIGN.'/mixins/typescale.php'); // loads tme to ypescale
  require(THEME);
  extract ($t);
  //echo "***Theme***";
  //echo THEME;
  $template = addPhpTags($template);
  //$template = activate_gf($template); //replace {$gf }
  saveTemplate($template);
  //$template_file = TEMPLATE_DIR.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
  //require $template_file;
  }


//consider peripheral content and content theme.
//require intermediate step of showing template without data (gf) not activated.

function showContentTemplate($content='', $theme='')
{
if (empty($theme)){
  $theme = THEME;
  }

  require(FRONT_END_DESIGN.'/variables/colours.php'); // loads $red $blue etc
  require(FRONT_END_DESIGN.'/variables/elevation.php'); // loads $z[ ]
  require(FRONT_END_DESIGN.'/mixins/typescale.php'); // loads tme to ypescale
  require $theme;
  extract ($t);
  $template_file = TEMPLATE_DIR.str_replace( '.php', '.tpl', $_SERVER['PHP_SELF']);
  require $template_file;
}

function cssCache()
{
//get a config file, which lists css that needs to be cached.
//all other css goes in style.
}



} //end class render
