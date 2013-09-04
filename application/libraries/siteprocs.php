<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
  /* Standard Audit field with action-yyyymmdd hh:mm:ss xm-id
   * ref: http://ellislab.com/codeigniter/user-guide/general/creating_libraries.html
  */
class Siteprocs{
	private $which_time = 'us';
    private $timezone = 'UM6';
    private $daylight_saving = true;
    private $include_seconds = true;
    
	function __construct(){
		$CI =& get_instance();
		$CI->load->helper('date');
 	    $which_time = 'us';
        $timezone = 'UM6';
        $daylight_saving = true;
        $include_seconds = true;
        
	}
    public function setTimeZone($tz = 'UM6')
    {
        $timezone = $tz;
    }
    public function setDayLightSavings($useSavings = true)
    {
        $daylight_saving = $useSavings;
    }
    
	public function getDateTime()
    {
        /*$rtn = gmt_to_local(time(), 'UM6', true);
        $rtn = unix_to_human($rtn, $this->include_seconds, $this->which_time); // U.S. time with seconds
        $rtn = str_replace('-','x',$rtn);
        return $rtn;*/
       $rtn = gmt_to_local(now(),'UM6',TRUE);  //use date_helper function now() not time() for config file settings (gmt)
       $rtn = unix_to_human($rtn, TRUE, 'us');
       $rtn = str_replace('-','',$rtn);
       return $rtn;
        
	}
   /**
    * getLoginId
    * @param   none
    * @return  logged in userid
   */
    public function getLoginId() {
 	    $rtn = '';
        $ci = get_instance();
        $ci->load->library('session');
        $sarr = $ci->session->all_userdata();
        if (isset($sarr['usrid']) && $sarr['usrid'] != 0) {
          $rtn = $sarr['usrid'];
	    }
	    return $rtn;
    }
    /**
     * isEmpty
     * @param string or array
     * @return true if empty array or string
    */
    public function isEmpty($stringOrArray) {
        if(is_array($stringOrArray)) {
            foreach($stringOrArray as $value) {
                if(!isEmpty($value)) {
                    return false;
                }
            }
            return true;
        }
    
        return !strlen($stringOrArray);  // this properly checks on empty string ('')
    }    
    /*
    public function getUserId()
    {
       $usrid = 0;
       $sarr = $this->session->all_userdata();
 	   if (isset($sarr['usrid']) && $sarr['usrid'] != '' && $sarr['usrid'] != '0') 
          $usrid = $sarr['usrid'];
       return $usrid;
    }
    public function getUserName()
    {
       $user = 0;
       $sarr = $this->session->all_userdata();
 	   if (isset($sarr['usrid']) && $sarr['usrid'] != '' && $sarr['usrid'] != '0') 
          $usrid = $sarr['usrid'];
       return $usrid;
    }
    */
}
?>