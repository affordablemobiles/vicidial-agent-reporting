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

echo "Total: " . $data->byQueue('BUYM_CS')->getTotal() . "\n";
echo "Total Direct: " . $data->byQueue('BUYM_CS')->getTotalDirect() . "\n";
echo "\n";
echo "Answered: " . $data->byQueue('BUYM_CS')->getTotalAnswered() . "\n";
echo "Direct Answered: " . $data->byQueue('BUYM_CS')->getTotalDirectAnswered() . "\n";
echo "\n";
echo "Out of Hours: " . $data->byQueue('BUYM_CS')->getTotalOOH() . "\n";
echo "Direct Out of Hours: " . $data->byQueue('BUYM_CS')->getTotalDirectOOH() . "\n";
echo "\n";
echo "Dropped: " . $data->byQueue('BUYM_CS')->getTotalDrop() . "\n";
echo "Direct Dropped: " . $data->byQueue('BUYM_CS')->getTotalDirectDrop() . "\n";
echo "\n";
print_r($data->byQueue('BUYM_CS')->getTotalByDispo());
echo "\n";
print_r($data->byQueue('BUYM_CS')->getTotalDirectByDispo());
