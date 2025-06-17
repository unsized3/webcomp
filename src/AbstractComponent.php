<?php namespace unsized\webcomp;

require_once  '/var/dev_communitye/vendor/unsized/webcomp/src/helpers/_url_helper.php';

abstract class AbstractComponent
{
  //protected $svg = [];
  abstract protected function html();//: string
  abstract protected function css();
  abstract protected function isLeaf();

  public $svg_dir    = SYMBOL;
  public $style_dir  = CARDBOARD3.'/css';  //design out - base css can go in body container component
  public $config;
  public $partsList;
  public $style = '';
  public $svg = '';
  //private $required_by;
  private $htmlAttributeString;
  private $booleanAttributeString;

  public $htmlGlobalAttributes = ['accesskey', 'class', 'contenteditable', 'data-*', 'dir', 'draggable', 'enterkeyhint', 'hidden', 'id', 'inert', 'inputmode', 'lang', 'popover', 'spellcheck', 'style', 'tabindex', 'title', 'translate'];

  public $htmlBooleanAttributes =  ['allowfullscreen','async','autofocus','autoplay',
                              'checked','controls','default','defer','disabled',
                              'formnovalidate','inert', 'ismap','itemscope','loop',
                              'multiple','muted','nomodule','novalidate','open',
                              'playsinline','readonly','required','reversed','selected'
                              ];
/*
public $htmlAttributeList = ['accept', 'accept-charset', 'accesskey', 'action', 'allow', 'alt', 'as', 'autocapitalize', 'autocomplete', 'background', 'bgcolor', 'border', 'capture', 'charset', 'cite', 'class', 'color', 'cols', 'colspan', 'content', 'contenteditable', 'coords', 'crossorigin', 'csp', 'data', 'data-*', 'datetime', 'decoding', 'dir', 'dirname', 'download', 'draggable', 'enctype', 'enterkeyhint', 'for', 'form', 'formaction', 'formenctype', 'formmethod', 'formtarget', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'http-equiv', 'id', 'integrity', 'intrinsicsize', 'inputmode', 'itemprop', 'kind', 'label', 'lang', 'language', 'loading', 'list', 'low', 'max', 'maxlength', 'minlength', 'media', 'method', 'min', 'name', 'optimum', 'pattern', 'ping', 'placeholder', 'poster', 'preload', 'referrerpolicy', 'rel', 'role', 'rows', 'rowspan', 'sandbox', 'scope', '"scoped Non-standard', 'shape', 'size', 'sizes', 'slot', 'span', 'spellcheck', 'src', 'srcdoc', 'srclang', 'srcset', 'start', 'step', 'style', 'tabindex', 'target', 'title', 'translate', 'type', 'usemap', 'value', 'width', 'wrap'
]; // boolean list removed.
*/

//public $htmlValidationList = ['pattern', 'email', 'minlength', 'maxlength', 'min','max' ];

//outputs??
//public $htmlAttributeString ='';

function __construct( array $config =[], $parentNode = 'root' )
  {
    $this->config = $config;
      if (isset($config['id'])){
        if (isset($gf->$config['id'])){
          //array_merge($config, $data); //data overrides config
          $this->d = $gf->$config['id'];
          }
        }

  $this->partsList = \unsized\webcomp\PartsList::getInstance();//$this->partsList = $partsList
  if (!$this->isLeaf()){
    $this->partsList->requiredBy = get_class($this);
    $this->partsList->addCss(get_class($this).'\css', ['string' => $this->css(),  'type' => 'namespace'] ); //'required_by' => get_class($this),
    }
  }

function component(AbstractComponent $component )
{
$html = '';
$this->partsList->addCss(get_class($component).'\css', ['string' => $component->css(), 'type' => 'namespace'] );  //'required_by' => $this->partsList->requiredBy,
$html = $component->html();
return $html;
}

public function setStyleFiles(array $files, string $dir = '', string $ext='css')
{
$dir = empty($dir) ? $this->style_dir : $dir;
foreach ($files as $k => $file){
      $string_css = file_get_contents($this->style_dir.'/'.$file.'.'.$ext);
      $this->partsList->addCss($this->style_dir.'/'.$file.'.'.$ext, ['string' => $string_css,  'type' => 'file'] ); //'required_by' => $this->partsList->requiredBy,
  }
}

//mote: more efficient to get builder to get and concat files
function setSymbolFiles($files, string $dir = '', string $ext='svg')
{
$files =(array)$files;
$dir = empty($dir) ? $this->svg_dir : $dir;
foreach ($files as $k => $file){
  $string_svg = file_get_contents($dir.'/'.$file.'.'.$ext);
  $this->partsList->addSvg( $dir.'/'.$file.'.'.$ext, ['string' => $string_svg,  'type' => 'file'] ); //'required_by' => $this->partsList->requiredBy,
  }
}

function setCss($method)
{
  $string_method = get_class($this).'\\'.$method;
  $this->partsList->addCss(get_class($this).'\\'.$method, ['string' => $this->$method(),  'type' => 'namespace'] );
}

function setCssString( $string, $fn = 'css')  //for direct use in scripts
{
  $string_method = get_class($this).'_'.$fn;
  $this->partsList->addCss($string_method, ['string' => $string,  'type' => 'string'] );
  }

function mustBeConfigured(array $properties)
{
foreach ($properties as $k => $property){
  if (!isset($this->config[$property])){
    die( "Component: ".get_class($this). "  must have property: $property <br> set in the config <br>");
    }
  }
}

//type defaults to text, but can be number, tel, email
function sanitiseConfig(string $property, array $filter=[], string $default='')
{
  $default_value  = $this->config[$property] ?? $default;

  if (!empty($filter)){
    $filtered_value = array_intersect( (array) $default_value, $filter) ; // default value must be in [filter] (if set)
    return $this->config[$property] = reset($filtered_value) ?: $default;
  }else {
    return $this->config[$property] = $default_value;
  }
}

function booleanAttributeString()
{
$booleanAttributeString = '';
$htmlBooleanAttributes = array_intersect( $this->htmlBooleanAttributes, $this->htmlComponentAttributes );

foreach ($this->config as $k => $v) {
  if (in_array($k, $htmlBooleanAttributes )){
      $booleanAttributeString .= ' '.$k;
      }
  }

$this->booleanAttributeString = $booleanAttributeString;
return $this->booleanAttributeString;
}


function htmlAttributeString()
{
$htmlAttributeString=' ';
$htmlAttributes = $this->htmlGlobalAttributes + $this->htmlComponentAttributes;
$htmlAttributes = array_diff($htmlAttributes, $this->htmlBooleanAttributes);
//filter based on htmlAttribute
$attributes = array_intersect_key( $this->config, array_flip($htmlAttributes) );

//add in data-* attributes
foreach ($this->config as $k => $v ){
  if (substr($k, 0, 5) == 'data-'){
    $attributes[$k] = $v;
  }
}

//print_r($this->config);
//print_r($attributes);
foreach ($attributes as $k => $v) {
  $htmlAttributeString .= " ".$k."='".$v."' ";
  }
$this->htmlAttributeString = $htmlAttributeString;
return $this->htmlAttributeString;
}


function js () :string
{
return '';
}

}
