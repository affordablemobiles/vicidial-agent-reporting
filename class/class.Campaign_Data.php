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

    public function getDispoName($k){
        global $db;

        $sql = "SELECT status_name FROM vicidial_campaign_statuses WHERE campaign_id = '" . $db->escape_string($id) . "' AND status = '" . $db->escape_string($k) . "'";
        $result = $db->query($sql);

        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['status_name'];
        } else {
            die('Problem Getting Dispo Name: ' . $k);
        }
    }
}
