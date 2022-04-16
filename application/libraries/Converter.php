<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Converter 

{

	protected $ci;
	protected $skey;
	protected $apikey;
	

	function __construct(){

		$this->ci 		= & get_instance();
		$this->skey 	= $this->ci->config->item('encryption_key');
		$this->apikey	= $this->ci->config->item('encryption_key');
	}
 

    public function encode($value=null){	
		
        return trim($this->ci->encryption->encrypt($value));
    } 

    function decode($value=null){      

        return trim($this->ci->encryption->decrypt($value));

    }

	

	

}



?>