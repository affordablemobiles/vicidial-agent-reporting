<?php

require "../config.php";

$camp = (@$_GET['camp'] != "" ? $_GET['camp'] : 'CS');

$data = new Campaign($camp);

function yday($time){
    return $time - (24*3600);
}

$yday_start = yday(mktime(0,0,0));
$yday_end = yday(mktime(23,59,59));

echo "<h1>Data For Campaign: " . $camp . "</h1>\n";

foreach ($data->fetchData()->getAgents() as $agent){
    $data->setAgent($agent);
    echo "<h2>Agent: " . $agent . "</h2>\n";
?>
<table border="1">
    <tr>
        <th>Time Period</th>
        <th>Calls Taken</th>
        <th>Calls Made</th>
        <th>Talk Time</th>
        <th>Park Time</th>
        <th>Dispo Time</th>
        <th>Dead Time</th>
        <th>Handle Time</th>
        <th>Wrap Time</th>
    </tr>
    <?php
    for ($i = 0; $i < 24; $i++){
        $start = yday(mktime($i, 0, 0));
        $end = yday(mktime($i, 59, 59));
        $data->setTimePeriod($start,$end);
        echo "<tr>\n";
            echo "<td>" . date("H:i:s", $start) . " - " . date("H:i:s", $end) . "</td>\n";
            echo "<td>" . $data->byInbound()->byQueue($data->byInbound()->queues)->getTotalAnswered() . "</td>\n";
            echo "<td>" . $data->byOutbound()->getTotal() . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalTalkTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalHoldTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalDispoTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalDeadTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalHandleTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalWrapTime()) . "</td>\n";
        echo "</tr>\n";
    }
    $data->setTimePeriod($yday_start, $yday_end);
        echo "<tr>\n";
            echo "<td><b>Total</b></td>\n";
            echo "<td>" . $data->byInbound()->byQueue($data->byInbound()->queues)->getTotalAnswered() . "</td>\n";
            echo "<td>" . $data->byOutbound()->getTotal() . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalTalkTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalHoldTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalDispoTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalDeadTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalHandleTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->fetchData()->fetchCallTimes()->getTotalWrapTime()) . "</td>\n";
        echo "</tr>\n";
    ?>
</table>
<?php
}
