<?php

namespace LaravelDebug;

class LaravelDebug {


    private function sendResult($data) {
        $curl = "curl -X POST --ipv4 --max-time 5";
        $curl .= " --header \"Content-Type: application/json\"";
        $curl .= " --header \"Accept: application/json\"";
        $curl .= " --header \"LaravelDebugClient: ".config("laraveldebug.client")."\"";
        $curl .= " --data {$data} {".config("laraveldebug.url")."}";
        $cmd = "({$curl} > /dev/null 2>&1";
        $cmd .= ')&';
        proc_close(proc_open($cmd, [], $pipes));
    }
}