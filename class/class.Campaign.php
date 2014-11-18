<?php

class Campaign {
    public $id;

    private $inbound;
    private $outbound;

    private $startEpoch;
    private $endEpoch;
    private $agent;

    public function __construct($id){
        $this->id = $id;

        $this->startEpoch = mktime(0, 0, 0);
        $this->endEpoch = mktime(23, 59, 59);

        $this->inbound = new Campaign_Inbound($this->id);
        $this->outbound = new Campaign_Outbound($this->id);
    }

    public function setTimePeriod($startEpoch, $endEpoch){
        if ($startEpoch < $endEpoch){
            $this->startEpoch = $startEpoch;
            $this->endEpoch = $endEpoch;
        }

        $this->inbound->setTimePeriod($this->startEpoch, $this->endEpoch);
        $this->outbound->setTimePeriod($this->startEpoch, $this->endEpoch);
    }

    public function setAgent($agent){
        $this->agent = $agent;

        $this->inbound->setAgent($this->agent);
        $this->outbound->setAgent($this->agent);
    }

    public function byInbound(){
        return $this->inbound;
    }

    public function byOutbound(){
        return $this->outbound;
    }
}
