<?php

class api {

    private $method;
    private $requestNetworks;
    private $format;
    private $params;

    private $validFormats;
    private $validMethods;
    private $validNetworks;

    public function __CONSTRUCT() {
        $this->method = '';
        $this->requestNetworks = array();
        $this->format = 'json';
        $this->params = array();
        
        $this->validFormats = array(
                                'xml',
                                'json',
                                'phparray1337'
                            );
        
        $this->validMethods = array(
                                'terminal',
                                'createaccount',
                                'getradioinfo',
                                'getradiohistory',
                                'getsysteminfo',
                                'toggleliquidsoap',
                                'changevolume',
                                'tracks',
                                'albums',
                                'artists',
                                'histories',
                                'queues',
                                'users',
                                'request',
                                'searches',
                                'correctplaylistforward',
                                'correctplaylistbackward'
                            );
                            
        $this->validNetworks = array(
                                'shoutzor'
                            );
    }
    
    public function doMethod($_params) {
        global $__resultCode; //Use the variable containing result codes
        
        //Set the method name, network name and requesttype to lowercase
        $_params['method'] 		= strtolower($_params['method']);
        $_params['network']     = strtolower($_params['network']);
        $_params['format']      = strtolower($_params['format']);
        
        //Set the private parameters
        $this->method           = preg_replace('/[^a-z]/i', '', $_params['method']);
		$this->requestNetworks 	= array();
		$this->format 		    = in_array($_params['format'], $this->validFormats) ? $_params['format'] : "json";
                
        //Store network values from POST in this temporary variable
        $requestNetworks = array_unique(preg_replace('/[^a-z]/i', '', explode(",", $_params['network'])));
            
        //Store user social network accounts in temporary variable
        cl::g("session")->setConnectedApiNetworks();
        $userNetworks = cl::g("session")->getConnectedApiNetworks();
        
        //Parse all requested networks
        foreach($requestNetworks as $network):
            //Filter set for $network
            if(isset($_params[$network . 'filterid'])):
                //Strip everything but numbers from the filter ID (an network ID can ONLY consist of digits)
                $filters = array_unique(preg_replace('/[^0-9]/', '', explode(",", $_params[$network.'filterid'])));
                
                //Parse all filters
                foreach($filters as $filter):
                    //Check if social network account for given filter exists, if not add value FALSE which will produce an error in the API result (invalid network requested)
                    $this->requestNetworks[$network . '_' . $filter] = (isset($userNetworks[$network . '_' . $filter])) ? $userNetworks[$network . '_' . $filter] : false;
                endforeach;
                
                //Remove from parameters
                unset($_params[$network . 'filterid']);
                
            //No filter is set for $network
            else:
                //Parse all social network accounts from the current session user
                foreach($userNetworks as $item=>$instance):
                    //strip all extra's from the name (for example 'facebook_1234567' will become 'facebook') and compare it to $network
                    if(preg_replace('/[^a-z]/i', '', $item) == $network): 
                        //If the name matches $network add the instance of the social network account to the requestNetwork variable
                        $this->requestNetworks[$item] = $instance;
                    endif;
                endforeach;
            endif;
        endforeach;
        
        //Remove parsed variables from parameters
        unset($_params['network']);
        unset($_params['method']);
        unset($_params['format']);
        
        //Store the params used
        $this->params = $_params;
        
        //Unset unused variables
        unset($_params);
        unset($requestNetworks);
        unset($userNetworks);
        
        //preset
        $result = array();
        
        /**
         *  Check if valid network is requested
         */
        if(count($this->requestNetworks) == 0):
            $result['info'] = $__resultCode['code_204'];
        elseif(!in_array($this->method, $this->validMethods) || empty($this->method)):
            $result['info'] = $__resultCode['code_203'];
        else:
            /**
             * Execute command for every requested network
             */
            foreach($this->requestNetworks as $network=>$instance):
                //Check if networks exist before executing the command
                if($instance === false):
                    //network does not exist                    
                    $result[$network]                   = array();
                    $result[$network]['result']         = array();
                    $result[$network]['info']        = $__resultCode['code_201'];
                else:
                    //Check if the current account has the requested social network account connected
                    //$connectedNetworks = cl::g("session")->getConnectedSocialNetworks();
                    //isset($connectedNetworks[$network.])
                    
                    //Execute the command
                    $output = $instance->{$this->method}($this->params);
                    
                    $result[$network] = array();
                    
                    if($output === false):
                        $result[$network]['result']     = array();
                        $result[$network]['info']    = $__resultCode['code_202'];
                    else:
                        $result[$network]['result']     = $output;
                        //Only add mssinfo data if no resultcode has been added
                        if(!isset($result[$network]['info'])) $result[$network]['info']    = $__resultCode['code_1'];
                    endif;
                    
                endif;
            endforeach;
        endif;
        
        //Format and return the result
        return cl::g("format")->convert($this->format, $result);
    }
}