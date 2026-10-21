<?php
class Database {

    private $_mDb = NULL;

    public function __construct($host, $user, $password, $database) {

        $this->_mDb = new mysqli($host, $user, $password, $database);
        
        if (!mysqli_connect_errno()) {
            
            $this->_mDb->set_charset('utf8');
            
            return TRUE;
        } else {
            exit('<br />db connect error');
        }
    }

    public function insert($table, $data = array(), $insertIgnore = FALSE) {
        
        $insert_ignore = !empty($insertIgnore) ? " IGNORE" : NULL;

        foreach ($data as $key => $value) {
            
            $cols[] = $key;
            $vals[] = $this->_mDb->real_escape_string($value);
        }
        
        $q_columns = implode(', ', $cols);
        
        $q_values = "'" . implode("', '", $vals) . "'";
        
        $q = "INSERT{$insert_ignore} INTO {$table} ({$q_columns}) VALUES ({$q_values})";
        
        if ($this->_mDb->query($q)) {
            
            return $this->_mDb->insert_id;
            
        } else {
            
            echo "<br />{$q}<br />" . $this->_mDb->error;
            
            return FALSE;
        }
    }

    public function update($table, $data = array(), $conditions = array()) {

        $q = "UPDATE {$table} SET ";
        
        $u = array();
        foreach ($data as $key => $value) {
            $u[] = "{$key} = '{$value}'";
        }
        
        $q .= implode(', ', $u);
        
        $c = array();
        foreach ($conditions as $key => $value) {
            $c[] = "{$key} = '{$value}'";
        }
        
        $q .= " WHERE " . implode(' AND ', $c);
        
        if ($this->_mDb->query($q)) {
            
            return $this->_mDb->affected_rows;
        } else {
            
            echo "<br />{$q}<br />" . $this->_mDb->error;
            
            return FALSE;
        }
    }
    
    public function query($query) {
        
        if ($this->_mDb->query($query)) {
                
            return $this->_mDb->affected_rows;
            
        } else {
                
            echo "<br />{$query}<br />" . $this->_mDb->error;
                
            return FALSE;
        }
    }

    public function select($query, $columns) {

        if ($stmt = $this->_mDb->prepare($query)) {
            
            if (!$stmt->execute()) {
                
                echo "{$query}<br />" . $this->_mDb->error;
                
                return FALSE;
            }
            
            $parameters = array();
            
            $params = $columns;
            
            $columns = array();
            
            foreach ($params as $key => $val) {
                /**
                 *
                 * @var array
                 */
                $parameters[] = &$columns[$val];
            }
            
            call_user_func_array(array($stmt,'bind_result'), $this->_ArrayValuesAsRefference($parameters));
            
            $results = array();
            
            while ($stmt->fetch()) {
                
                $result = array();
                
                foreach ($columns as $key => $val) {
                    
                    $result[$key] = $val;
                }
                
                $results[] = $result;
            }
            
            $stmt->close();
            
            return $results;
        } else {
            
            echo "<br />{$query}<br />" . $this->_mDb->error;
            
            return FALSE;
        }
    }

    /**
     * PHP 5.3 fix call_user_func_array + mysqli bind_param
     *
     * @author http://ca.php.net/manual/en/mysqli-stmt.bind-param.php#96770
     * @param array $data        	
     *
     * @return array
     */
    private function _ArrayValuesAsRefference($data = array()) {

        if (strnatcmp(phpversion(), '5.3') >= 0) {
            /**
             *
             * @var array
             */
            $refs = array();
            
            foreach ($data as $key => $value) {
                /**
                 *
                 * @var array
                 */
                $refs[$key] = &$data[$key];
            }
            return $refs;
        }
        
        return $data;
    }
}

?>