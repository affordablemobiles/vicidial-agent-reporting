<?php

require "../config.php";

$camp = (@$_GET['camp'] != "" ? $_GET['camp'] : 'CS');

$data = new Campaign($camp);

echo "<h1>Data For Campaign: " . $camp . "</h1>\n";

print_r($data->byInbound()->fetchCallTimes()->byQueue('BUYM_CS')->byAgent('lrobinson')->byDispo('XFER2S')->getAVGWaitTime());
