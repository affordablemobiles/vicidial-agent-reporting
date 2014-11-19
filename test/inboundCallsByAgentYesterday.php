<?php

require "../config.php";

$camp = (@$_GET['camp'] != "" ? $_GET['camp'] : 'CS');

$data = new Campaign($camp);

$yday_start = mktime(0,0,0) - (24*3600);
$yday_end = mktime(23,59,59) - (24*3600);

$data->setTimePeriod($yday_start, $yday_end);

echo "<h1>Data For Campaign: " . $camp . "</h1>\n";
?>
<table border="1">
    <tr>
        <th>Agent Name</th>
        <th>Calls Answered</th>
        <th>Average Talk Time</th>
        <th>Average Park Time</th>
        <th>Average Dispo Time</th>
        <th>Average Dead Time</th>
        <th>Average Handle Time</th>
        <th>Average Wrap Time</th>
    </tr>
<?php
foreach ($data->fetchData()->getAgents() as $agent){
    $data->setAgent($agent);
    echo "<tr>\n";
        echo "<td>" . $agent . "</td>\n";
        echo "<td>" . $data->byInbound()->byQueue($data->byInbound()->queues)->getTotalAnswered() . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGTalkTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGHoldTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGDispoTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGDeadTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGHandleTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGWrapTime()) . "</td>\n";
    echo "</tr>\n";
}
$data->setAgent(NULL);
    echo "<tr>\n";
        echo "<td><b>Total</b></td>\n";
        echo "<td>" . $data->byInbound()->byQueue($data->byInbound()->queues)->getTotalAnswered() . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGTalkTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGHoldTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGDispoTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGDeadTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGHandleTime()) . "</td>\n";
        echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->getAVGWrapTime()) . "</td>\n";
    echo "</tr>\n";
?>
</table>
