<?php
// VARIABLES
// =========
require("inc/variables.php");

// define table variables as well


if (SHOW_ERRORS == 1) {
   ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1); 
}

// DATABASE
// ========
require("classes/db.php");
$db = new DB();

// ROUTING STARTS
// ===============
require('classes/command.php');
require('classes/urlinterpreter.php');
require("classes/dispatcher.php");
require('classes/controller.php');

// ROUTING ENDS
// ============


// MISC
// ====
require("inc/functions.php");

?>