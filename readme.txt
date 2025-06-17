

New approach

1. Interface / abstract class that enforces concrete webcomponent classes to have [html, css]
2. Set of concrete COMPONENT classes for 'buttons' , 'switches', 'form inputs';
3. Set of

Execution class(es) that invokes the concrete webcomponent classes that contain methods
[html, css],   and optional js, css.
...and holds the Asset Container.

//Asset Container crud class.

//1. Partlist class,
// Stores parts in the container
// Part_id , Show first[], get object eg- name, params.

//2. Build class.  Build the html.
// eg build order:  components -> modules -> body -> inject svg -> head -> style -> css_link.
// cached vs non cached content.

//2b. Optimise class
// minify

//3. Auto Documentation class  - click on an id to reveal the parts of that module, with examples.

//4. For simplicity consider separate containers for svg, css, js vs one with an array against the id (preferred)
// - store name
// - required by  html_id =
// - requested by [concrete-class]. eg button
//    class_type_subtype  - eg  ['type' => 'class'] button_css_fab_extended

//think backwards -
(i)  What are the required resources?
eg ['file' => 'filename']
(ii) What is the order / which id needs the resource first?
(ii) Where does the resource need to be placed?  eg if svg before / inside the html-id tag.
// if content and css inside the style tag.
// if layout inside the css link file.

//conclusion - due to different behavours of dependancy types, initially create 3 different assetcontainer classes.
//eg css_link container (extends style_container). style_container

//partslist class eg

//partslist(cssContainer, svgContainer, jsContainer)

$component = new PartsList($assetContainer);

$component->html(new Button( args    ));

class PartsList( )
{
function __construct( AssetContainer $assetContainer )
  {
    $this->assetContainer = $assetContainer;
    }

sfunction html(obj )
{

}



}

//try and default all css dependancies to be class / namespace based. [simplification]

[required by] | [component_id]
//body
//svg         [//file, but can later deploy code which gets info from google class // deploy adapter. ]
//style       [type | file, class, string ] // deploy as strategy.
//css_link     can
//js

//format required for describing part. eg namespace eg instantiate class, instanciate method.
//is it new?

//Below the line // below the content flag??  // order for symbol file //method for determining where to place symbol file.
