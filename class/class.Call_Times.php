<?php

/* ----
|   Call Times
|  ----
|   Access via Campaign_Data, Campaign_Inbound or Campaign_Outbound
|       $camp->fetchData()->fetchCallTimes()->...
|       $camp->byInbound()->fetchCallTimes()->...
|       $camp->byOutgoing()->fetchCallTimes()->...
|  ----
|   Apply filters by chaining the filter functions...
|       $camp->fetchData()->fetchCallTimes()->byInbound()->byQueue('BUYM_CS')->byAgent('lrobinson')->byDispo('XFER2S')->getAVGHandleTime();
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

    /* ----
    |   Filters
    |  ---- */

    public function setTimePeriod($startEpoch, $endEpoch){
        if ($startEpoch < $endEpoch){
            $this->startEpoch = $startEpoch;
            $this->endEpoch = $endEpoch;
        }
    }

    public function setAgent($agent){
        $this->agent = $agent;
    }

    public function byInbound(){
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

    /* ----
    |   Average Times
    |  ---- */

    public function getAVGPausedTime(){
        global $db;

        $sql = "    SELECT
                        AVG(pause_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        pause_sec <> 0
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getAVGHoldTime(){
        global $db;

        $sql = "    SELECT
                        AVG(d.parked_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    JOIN
                        parked_log d
                            ON a.uniqueid = d.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        print_r($sql);

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getAVGWaitTime(){
        global $db;

        $sql = "    SELECT
                        AVG(wait_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getAVGTalkTime(){
        global $db;

        $sql = "    SELECT
                        AVG(talk_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getAVGDispoTime(){
        global $db;

        $sql = "    SELECT
                        AVG(dispo_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getAVGDeadTime(){
        global $db;

        $sql = "    SELECT
                        AVG(dead_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    // Wrap = Dispo + Dead
    public function getAVGWrapTime(){
        global $db;

        $sql = "    SELECT
                        AVG(dispo_sec+dead_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    // Handle = Talk + Wrap (Disp+Dead)
    public function getAVGHandleTime(){
        global $db;

        $sql = "    SELECT
                        AVG(talk_sec+dispo_sec+dead_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    /* ----
    |   Total Times
    |  ---- */

    public function getTotalPausedTime(){
        global $db;

        $sql = "    SELECT
                        SUM(pause_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        pause_sec <> 0
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getTotalHoldTime(){
        global $db;

        $sql = "    SELECT
                        SUM(d.parked_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    JOIN
                        parked_log d
                            ON a.uniqueid = d.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getTotalWaitTime(){
        global $db;

        $sql = "    SELECT
                        SUM(wait_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getTotalTalkTime(){
        global $db;

        $sql = "    SELECT
                        SUM(talk_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getTotalDispoTime(){
        global $db;

        $sql = "    SELECT
                        SUM(dispo_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    public function getTotalDeadTime(){
        global $db;

        $sql = "    SELECT
                        SUM(dead_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    // Wrap = Dispo + Dead
    public function getTotalWrapTime(){
        global $db;

        $sql = "    SELECT
                        SUM(dispo_sec+dead_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }

    // Handle = Talk + Wrap (Disp+Dead)
    public function getTotalHandleTime(){
        global $db;

        $sql = "    SELECT
                        SUM(talk_sec+dispo_sec+dead_sec) as 'num'
                    FROM
                        vicidial_agent_log a
                    LEFT OUTER JOIN
                        vicidial_log b
                            ON a.uniqueid = b.uniqueid
                    LEFT OUTER JOIN
                        vicidial_closer_log c
                            ON a.uniqueid = c.uniqueid
                    WHERE
                        a.event_time > FROM_UNIXTIME('" . $db->escape_string($this->startEpoch) . "') AND a.event_time < FROM_UNIXTIME('" . $db->escape_string($this->endEpoch) . "')
                    AND
                        a.status IS NOT NULL
                    " . ($this->campaign != "" ? " AND a.campaign_id = '" . $db->escape_string($this->campaign) . "'" : "" ) . "
                    " . ($this->inorout != "all" ? ( " AND b.uniqueid IS " . ($this->inorout == "out" ? "NOT" : "") . "NULL") : "" ) . "
                    " . ($this->queue != "" ? " AND c.campaign_id = '" . $db->escape_string($this->queue) . "'" : "" ) . "
                    " . ($this->agent != "" ? " AND a.user = '" . $db->escape_string($this->agent) . "'" : "" ) . "
                    " . ($this->dispo != "" ? " AND a.status = '" . $db->escape_string($this->dispo) . "'" : "" );

        $result = $db->query($sql);
        if ($result->num_rows == 1){
            $row = $result->fetch_assoc();
            return $row['num'];
        } else {
            return 0;
        }
    }
}
