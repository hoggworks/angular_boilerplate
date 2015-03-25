<?php
class URLInterpreter
{
    var $Command;

    function URLInterpreter()
    {
        $requestURI = explode('/', $_SERVER['REQUEST_URI']);
        $scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
        $commandArray = array_diff_assoc($requestURI,$scriptName);
        $commandArray = array_values($commandArray);
        $controllerName = $commandArray[0];
        $controllerFunction = $commandArray[1];
        $parameters = array_slice($commandArray,2);
        
        // Check if the url is the root.
        // if it is then set the command to the root controller.
        // and _default function.
        if($controllerName == '') { $controllerName = 'root'; }
        
        // get user data
        $data = file_get_contents("php://input");
        $obj = json_decode($data);


        $this->Command = new Command($controllerName,$controllerFunction,$parameters, $obj);
    }

    function getCommand()
    {
        return $this->Command;
    }
}
?>