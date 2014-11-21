<?php

class Campaign_Data {
    public $id;

    private $startEpoch;
    private $endEpoch;
    private $agent;

    public function __construct($id){
        $this->id = $id;

        $this->startEpoch = mktime(0, 0, 0);
        $this->endEpoch = mktime(23, 59, 59);
    }

    public function setTimePeriod($startEpoch, $endEpoch){
        if ($startEpoch < $endEpoch){
            $this->startEpoch = $startEpoch;
            $this->endEpoch = $endEpoch;
        }
    }

    public function setAgent($agent){
        $this->agent = $agent;
    }

    public function fetchCallTimes(){
        $obj = new Call_Times();
        $obj->setTimePeriod($this->startEpoch, $this->endEpoch);
        $obj->setAgent($this->agent);
        return $obj->byCampaign($this->id);
    }

    public function getDispoName($k){
        global $db;

        $map = array(
                        'AFTHRS' => 'Out of Hours'
        );

        $sql = "SELECT status_name FROM vicidial_campaign_statuses WHERE campaign_id = '" . $db->escape_string($this->id) . "' AND status = '" . $db->escape_string($k) . "'";
        $result = $db->query($sql);

        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['status_name'];
        } else {
            if (!empty($map[$k])){
                return $map[$k];
            } else {
                return $k;
            }
        }
    }

    public function getQueueName($k){
        global $db;

        $sql = "SELECT group_name FROM vicidial_inbound_groups WHERE group_id = '" . $db->escape_string($k) . "'";

        $result = $db->query($sql);

        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['group_name'];
        } else {
            return $k;
        }
    }

    public function getAgents(){
        global $db;

        $sql = "    SELECT
                        a.user as 'agent'
                    FROM
                        vicidial_agent_log a
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.campaign_id = '" . $db->escape_string($this->id) . "'
                    GROUP BY a.user";

        $result = $db->query($sql);

        $agents = array();

        while($row = $result->fetch_assoc()){
              $agents[] = $row['agent'];
        }

        return $agents;
    }

    public function getLoginTimes(){
        global $db;

        $sql = "    SELECT
                        user,
                        UNIX_TIMESTAMP(event_time) as time
                    FROM
                        vicidial_agent_log
                    WHERE
                        sub_status = 'LOGIN'
                    AND
                        event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        campaign_id = '" . $db->escape_string($this->id) . "'
                    " . ($this->agent != "" ? " AND user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    GROUP BY user
                    ORDER BY event_time ASC";

        $result = $db->query($sql);

        if ($result->num_rows > 0){
            if ( (!empty($this->agent)) && ($result->num_rows == 1) ){
                $row = $result->fetch_assoc();
                return $row['time'];
            } else {
                $data = array();
                while ($row = $result->fetch_assoc()){
                    $data[$row['user']] = $row['time'];
                }
                return $data;
            }
        } else {
            return 0;
        }
    }

    public function getLastActivity(){
        global $db;

        $sql = "    SELECT
                        user,
                        UNIX_TIMESTAMP(event_time) as time
                    FROM
                        vicidial_agent_log
                    WHERE
                        event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        campaign_id = '" . $db->escape_string($this->id) . "'
                    " . ($this->agent != "" ? " AND user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    GROUP BY user
                    ORDER BY event_time DESC";

        $result = $db->query($sql);

        if ($result->num_rows > 0){
            if ( (!empty($this->agent)) && ($result->num_rows == 1) ){
                $row = $result->fetch_assoc();
                return $row['time'];
            } else {
                $data = array();
                while ($row = $result->fetch_assoc()){
                    $data[$row['user']] = $row['time'];
                }
                return $data;
            }
        } else {
            return 0;
        }
    }
}
