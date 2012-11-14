<?php
/**
 * :: DB Model ::
 * Maintains connection to database
 */

class Db extends Model {
    private $dbSettings;
    private $dbOject;
    private $connected = false;
    private $resultObject;
    
    public function __construct($context) {
        parent::__construct($context);
        if(isset($this->context->config['db'])) {
            $this->dbSettings = $this->context->config['db'];
            $this->connect();
        } else {
            throw new Exception('No DB settings found in config file!');
        }
    }
    
    /**
     * Connects to datbase using MySQLi
     * @throws Exception
     */
    public function connect() {
        $this->dbOject = new mysqli($this->dbSettings['server'], $this->dbSettings['username'], $this->dbSettings['password'], $this->dbSettings['database']);
        if($this->dbOject->connect_errno) {
            throw new Exception('Could not connect to database! Server returned: '. $this->dbOject->connect_errno);
        } else {
            $this->connected = true;
        }
    }
    
    /**
     * Runs a query against the DB
     * 
     * @param type $q
     * @return mysqli_result
     */
    public function query($q = null) {
        if(!is_string($q)) return false;
        if(!$this->connected) return false;
        $this->resultObject = $this->dbOject->query($q);
        if($this->resultObject == false) {
            throw new Exception('Query Error: '. $this->dbOject->error);
            return false;
        }
        return $this->resultObject;
    }
    
    /**
     * Inset some data iinto db
     * 
     * @param string $table
     * @param array $data
     * @return mysqli_result
     * @throws Exception
     */
    public function inset($table = null, $data = null) {
        if(!is_string($table))  throw new Exception('Table given is not a sting');
        if(!is_array($data))    throw new Exception('Data given is not an array!');
        
        $table = $this->escpae($table);
        $data = $this->escpae($data);
        
        $fields = "(";
        $values = "(";
        $lenth = count($data);
        $c = 1;
        
        foreach($data as $field => $value) {
            $fields .= $field;
            $values .= $value;
            
            if($c != $lenth) {
                $fields .= ", ";
                $values .= ", ";
                $c++;
            }
        }
        
        $fields .= ")";
        $values .= ")";
        
        $q = "INSET INTO ". $table. " ". $fields. " VALUES ". $values;
        return $this->query($q);
    }
    
    /**
     * Returns list where the query fits
     * 
     * @param string $table
     * @param array $query
     * @return mysqli_result
     * @throws Exception
     */
    public function get_where($table = null, $query = null) {
        if(!is_string($table))  throw new Exception('Table given is not a sting');
        if(!is_array($query))    throw new Exception('Query given is not an array!');
        
        $table = $this->escpae($table, false);
        $query = $this->whereValueList($query);
        
        $q = "SELECT * FROM ". $table. " WHERE ". $query;
        return $this->query($q);
    }
    
    /**
     * Runs a select command against the given table
     * 
     * @param string $table
     * @return mysqli_result
     * @throws Exception
     */
    public function get($table = null) {
        if(!is_string($table))  throw new Exception('Table given is not a sting');
        $table = $this->escpae($table);
        $q = "SELECT * FROM ". $table;
        return $this->query($q);
    }
    
    /**
     * Delets a item in the table, where query is fullfilled
     * 
     * @param string $table
     * @param array $query
     * @return mysqli_result;
     * @throws Exception
     */
    public function delete($table = null, $query = null) {
        if(!is_string($table))  throw new Exception('Table given is not a sting');
        
        $table = $this->escpae($table);

        $q = "DELETE FROM ". $table;
        
        if(isset($query) && is_array($query)) {
            $query = $this->setValueList($query);
            $q .= " WHERE ". $query;
        }

        return $this->query($q);
    }
    
    /**
     * Runs an update against DB
     * 
     * @param string $table
     * @param array $set
     * @param array $query
     * @return mysqli_result
     * @throws Exception
     */
    public function update($table = null, $set = null, $query = null) {
        if(!is_string($table))  throw new Exception('Table given is not a sting');
        if(!is_array($set))    throw new Exception('Set parameter given is not an array!');
        
        $table = $this->escpae($table);
        $set = $this->setValueList($set);
        $q = "UPDATE ". $table. " SET ". $set;
        
        if(is_array($query)) {
            $queryString = $this->setValueList($query);
            $q .= " WHERE ". $queryString;
        }
        
        return $this->query($q);
    }
    
    /**
     * Escapes a single string or array
     * 
     * @param type $inp
     * @return boolean|string
     */
    public function escpae($inp = null, $pling = true) {
        if(is_array($inp)) return $this->escapeArray($inp);
        if(!is_string($inp)) return $inp;
        if(!$this->connected) return false;
        $ret = $this->dbOject->real_escape_string($inp);
        
        if($pling && !is_numeric($ret) ) {
            $ret = "'". $ret. "'";
        }
        
        return $ret;
    }
    
    /**
     * Escapes an array
     * 
     * @param type $list
     * @return type
     */
    private function escapeArray($list = null) {
        if(!is_array($list)) return $list;
        
        foreach($list as $k => $item) {
            $list[$k] = $this->escpae($item);
        }
        
        return $list;
    }
    
    /**
     * Makes an Array to a comma seperatet list
     * 
     * @param type $list
     * @return string
     */
    private function makeList($list = null) {
        if(!is_array($list)) return $list;
        $output = "";
        foreach($list as $item) {
            $output .= $this->escpae($item). ", ";
        }
        
        return $output;
    }
    
    /**
     * 
     * @param type $inp
     */
    private function setValueList($inp = null) {
        if(!is_array($inp)) return $inp;

        $query = $this->escpae($inp);
        $lenth = count($query);
        $c = 1;
        
        $queryString = "";
        
        foreach($query as $field => $value) {
            $queryString .= $field. "=". $value;
            
            if($c != $lenth) {
                $queryString .= ", "; 
                $c++;
            }
        }
        
        return $queryString;
    }
    
    private function whereValueList($inp = null) {
        if(!is_array($inp)) return $inp;

        $query = $this->escpae($inp);
        $lenth = count($query);
        $c = 1;
        
        $queryString = "";
        
        foreach($query as $field => $value) {
            $queryString .= $field. "=". $value;
            
            if($c != $lenth) {
                $queryString .= " && "; 
                $c++;
            }
        }
        
        return $queryString;
    }
}

?>
