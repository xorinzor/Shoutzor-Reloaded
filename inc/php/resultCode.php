<?php

$__resultCode = array(
        /**
         * regular messages
         */
		'code_1'	=>	array(
        				"code" 	    	=> "200",
        				"description" 	=> "Command executed succesfully"
        			),


        /**
         * Error-related messages
         */
		'code_200' 	=>	array(
    					"code" 	    	=> "400",
    					"description" 	=> "Invalid Method requested"
    				),

    	'code_201' 	=> array(
    					"code"	    	=> "400",
    					"description"	=> "This network does not exist"
    				),

		'code_202' 	=>	array(
    					"code"	    	=> "400",
    					"description"	=> "This network has no such method"
    				),

	    'code_203'	=> 	array(
    					"code"	    	=> "400",
    					"description"	=> "Not all required parameters are given"
    				),
                    
        'code_204'  =>  array(
                        "code"          => "400",
                        "description"   => "No request network is provided or user has no accounts for requested network connected"
                    ),


        /**
         * Authentication-related error messages
         */
		'code_300' 	=> 	array(
    					"code" 	    	=> "403",
    					"description"	=> "Your account has not been activated yet!"
    				),

		'code_301' 	=> 	array(
    					"code" 		    => "403",
    					"description"	=> "Incorrect login credentials given"
    				),

		'code_302'	=>	array(
    					"code"	    	=> "403",
    					"description"	=> "There is no active session to use"
    				),

		'code_303'	=>	array(
    					"code"	    	=> "403",
    					"description"	=> "You dont have sufficient privileges to use the requested method"
    				),
    
        'code_304'  =>  array(
                        "code"          => "403",
                        "description"   => "This social network account is not connected to any of our accounts"
                    ),
    
        /**
         * System-related error messages
         */
    	'code_400' 	=> 	array(
    					"code" 	    	=> "500",
    					"description"	=> "An internal error occured while processing your API request"
    				)
	);