<?php

require "class/class.Queue.php";
require "class/class.Campaign.php";
require "class/class.Campaign_Inbound.php";
require "class/class.Campaign_Outbound.php";
require "class/class.Agent.php";

$db = new MySQLi('127.0.0.1', 'cron', '1234', 'asterisk');

$data = new Campaign_Inbound('CS');
