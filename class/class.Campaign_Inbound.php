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

        $this->startEpoch = mktime(0, 0, 0);
        $this->endEpoch = mktime(23, 59, 59);

        $this->_fetchQueues();
        $this->_initObjects();
    }

    public function setTimePeriod($startEpoch, $endEpoch){
        if ($startEpoch < $endEpoch){
            $this->startEpoch = $startEpoch;
            $this->endEpoch = $endEpoch;
        }

        $this->_setTimePeriodAll();
    }

    public function setAgent($agent){
        $this->agent = $agent;

        $this->_setAgentAll();
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
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $this->queues = explode(" ", trim($row['closer_campaigns']));
            array_pop($this->queues);
        } else {
            die("Unable to Fetch Queues in Campaign_Inbound class: " . $this->id);
        }
    }

    private function _initObjects(){
        foreach ($this->queues as $q){
            $this->qobjects[$q] = new Queue($q);
        }
    }

    public function fetchCallTimes(){
        $obj = new Call_Times();
        return $obj->byCampaign($this->id)->byInbound();
    }

    public function byQueue($qid){
        if (is_object($this->qobjects[$qid])){
            return $this->qobjects[$qid];
        } else {
            return false;
        }
    }
}
