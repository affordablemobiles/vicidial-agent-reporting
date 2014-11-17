<?php

class Campaign_Inbound {
    public $id;
    public $queues = array();

    private $qobjects = array();
    private $startEpoch;
    private $endEpoch;
    private $agent;

    public function __construct($id){
        $this->id = $id;

        $this->_fetchQueues();
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

    private function _setTimePeriodAll(){
        foreach ($this->qobjects as $name => &$obj){
            $obj->setTimePeriod($this->startEpoch, $this->endEpoch);
        }
    }

    private function _setAgentAll(){
        foreach ($this->qobjects as $name => &$obj){
            $obj->setAgent($this->agent);
        }
    }

    private function _fetchQueues(){
        global $db;

        $sql = "    SELECT
                        closer_campaigns
                    FROM
                        vicidial_campaigns
                    WHERE
                        campaign_id = '" . $db->escape_string($this->id) . "'";

        $result = $db->query($sql);
        if ($result->num_rows() == 1){
            $row = $result->fetch_assoc();
            print_r($row);
        } else {
            die("Unable to Fetch Queues in Campaign_Inbound class: " . $this->id);
        }
    }

    public function byQueue($qid){
        if (is_object($this->qobjects[$qid])){
            return $this->qobjects[$qid];
        } else {
            return false;
        }
    }
}
