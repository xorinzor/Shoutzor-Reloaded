<?php
/* 
* 	FUNCTION form_value_user($input) 
*
*	looks if there is a POST available
*	if not then return the original value
*
*/
function form_value_user($name, $original)
{
	return (isset($_POST[$name])) ? $_POST[$name] : $original;
}

/**
*	FUNCTION get_string_to_array
*
*	This functions fetches GET variables from a string
*/
function get_string_to_array($string)
{
	$array = array();

	if(!strpos($string, "?"))
	{
		$string = substr($string, 1);
	}

	$parts = explode("&", $string);

	foreach($parts as $part)
	{
		$new = explode("=", $part);
		$name = $new[0];
		$new[0] = "";
		$value = implode("", $new);
		$array[($name)] = $value;
	}

	return $array;
}

function make_url_clickable($text, $maxurl_len = false, $target = '_blank', $nofollow = true)
{
    if (preg_match_all('/((ht|f)tps?:\/\/([\w\.]+\.)?[\w-]+(\.[a-zA-Z]{2,4})?[^\s\r\n\(\)"\'<>\,\!]+)/si', $text, $urls))
    {
        $offset1 = ceil(0.65 * $maxurl_len) - 2;
        $offset2 = ceil(0.30 * $maxurl_len) - 1;
        
        foreach (array_unique($urls[1]) AS $url)
        {
            $urltext = (($maxurl_len == true) && (strlen($url) > $maxurl_len)) ? substr($url, 0, $offset1).'...'.substr($url, -$offset2) : $url;
            
            $text = str_replace($url, '<a href="'.SITEURL."redirect/?url=". urlencode($url) .'" rel="'.(($nofollow)?"nofollow":"").'" target="'. $target .'" title="'. $url .'">'. $urltext .'</a>', $text);
        }
    }

    return $text;
}  

/** 
 * Send a POST requst using cURL 
 * @param string $url to request 
 * @param array $post values to send 
 * @param array $options for cURL 
 * @return string 
 */ 
function curl_post($url, array $post = NULL, array $options = array()) 
{ 
    $defaults = array( 
        CURLOPT_POST => 1, 
        CURLOPT_HEADER => 0, 
        CURLOPT_URL => $url, 
        CURLOPT_FRESH_CONNECT => 1, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_FORBID_REUSE => 1, 
        CURLOPT_TIMEOUT => 4, 
        CURLOPT_POSTFIELDS => http_build_query($post) 
    ); 

    $ch = curl_init(); 
    curl_setopt_array($ch, ($options + $defaults)); 
    if( ! $result = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch); 
    return $result; 
} 

/** 
 * Send a GET requst using cURL 
 * @param string $url to request 
 * @param array $get values to send 
 * @param array $options for cURL 
 * @return string 
 */ 
function curl_get($url, array $get = NULL, array $options = array()) 
{    
    $defaults = array( 
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
        CURLOPT_HEADER => 0, 
        CURLOPT_RETURNTRANSFER => TRUE, 
        CURLOPT_TIMEOUT => 4 
    ); 
    
    $ch = curl_init(); 
    curl_setopt_array($ch, ($options + $defaults)); 
    if( ! $result = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch); 
    return $result; 
} 


function simpleXMLToArray($xml, 
                    $flattenValues=true,
                    $flattenAttributes = true,
                    $flattenChildren=true,
                    $valueKey='@value',
                    $attributesKey='@attributes',
                    $childrenKey='@children')
{
    $return = array();

    if(!($xml instanceof SimpleXMLElement)){return $return;}

    $name = $xml->getName();
    $_value = trim((string)$xml);

    if(strlen($_value)==0){$_value = null;};

    if($_value!==null){
        if(!$flattenValues){$return[$valueKey] = $_value;}
        else{$return = $_value;}
    }

    $children = array();
    $first = true;

    foreach($xml->children() as $elementName => $child):
        $value = simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);

        if(isset($children[$elementName])):
            if($first){
                $temp = $children[$elementName];
                unset($children[$elementName]);
                $children[$elementName][] = $temp;
                $first=false;
            }
            $children[$elementName][] = $value;
        else:
            $children[$elementName] = $value;
        endif;
    endforeach;

    if(count($children)>0){
        if(!$flattenChildren){$return[$childrenKey] = $children;}
        else{$return = array_merge($return,$children);}
    }

    $attributes = array();

    foreach($xml->attributes() as $name=>$value){
        $attributes[$name] = trim($value);
    }
    
    if(count($attributes)>0){
        if(!$flattenAttributes){$return[$attributesKey] = $attributes;}
        else{$return = array_merge($return, $attributes);}
    }

    return $return;
}

function parseObject($obj, $values=true){
  
    $obj_dump  = print_r($obj, 1);
    $ret_list = array();
    $ret_map = array();
    $ret_name = '';
    $dump_lines = preg_split('/[\r\n]+/',$obj_dump);
    $ARR_NAME = 'arr_name';
    $ARR_LIST = 'arr_list';
    $arr_index = -1;
    
    // get the object type...
    $matches = array();
    preg_match('/^\s*(\S+)\s+\bObject\b/i',$obj_dump,$matches);
    if(isset($matches[1])){ $ret_name = $matches[1]; }//if
    
    foreach($dump_lines as &$line){
    
      $matches = array();
    
      //load up var and values...
      if(preg_match('/^\s*\[\s*(\S+)\s*\]\s+=>\s+(.*)$/', $line, $matches)){
        
        if(mb_stripos($matches[2],'array') !== false){
        
          $arr_map = array();
          $arr_map[$ARR_NAME] = $matches[1];
          $arr_map[$ARR_LIST] = array();
          $arr_list[++$arr_index] = $arr_map;
        
        }else{
        
          // save normal variables and arrays differently...
          if($arr_index >= 0){  
            $arr_list[$arr_index][$ARR_LIST][$matches[1]] = $matches[2];
          }else{
            $ret_list[$matches[1]] = $matches[2];
          }//if/else
        
        }//if/else
      
      }else{
      
        // save the current array to the return list...
        if(mb_stripos($line,')') !== false){
        
          if($arr_index >= 0){
            
            $arr_map = array_pop($arr_list);
            
            // if there is more than one array then this array belongs to the earlier array...
            if($arr_index > 0){
              $arr_list[($arr_index-1)][$ARR_LIST][$arr_map[$ARR_NAME]] = $arr_map[$ARR_LIST];
            }else{
              $ret_list[$arr_map[$ARR_NAME]] = $arr_map[$ARR_LIST];
            }//if/else
            
            $arr_index--;
            
          }//if
        
        }//if
      
      }//if/else
      
    }//foreach
    
    $ret_map['name'] = $ret_name;
    $ret_map['variables'] = $ret_list;
    return $ret_map;
    
}//method

function make_seo($string)
{
    $separator = '-';

    $string = trim($string);
    $string = strtolower($string); // convert to lowercase text

    // Only space, letters, numbers and underscore are allowed
    $string = trim(preg_replace("/[^ A-Za-z0-9_]/", " ", $string));

    $string = str_replace(" ", $separator, $string);
    $string = preg_replace("/[ -]+/", "-", $string);

    return $string;

}