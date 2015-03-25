<?php
class Command
{
    var $Name = '';
    var $Function = '';
    var $Parameters = array();
    var $Data;
    
    
    function Command($controllerName,$functionName,$parameters, $data)
    {
        $this->Parameters = $parameters;
        $this->Name = $controllerName;
        $this->Function = $functionName;
        $this->Data = $data;
    }

    function getControllerName()
    {
        return $this->Name;
    }

    function setControllerName($controllerName)
    {
        $this->Name = $controllerName;
    }

    function getFunction()
    {
        return $this->Function;
    }

    function setFunction($functionName)
    {
        $this->Function = $functionName;
    }

    function getParameters()
    {
        return $this->Parameters;
    }

    function setParameters($controllerParameters)
    {
        $this->Parameters = $controllerParameters;
    }
    
    function setData($d)
    {
        $this->data = $d;
    }
    
    function getData()
    {
        return $this->Data;
    }
}
?>