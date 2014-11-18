<?php

require "../config.php";

$camp = (@$_GET['camp'] != "" ? $_GET['camp'] : 'CS');

$data = new Campaign($camp);

echo "<h1>Data For Campaign: " . $camp . "</h1>\n";

foreach ($data->byInbound()->queues as $queue){
    echo "<h2>Queue Data: " . $queue . "</h2>\n";

    echo "Total Calls: " . $data->byInbound()->byQueue($queue)->getTotal() . "<br />\n";
    echo "Total Direct Calls: " . $data->byInbound()->byQueue($queue)->getTotalDirect() . "<br />\n";
    echo "<br />\n";
    echo "Calls Answered: " . $data->byInbound()->byQueue($queue)->getTotalAnswered() . "<br />\n";
    echo "Direct Calls Answered: " . $data->byInbound()->byQueue($queue)->getTotalDirectAnswered() . "<br />\n";
    echo "<br />\n";
    echo "Out of Hours: " . $data->byInbound()->byQueue($queue)->getTotalOOH() . "<br />\n";
    echo "Direct Out of Hours: " . $data->byInbound()->byQueue($queue)->getTotalDirectOOH() . "<br />\n";
    echo "<br />\n";
    echo "Dropped Calls: " . $data->byInbound()->byQueue($queue)->getTotalDrop() . "<br />\n";
    echo "Direct Calls Dropped: " . $data->byInbound()->byQueue($queue)->getTotalDirectDrop() . "<br />\n";
    echo "<br />\n";
    echo "<b>Calls By DISPO</b><br />\n";
    echo str_replace("\n", "<br />\n", str_replace("\t", "&nbsp;", var_export($data->byInbound()->byQueue($queue)->getTotalByDispo(), true)));
    echo "<br />\n";
    echo "<b>Direct Calls By DISPO</b><br />\n";
    echo str_replace("\n", "<br />\n", str_replace("\t", "&nbsp;", var_export($data->byInbound()->byQueue($queue)->getTotalDirectByDispo(), true)));
}
