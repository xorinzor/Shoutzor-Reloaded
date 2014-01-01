<?php
    /**
     * Base abstract class for a setting to be used in the settings class
     */
    abstract class setting {
        
        private $name = ""; //The name of the setting
        private $value = false; //The value of the setting
        
        public function __CONSTRUCT($name, $value) {
            $this->name = $name;
            $this->value = $value;
        }
        
        /**
         * Returns the name of the setting
         */
        public function getName() {
            return $this->name;
        }
        
        /**
         * Returns the value of the setting
         */
        public function getValue() {
            return $this->value;
        }
        
        /**
         * Sets the name of the setting
         */
        public function setName($name) {
            $this->name = $name;
            return $this;
        }
        
        /**
         * Sets the value of the setting
         */
        public function setValue($value) {
            $this->value = $value;
            return $this;
        }   
        
        public function __toString() {
            return $this->getValue();
        }
    }