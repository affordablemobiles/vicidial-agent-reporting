<?php

require "../config.php";

$camp = (@$_GET['camp'] != "" ? $_GET['camp'] : 'CS');

$data = new Campaign($camp);

$yday_start = mktime(0,0,0) - (24*3600);
$yday_end = mktime(23,59,59) - (24*3600);

$data->setTimePeriod($yday_start, $yday_end);

echo "<h1>Data For Campaign: " . $camp . "</h1>\n";
echo "<b>OOH Percentage is of total calls, not calls offered, which is not shown. This is why you could have 100% answered and 33% OOH for the same queue.</b><br />\n";
?>
<table border="1">
    <tr>
        <th>Queue Name</th>
        <th>Calls Offered</th>
        <th>Direct Offered</th>
        <th>Direct %</th>
        <th>Calls Answered</th>
        <th>% Answered</th>
        <th>% SLA of Ans</th>
        <th>OOH</th>
        <th>% OOH</th>
        <th>Drop</th>
        <th>% Drop</th>
        <th>Avg Handle</th>
        <th>Avg Wait</th>
        <th>Avg Wrap</th>
    </tr>
    <?php
    foreach ($data->byInbound()->queues as $queue){
        echo "<tr>\n";
            echo "<td>" . $data->fetchData()->getQueueName($queue) . "</td>\n";
            echo "<td>" . $data->byInbound()->byQueue($queue)->getTotalOffered() . "</td>\n";
            echo "<td>" . $data->byInbound()->byQueue($queue)->getTotalDirectOffered() . "</td>\n";
            echo "<td>" . @round(( ( $data->byInbound()->byQueue($queue)->getTotalDirectOffered() / $data->byInbound()->byQueue($queue)->getTotalOffered() ) * 100 )) . "%</td>\n";
            echo "<td>" . $data->byInbound()->byQueue($queue)->getTotalAnswered() . "</td>\n";
            echo "<td>" . @round(( ( $data->byInbound()->byQueue($queue)->getTotalAnswered() / $data->byInbound()->byQueue($queue)->getTotalOffered() ) * 100 )) . "%</td>\n";
            echo "<td>N/A</td>\n";
            echo "<td>" . $data->byInbound()->byQueue($queue)->getTotalOOH() . "</td>\n";
            echo "<td>" . @round(( ( $data->byInbound()->byQueue($queue)->getTotalOOH() / $data->byInbound()->byQueue($queue)->getTotal() ) * 100 )) . "%</td>\n";
            echo "<td>" . $data->byInbound()->byQueue($queue)->getTotalDrop() . "</td>\n";
            echo "<td>" . @round(( ( $data->byInbound()->byQueue($queue)->getTotalDrop() / $data->byInbound()->byQueue($queue)->getTotalOffered() ) * 100 )) . "%</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->byQueue($queue)->getAVGHandleTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->byQueue($queue)->getAVGWaitTime()) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", (int)$data->byInbound()->fetchCallTimes()->byQueue($queue)->getAVGWrapTime()) . "</td>\n";
        echo "</tr>\n";
    }
    ?>
</table>
