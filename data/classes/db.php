<?php

class DB {
    private $q;
    private $db;
    private $s;
    
    private $table;
    private $operation;
    private $join;
    private $set;
    private $whereAnd;
    private $whereOr;
    private $order;
    private $group;
    private $limit;
    private $from;
    private $insert_fields;
    private $insert_values;
    
    function __construct() {
        try {
            $this->db = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME,
                DB_USER,
                DB_PASS
            );
            
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        catch(PDOException $e) {  
            echo $e->getMessage();  
        }  
        
        $this->join = array();
        $this->whereAnd = array();
        $this->whereOr = array();
        $this->what = array();
        $this->set = array();
        $this->insert_fields = array();
        $this->insert_values = array();
    }
    
    public function query($query)
    {
        $this->s = $this->db->prepare($query);
        
        return $this;
    }
    
    public function ready()
    {
        $this->q = $this->operation . " " . $this->assembleWhat() . $this->from ." " . $this->table . $this->assembleSet() ." " . $this->assembleJoin() . " " . $this->assembleWhere(). " " . $this->group . " " . $this->order . " " . $this->limit . $this->getInsertFields();
        
        $this->query($this->q);
        return $this;   
    }
    
    
    public function delete($table)
    {
        $this->table = $table;
        $this->operation = "DELETE FROM";
        
        $this->ready();
        return $this;
    }
    
    public function insert($table) 
    {
        $this->table = $table;
        $this->operation = "INSERT INTO";
        $this->ready();
        return $this;
    }
    
    public function update($table)
    {
        $this->table = $table;
        $this->operation = "UPDATE";
        $this->ready();
        return $this;
    }
    
    public function set($args)
    {
        if (is_array($args)) {
            for ($i = 0; $i<count($args); $i++) {
                $this->set[]= $args[$i];   
            }
        } else {
            $this->set[]= $args;
        }
        
        $this->ready();
        
        return $this;
    }
    
    public function fields($insert_fields) {
        if (is_array($insert_fields)) {
            for ($i = 0; $i<count($insert_fields); $i++) {
                $this->insert_fields[]= $insert_fields[$i];
            }
        } else {
            $this->insert_fields[] = $insert_fields;    
        }
        $this->ready();
        return $this;
    }
    
    public function pair($f, $v) {
        $this->insert_fields[]= $f;
        $this->insert_values[]= $v;
        $this->ready();
        return $this;
            
    }
    
    public function values($insert_values) {
        if (is_array($insert_values)) {
            for ($i = 0; $i<count($insert_values); $i++) {
                $this->insert_values[]= $insert_values[$i];   
            }
        } else {
            $this->insert_values = $insert_values;
        }
        $this->ready();
        return $this;
    }
    
    public function getInsertFields()
    {
        if (count($this->insert_fields) > 0 && count($this->insert_values) > 0) {
            $nfv = array();
            foreach ($this->insert_values as $f) {
                
                if ($f != "NOW()") {
                    $f= "'".$f."'";
                }
                $nfv[]= $f;
            }
            
            
            
            $fv = implode(", ", $nfv);
                
            $r = " (`".implode("`, `", $this->insert_fields)."`) VALUES (".$fv.")";
        } else {
            $r = "";
        }
        
        return $r;
    }
    
    public function select($table) 
    {
        $this->table = $table;   
        $this->operation = "SELECT";
        $this->from = " FROM";
        $this->ready();
        return $this;
    }
    
    public function order($field, $direction = 'ASC')
    {
        // default to ascending
        if ($direction != "ASC" && $direction != "DESC") {
            $direction = "ASC";
        }
        
        $this->order =  "ORDER BY $field $direction";
        $this->ready();
        return $this;
    }
    
    public function group($group) {
        $this->group = "GROUP BY $group";
        $this->ready();
        return $this;
    }
    
    public function leftJoin($join) {
        if (is_array($join)) {
            for ($i = 0; $i<count($join); $i++) {
                $this->join[]= $join[$i];
            }
        } else {
            $this->join[]= $join;
        }
        $this->ready();
        
        return $this;
    }
    
    public function limit($offset,$limit)
    {
        $this->limit = "LIMIT $offset, $limit";
        $this->ready();
        return $this;
    }
    
    public function what($what) {
        if (is_array($what)) {
            for ($i = 0; $i<count($what); $i++) {
                $this->what[]= $what[$i];
            }
        } else {
            $this->what[]= $what;
        }
        $this->ready();
        
        return $this;
    }
    
    public function getQuery()
    {
        return $this->q;   
    }
    
    public function execute() 
    {
        // clear components of query
        $this->table = null;
        $this->operation = null;
        $this->from = null;
        $this->join = array();
        $this->whereAnd = array();
        $this->whereOr = array();
        $this->what = array();
        $this->order = null;
        $this->group = null;
        $this->limit = null;
        $this->insert_fields = array();
        $this->insert_values = array();
        $this->set = array();
        
        
        
        return $this->s->execute();
    }
    
    public function go()
    {
        return $this->execute();   
    }

    public function lastId()
    {
        return $this->db->lastInsertId();
    }
    public function all() 
    {
        $this->execute();
        return $this->s->fetchAll(PDO::FETCH_OBJ);
    }

    public function single() 
    {
        $this->execute();
        return $this->s->fetchObject();
    }
    
    public function getCount()
    {
        $this->execute();
        return $this->s->fetchColumn();
    }
    
    public function where($args, $type = "and") {
        if (is_array($args)) {
            for ($i =0; $i<count($args); $i++) {
                if ($type == "and") {
                    $this->whereAnd[]= $args[$i];
                } else if ($type == "or") {
                    $this->whereOr[]= $args[$i];
                }
            }
        } else {
            if ($type == "and") {
                $this->whereAnd[]= $args;
            } else if ($type == "or") {
                $this->whereOr[]= $args;
            }
        }
        $this->ready();
        return $this;
    }
    
    public function assembleWhere()
    {
        $r = '';
        
        if (count($this->whereAnd) == 0 && count($this->whereOr) == 0 && $this->operation != "INSERT INTO") {
            // nothing defined; default to *;
            $r = "";
        } else if ((count($this->whereAnd) > 0 || count($this->whereOr)) && $this->operation != "INSERT INTO"){
            $r = "WHERE ";
            if (count($this->whereAnd) > 0) {
                if (count($this->whereAnd) > 1) {
                    $r .= "(";
                }
                
                $r .= implode(" AND ", $this->whereAnd);  
               
                if (count($this->whereAnd) > 1) {
                    $r .= ")";
                }
            } 
            
            if (count($this->whereAnd) > 0 && count($this->whereOr) > 0) {
                $r .= " OR ";
            }
            
            if (count($this->whereOr) > 0) {
                $r .= implode(" OR ", $this->whereOr);   
            }
        }
        
        return $r;
    }
    
    public function assembleWhat()
    {
        $r = '';
        
        if (count($this->what) == 0 && $this->operation == "SELECT") {
            // nothing defined; default to *;
            $r = "*";
        } else {
            $r .= implode(", ", $this->what);  
        }
        
        return $r;
    }
    
    public function assembleSet()
    {
        $r = '';
        
        if ($this->operation == "UPDATE") {
            if (count($this->set) > 0) {
                $r .= ' SET ';
            }

            $r .= implode(", ", $this->set);
        }
        
        return $r;
    }
    
    public function assembleJoin()
    {
        if (count($this->join) > 0) {
            $r = "left join " . implode(" ", $this->join);
        } else {
            $r =  '';
        }
        return $r;
    }
    
}
?>