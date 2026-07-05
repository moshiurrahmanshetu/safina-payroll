<?php

// for only one item check the array item and return 
function check_menu_active($current_location,$optionsArr){
  $condition = false;
  list($current['controller'], $current['action']) = explode('@', $current_location);	
  if( in_array($current_location,$optionsArr) == true || in_array( $current['controller'],$optionsArr) == true){
   $condition = true;
 } 	
 if($condition == true){
   return 'in '.str_replace('@','_', $current_location);
 }
}

// for checking multidimentional array item
function checkMenuActive($needle,$haystackArr){
	
	if(is_array($needle)){
		$result = array_intersect($needle, $haystackArr);
		if(count($result) > 0){
			return true;
		}
	}else{
		if(in_array($needle, $haystackArr)){
			return true;
		}
	}
	return false;
}	

// Start Log data mgt.
class Logs
{
	
  public static function searcharray($value, $key, $array) {
   foreach ($array as $k => $val) {
     if ($val->$key == $value) {
       return $array[$k];
     }
   }
   return null;
 }

 public static function displayButton($dataStatus=null, $dataUser_id=null, $dataId=null, $lavelcheck=null, $auth_user_id=null)
 {
  if($dataStatus!='2') {
    if ($dataUser_id == $auth_user_id) {
      $pid = '1';
    } else {
      $pid = '0';
    }
    $allow = true; 
    $item_found = 0; 
    $returnValues = array();
    $returnValues['status'] = 0;
    $returnValues['is_sender'] = 1;
    $returnValues['confirm'] = 0;
    $returnValues['onlySendButton'] = 0;

            //$found_prev = self::searcharray($dataId, 'data_id', $previouscheck);
    $found_lavel = self::searcharray($dataId, 'data_id', $lavelcheck);
            //dd($lavelcheck);
    if ($found_lavel) {    $item_found = 1;
      if ($found_lavel->sender_id == $auth_user_id) {
        if($found_lavel->status == 1) {
          $returnValues['status'] = $found_lavel->status;
          $allow = false;
        }else{
          $returnValues['confirm'] = $found_lavel->confirm;
        }
      }else if ($found_lavel->receiver_id == $auth_user_id) {
        $returnValues['is_sender'] = 0;
        if ($found_lavel->confirm == 0){
          if($found_lavel->status== 0){
            $returnValues['onlySendButton'] = 1;
          }else{
            $allow = false;
          }
        }else{
          $returnValues['confirm'] = 1;
          $returnValues['status'] = $found_lavel->status;
        }
      }else{
        $allow = false;
      }
    }
            //return $previouscheck;
    if (($pid > 0) && ($allow)) {
      return (object)$returnValues;
    }else if(($item_found > 0) && ($allow)) {
      return (object)$returnValues;
    }else{
      return false;
    }

  }else {
    return false;
  }
}

}
// End Log data mgt.

function reverseDate($date){
  if($date){ $new_Date = date("Y-m-d", strtotime($date));
  return $new_Date;}else{return '';}
}

function reverseDate_with_add($date){
  if($date){ $new_Date = date("Y-m-d", strtotime($date. '+1 day'));
  return $new_Date;}else{return '';}
}


// ======= Start need to filter for invoice mgt. =======
function str_spacecase($slug){
  return str_replace(['-', '_'], " ", $slug);
}
//show value with format
function showDateWithFormat($value) {
  return Carbon\Carbon::parse($value)->format('d/m/Y H:i');
}
  //get value from input field
function getDateFormInput($value){
  return Carbon\Carbon::parse($value)->format('Y-m-d');
}

function mystudy_case($value){
  return ucwords(str_replace(['-', '_'], ' ', $value));
}


function get_file_name($string) {
    // Transliterate non-ascii characters to ascii
  $str = trim(strtolower($string));
  $str = iconv('UTF-8', 'ISO-8859-1//IGNORE', $str);

    // Do other search and replace
  $searches = array(' ', '&', '/');
  $replaces = array('-', 'and', '-');
  $str = str_replace($searches, $replaces, $str);

    // Make sure we don't have more than one dash together because that's ugly
  $str = preg_replace("/(-{2,})/", "-", $str );

    // Remove all invalid characters
  $str = preg_replace("/[^A-Za-z0-9-.]/", "", $str );

    // Done!
  return $str;
}
// End need to filter for invoice mgt. 


?>