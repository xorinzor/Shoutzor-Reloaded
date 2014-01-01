<?php

    class mysqlSetting extends setting {
        public function __CONSTRUCT($name, $value) {
            parent::__CONSTRUCT($name, $value);
        }
        
        public function setName($name) {
            $query = cl::g("mysql")->query("UPDATE `".SQL_PREFIX."settings` SET name='$name' WHERE name='".$this->name."'");
            if($query):
                $this->name = $name;
                return $this;
            else:
                //errorhandler
            endif;
        }
        
        public function setValue($value) {
            $query = cl::g("mysql")->query("UPDATE `".SQL_PREFIX."settings` SET value='$value' WHERE name='".$this->name."'");
            if($query):
                $this->value = $value;
                return $this;
            else:
                //errorhandler
            endif;
        }
    }