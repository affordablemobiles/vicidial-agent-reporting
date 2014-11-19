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
}
