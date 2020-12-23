<?php

namespace App\Services\Logger;

interface  LoggerInterface {
    public function log($data = [], $title = '', $level = 1);
    public function read($fileName = '', $endCount = -4);
}
