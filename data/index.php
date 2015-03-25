<?php
require("inc/init.php");

//$obj = $db->insert("user_teams")
    //->insertFields(["user_id", "year"])
    //->insertValues(["20", "(select current_year from site_configs)"]);

//$obj = $db->update("user_teams")->set(["enabled = 1", "created_at = NOW()"])->where(["id = 291","year = 2013"]);

/*$obj = $db->group("user_id")->select("user_teams")->order("user_id", "DESC")->all();

foreach ($obj as $user) {
    echo $user->team_name."<br/>";
}*/

/*echo $insert = $db
    ->insert("user_teams")
    ->fields(["year", "user_id"])
    ->values(["2013", "22"])->getQuery();*/

//$db->select("user_teams")->where(["id = '291'", "id = '293'"], "or")->go();
//echo $db->select("user_teams")->where(["id = '29'", "id = '293'"], "or")->go();



//echo $db->lastId();
//echo '.'.$urlInterpreter->getCommand()->Function;

// redirect as appropriate
$urlInterpreter = new URLInterpreter();
$command = $urlInterpreter->getCommand();
$dispatcher = new Dispatcher($command, $db);
global $commandResult;
$dispatcher->Dispatch();

?>