<?php

require "../config.php";

$camp = (@$_GET['camp'] != "" ? $_GET['camp'] : 'CS');

$data = new Campaign($camp);

function yday($time){
    return $time - (24*3600);
}

$yday_start = yday(mktime(0,0,0));
$yday_end = yday(mktime(23,59,59));

$data->setTimePeriod($yday_start, $yday_end);

echo "<h1>Data For Campaign: " . $camp . "</h1>\n";

?>
<table border="1">
    <tr>
        <th>Agent Name</th>
        <th>First Login</th>
        <th>Last Activity</th>
        <th>Time Difference</th>
    </tr>
    <?php
    foreach ($data->fetchData()->getAgents() as $agent){
        $data->setAgent($agent);
        echo "<tr>\n";
            echo "<td>" . $agent . "</td>\n";
            $start = $data->fetchData()->getLoginTimes();
            echo "<td>" . date("H:i:s", $start) . "</td>\n";
            $end = $data->fetchData()->getLastActivity();
            echo "<td>" . date("H:i:s", $end) . "</td>\n";
            echo "<td>" . gmdate("H:i:s", ($end-$start)) . "</td>\n";
        echo "</tr>\n";
    }
    ?>
</table>
