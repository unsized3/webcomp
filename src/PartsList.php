<?php namespace unsized\webcomp;

class PartsList extends Singleton
{
    public $hashmap = [];
    public $requiredBy = 'root';


public function addCss(string $key, array $value )
    {
    if (!isset($this->hashmap['css'][$key])){
      $this->hashmap['css'][$key] = $value + ['required_by' => $this->requiredBy ];
      }
    }

public function addSvg(string $key, array $value )
    {
      if (!isset($this->hashmap['svg'][$key])){
        $this->hashmap['svg'][$key] = $value + ['required_by' => $this->requiredBy ];
        }
    }

public function addJs(string $key, array $value )
    {
      if (!isset($this->hashmap['js'][$key])){
            $this->hashmap['js'][$key] = $value + ['required_by' => $this->requiredBy ];
            }
    }

public function getCss(): array
        {
        return $this->hashmap['css'] ?? [];
        }

public function getSvg(): array
        {
        return $this->hashmap['svg'] ?? [];
        }

public function getJs(): array
        {
        return $this->hashmap['js'] ?? [];
        }


public function isRequiredBy( $className)
        {
          $filteredParts = [];
          foreach ($this->hashmap AS $type => $record){
            foreach ($record AS $key => $value){

            if (in_array( $className, $value)){ //print_r($value);
              $filteredParts[$type][$key] = $value;
              }
            }
          }
          return $filteredParts;
        }



public function serialize($file='') //in use for layout.php
{
  foreach ($this->hashmap as $type => $row)
    {
      foreach ($row as $key => $element)
        {
          //print_r ($element);
        //  echo " - ";
        //  print_r ($key);
        //  echo "<br>";
          $parts[$type][$key]=$element;
        }
    //$parts[$type] = array_keys($type);
    $serialized_parts = serialize($parts);
    file_put_contents( $file, $serialized_parts);
    }
    //echo nl2br(print_r($parts['css'], 1));
    //echo nl2br(print_r($parts['svg'], 1));

}

//unserialize
//get the serialized file
//load all items into the partslist
// construct a filtered version of css, html etc.

}
