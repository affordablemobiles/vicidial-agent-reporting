<?php

class Campaign_Outbound {
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
        return $obj->byCampaign($this->id)->byOutbound();
    }

    public function getTotal(){
        return $this->_getTotal("");
    }

    public function getTotalByDispo(){
        global $db;

        $sql = "    SELECT
                        COUNT(*) as 'num',
                        status
                    FROM
                        `vicidial_log`
                    WHERE
                        campaign_id = '" . $db->escape_string($this->id) . "'
                    AND
                        start_epoch > '" . $db->escape_string($this->startEpoch) . "' AND  start_epoch < '" . $db->escape_string($this->endEpoch) . "'
                        " . ( $this->agent != "" ? " AND user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    GROUP BY status";

        $data = array();
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()){
            $data[$row['status']] = $row['num'];
        }

        return $data;
    }

    private function _getTotal($additional_where){
        global $db;

        $sql = "    SELECT
                        COUNT(*) as 'num'
                    FROM
                        `vicidial_log`
                    WHERE
                        campaign_id = '" . $db->escape_string($this->id) . "'
                    AND
                        start_epoch > '" . $db->escape_string($this->startEpoch) . "' AND  start_epoch < '" . $db->escape_string($this->endEpoch) . "'
                        " . ( $additional_where != "" ? " AND " . $additional_where : "" ) . "
                        " . ( $this->agent != "" ? " AND user = '" . $db->escape_string($this->agent) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }
}
