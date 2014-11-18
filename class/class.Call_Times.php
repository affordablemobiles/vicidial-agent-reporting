<?php

/* ----
|   Call Times
|  ----
|   Access via Campaign_Data, Campaign_Inbound or Campaign_Outbound
|       $camp->fetchData()->fetchCallTimes()->...
|       $camp->byIncoming()->fetchCallTimes()->...
|       $camp->byOutgoing()->fetchCallTimes()->...
|  ----
|   Apply filters by chaining the filter functions...
|       $camp->fetchData()->fetchCallTimes()->byIncoming()->byQueue('BUYM_CS')->byAgent('lrobinson')->byDispo('XFER2S')->getAVGHandleTime();
|  ---- */

class Call_Times {
    private $startEpoch;
    private $endEpoch;
    private $agent;
    private $queue;
    private $dispo;
    private $campaign;
    private $inorout = 'all';

    public function __construct(){
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

    public function byIncoming(){
        $this->inorout = "in";
        return $this;
    }

    public function byOutgoing(){
        $this->inorout = "out";
        return $this;
    }

    public function byCampaign($v){
        $this->campaign = $v;
        return $this;
    }

    public function byQueue($v){
        $this->queue = $v;
        return $this;
    }

    public function byAgent($v){
        $this->agent = $v;
        return $this;
    }

    public function byDispo($v){
        $this->dispo = $v;
        return $this;
    }

    public function getAVGWaitTime(){

    }

    public function getAVGPausedTime(){

    }

    public function getAVGTalkTime(){

    }

    public function getAVGHoldTime(){

    }

    public function getAVGDispoTime(){

    }

    public function getAVGDeadTime(){

    }

    // Wrap = Dispo + Dead
    public function getAVGWrapTime(){

    }

    // Handle = Talk + Hold + Wrap (Disp+Dead)
    public function getAVGHandleTime(){

    }
}
