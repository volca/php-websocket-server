<?php

function saveLog($msg) {
    // DATE_ATOM - msg
    $msgType = 3;
    $logFile = "devices_" . date("Ymd") . ".log";
    $msg = date(DATE_ATOM) . " - $msg\n";
    error_log($msg, $msgType, $logFile);
}

function saveData($data) {
    $msgType = 3;
    $logFile = "data_" . date("Ymd") . ".log";
    error_log($data, $msgType, $logFile);
}

$server = new swoole_websocket_server("0.0.0.0", 8000);

$server->on('open', function (swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    //saveData($frame->data);
    $arr = explode("\r\n", $frame->data);
    $meta = array_shift($arr);
    array_shift($arr);
    if (empty($meta)) {
        $device = new stdClass();
    } else {
        $device = json_decode($meta);
    }
    $device->beacon_count = count($arr);
    saveLog(json_encode($device));
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
