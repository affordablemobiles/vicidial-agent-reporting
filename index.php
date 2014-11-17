<?php

ini_set('display_errors', 'On');

date_default_timezone_set("Europe/London");

require "class/class.Queue.php";
require "class/class.Campaign.php";
require "class/class.Campaign_Inbound.php";
require "class/class.Campaign_Outbound.php";
require "class/class.Agent.php";

$db = new MySQLi('127.0.0.1', 'cron', '1234', 'asterisk');

$data = new Campaign_Inbound('CS');

//echo "Direct: " . $data->byQueue('BUYM_CS')->getTotalDirect();
//echo "Direct Answered: " . $data->byQueue('BUYM_CS')->getTotalDirectAnswered();
print_r($data->byQueue('BUYM_CS')->getTotalDirectByDispo());
