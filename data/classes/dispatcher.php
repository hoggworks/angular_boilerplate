<?php 
class Dispatcher
{
    var $Command;
    var $db;
 
    function Dispatcher(&$command, $db)
    {
        $this->Command = $command;
        $this->db = $db;
    }
 
    function isValid($controllerName)
    {
        
        $fn = 'controllers/'.$controllerName.'.php';
        if (file_exists($fn))
        {
            return true;
        } else {
            return false;
        }
    }
 
    function Dispatch()
    {
        $controllerName = $this->Command->getControllerName();
 
        if($this->isValid($controllerName) == false)
        {
            $controllerName = 'error';
        }
        
        $fn = 'controllers/'.$controllerName.'.php';
        require($fn);
        
        if ($controllerName != 'email') {
            require_once('controllers/email.php');
        }
        
        $controllerClass = $controllerName."Controller";
        
        //$controllerClass = "errorController";
        if ($controllerName != 'email') {
            $cn = 'email';
        
            $controllerClass2 = $cn."Controller";
            $e = new $controllerClass2($this->Command);
            $controller = new $controllerClass($this->Command, $e);
        } else {
            $controller = new $controllerClass($this->Command);
     }
        $controller->execute($this->db);
      }
}
?>