<?php

class Queue {
    private $id;
    private $startEpoch;
    private $endEpoch;
    private $agent;

    public function __construct($id) {
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

    public function getTotal(){
        return $this->_getTotal("");
    }

    public function getTotalAnswered(){
        return $this->_getTotal("status <> 'DROP' AND status <> 'AFTHRS'");
    }

    public function getTotalOOH(){
        return $this->_getTotal("status = 'AFTHRS'");
    }

    public function getTotalDrop(){
        return $this->_getTotal("status = 'DROP'");
    }

    public function getTotalAbandoned(){
        return 0;
    }

    public function getTotalDirect(){
        return $this->_getTotalDirect("");
    }

    public function getTotalDirectAnswered(){
        return $this->_getTotalDirect("status <> 'DROP' AND status <> 'AFTHRS'");
    }

    public function getTotalDirectOOH(){
        return $this->_getTotalDirect("status = 'AFTHRS'");
    }

    public function getTotalDirectDrop(){
        return $this->_getTotalDirect("status = 'DROP'");
    }

    public function getTotalByDispo(){
        global $db;

        $sql = "    SELECT
                        COUNT(*) as 'num',
                        status
                    FROM
                        `vicidial_closer_log`
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

    public function getTotalDirectByDispo(){
        global $db;

        $sql = "    SELECT
                        COUNT(*) as 'num',
                        b.status as 'status'
                    FROM
                        (
                            SELECT
                                COUNT(*) as agentcount,
                                lead_id
                            FROM
                                `vicidial_closer_log`
                            WHERE
                                lead_id IN (SELECT lead_id FROM `vicidial_closer_log` WHERE campaign_id = '" . $db->escape_string($this->id) . "')
                            AND
                                start_epoch > '" . $db->escape_string($this->startEpoch) . "' AND  start_epoch < '" . $db->escape_string($this->endEpoch) . "'
                            " . ( $this->agent != "" ? " AND user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                            GROUP BY lead_id
                        ) a
                    JOIN
                        `vicidial_closer_log` b
                            ON b.closecallid = (SELECT closecallid FROM `vicidial_closer_log` WHERE lead_id = a.lead_id ORDER BY end_epoch DESC LIMIT 1)
                    WHERE
                        a.agentcount = 1
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
                        `vicidial_closer_log`
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

    private function _getTotalDirect($additional_where){
        global $db;

        $sql = "    SELECT
                        COUNT(*) as 'num'
                    FROM
                        (
                            SELECT
                                COUNT(*) as agentcount,
                                lead_id
                            FROM
                                `vicidial_closer_log`
                            WHERE
                                lead_id IN (SELECT lead_id FROM `vicidial_closer_log` WHERE campaign_id = '" . $db->escape_string($this->id) . "')
                            AND
                                start_epoch > '" . $db->escape_string($this->startEpoch) . "' AND  start_epoch < '" . $db->escape_string($this->endEpoch) . "'
                            " . ( $additional_where != "" ? " AND " . $additional_where : "" ) . "
                            " . ( $this->agent != "" ? " AND user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                            GROUP BY lead_id
                        ) a
                    WHERE
                        agentcount = 1";

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }
}
