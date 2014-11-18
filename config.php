<?php

ini_set('display_errors', 'On');

date_default_timezone_set("Europe/London");

require __DIR__ . "/class/class.Queue.php";
require __DIR__ . "/class/class.Campaign.php";
require __DIR__ . "/class/class.Campaign_Inbound.php";
require __DIR__ . "/class/class.Campaign_Outbound.php";
require __DIR__ . "/class/class.Agent.php";

$db = new MySQLi('127.0.0.1', 'cron', '1234', 'asterisk');
