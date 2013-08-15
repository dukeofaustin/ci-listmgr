<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
  /* Standard Audit field with action-yyyymmdd hh:mm:ss xm-id
   * ref: http://ellislab.com/codeigniter/user-guide/general/creating_libraries.html
  */
class Sitefileutils{
    
	function __construct(){
		$CI =& get_instance();
		$CI->load->helper('date');
 	    $which_time = 'us';
        $timezone = 'UM6';
        $daylight_saving = true;
        $include_seconds = true;
        
	}
    
    public function dirToArray($dir) { 
        $result = array(); 
     
        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) 
        { 
           if (!in_array($value,array(".",".."))) 
           { 
              //if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
              //{ 
              //   $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
              //} 
              //else 
              //{
              if(is_file($dir . DIRECTORY_SEPARATOR . $value))
                 $result[] = $value; 
              //} 
           } 
        } 
        return $result; 
    }
}
?>