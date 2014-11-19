<?php

require "../config.php";

print_r($data->byInbound()->fetchCallTimes()->byQueue('BUYM_CS')->byAgent('lrobinson')->byDispo('XFER2S')->getAVGWaitTime());
