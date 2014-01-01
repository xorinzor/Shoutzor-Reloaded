<?php

abstract class socialAccount {
    private $id;
    private $name;
    private $accesstoken;
    private $permissions;
    private $isValidSession;
    
    public function __CONSTRUCT($id = 0, $name = '', $accesstoken = '', $permissions = array()) {
        $this->setUid($id);
        $this->setName($name);
        $this->setAccessToken($accesstoken);
        $this->setPermissions($permissions);
    }
    
    /**
     * setter for the permissions
     */
    protected function setPermissions($permissions) {
        if(!is_array($permissions)) $permission = array();
        $this->permissions = $permissions;
    }
    
    /**
     * getter for the permissions
     */
    public function getPermissions() {
        return $this->permissions;
    }
    
    /**
     * setter for the user ID
     * @param uid
     */
    protected function setUid($uid) {
        $this->id = $id;
        return true;
    }
    
    /**
     * getter for the user ID
     */
    public function getUid() {
        return $this->id;
    }
    
    /**
     * setter for the user's name
     * @param name
     */
    protected function setName($name) {
        $this->name = $name;
        return true;
    }
    
    /**
     * getter for the user's name
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * getter for the user accesstoken
     */
    public function getAccessToken() {
        return $this->accesstoken;
    }
    
    protected function setAccessToken($accesstoken) {
        $this->accesstoken = $accesstoken;
        return true;
    }
    
    /**
     * setter for the isValidSession variable
     */
    protected function setIsValidSession($bool) {
        $this->isValidSession = ($bool) ? true : false;
    }
    
    /**
     * getter for the isValidSession variable
     */
    public function getIsValidSession() {
        return $this->isValidSession;
    }
    
    /*=====================================
     * MYSOCIALSYNC-API ONLY METHODS
     *====================================*/
    
    /*=====================================
     * API METHODS
     *====================================*/
     
    /**
     * Get the statuses from the social network account
     */
    public function getstatus() {
        return false;
    }
}