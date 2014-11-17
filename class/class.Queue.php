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
    }

    public function getTotalAnswered(){
    }

    public function getTotalDirect(){
    }

    public function getTotalDirectAnswered(){
    }

    public function getTotalOOH(){
    }

    public function getTotalDrop(){
    }

    public function getTotalAbandoned(){
    }

    public function getTotalByDispo(){
    }
}
