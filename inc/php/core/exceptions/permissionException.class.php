<?php

class permissionException extends Exception {
    
    private $type;
    
    const INSUFFICIENT_PERMISSIONS = 0;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, $previous = null) {
        $this->type = $message;
        
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    
    public function getType() {
        return $this->type;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}