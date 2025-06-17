<?php

//echo  '<h4>***url_helper***<h4>' ;

function get_url_segments()
{
  return  ( explode ( '/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ) );
  }

function get_url_segment($seg)
  {
    $segs=get_url_segments();
    //echo count($segs);
    if ($seg=='last'){
      $output  = $segs[count($segs)-1] ?? '';
    } elseif ($seg < 0){
      $output = $segs[count($segs)+$seg ];
    }

    else{
      $output = $segs[$seg] ?? '';
      }
    return $output;
    }

/*moved to maker
function subdomain_is($name='test'){
      $test=false;
      $subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -2));
      if ($subdomain == $name){
        $test='true';
        }
      return $test;
  }
*/
  //date helper
  function getDateFromUrl($p='3')
  {
  $sql_date=false;
  //assumed format is dd/mm/yyyy
  $url=get_url_segments();
  $j = (int)$url[$p];//day
  $n = (int)$url[$p+1];//month
  $Y = (int)$url[$p+2]; //year

  $i_format= 'j-n-Y';
  //echo '<p>Big</p>';
  $date=$j.'-'.$n.'-'.$Y;
  //convert to sql format
  if ($d = DateTime::createFromFormat($i_format, $date)){
    $sql_date = $d->format('Y-m-d');
    }
  return $sql_date;
  }

//gets scriptname with the .ext removed
function script_name(){
  $script = pathinfo ( $_SERVER['SCRIPT_NAME'] , PATHINFO_BASENAME );
  return substr ( $script , 0 , strpos ($script , '.' , 0 ));  //remove .ext
  }
