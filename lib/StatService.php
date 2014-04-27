<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.7 $
    Filename:  $Source: /cvshome/brutis/lib/StatService.php,v $
    Date:      $Date: 2010-03-16 20:42:39 $
    Copyright:

    Software License Agreement (BSD License)

    Copyright (c) 2010, Violin Memory, Inc.
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are
    met:

    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.

    * Redistributions in binary form must reproduce the above
      copyright notice, this list of conditions and the following disclaimer
      in the documentation and/or other materials provided with the
      distribution.

    * Neither the name of Violin Memory, Inc. nor the names of its
      contributors may be used to endorse or promote products derived from
      this software without specific prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
    "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
    LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
    A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
    OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
    SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
    LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
    DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
    THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
    OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

class StatService {
    private static $_instance = NULL;
    private static $sock = NULL;
    private static $counters = array();
    private static $settings = array();
    private static $types = array();
    private static $decimals = array();
    private static $timestamp = 0;
    private static $events = array();

    private function __construct() {
    }

    private function __clone() {
    }

    private static function connectCollector() {
        global $settings;

        $errno = 0;
        $errstr = NULL;

        self::$sock = @fsockopen(self::$settings['collector']['host'],
            self::$settings['collector']['port'],
            $errono, $errmsg, COLLECTOR_TIMEOUT);

        if (!self::$sock) {
            echo("Error connecting to collector.\n");
            echo("extended: ". $errmsg . "\n");
            exit(1);
        }

        $data['memcache'] = self::$settings['servers'];
        $serialized_data = serialize($data);

        $result = NULL;
        while ($result != "0\n") {
            fwrite(self::$sock, strlen($serialized_data) . "\n");
            fwrite(self::$sock, $serialized_data . "\n");
            $result = fgets(self::$sock);
            if ($result == "1\n") {
                echo("Error reporting to collector\n");
                exit(1);
            }
        }
    }

    private static function disconnectCollector() {
        fclose(self::$sock);
    }

    public static function report() {
        $data['counters'] = self::$counters;
        $data['events'] = self::$events;
        $serialized_data = serialize($data);

        $result = NULL;
        while ($result != "0\n") {
            fwrite(self::$sock, strlen($serialized_data) . "\n");
            fwrite(self::$sock, $serialized_data . "\n");
            $result = fgets(self::$sock);
            if ($result == "1\n") {
                echo("Error reporting to collector\n");
                exit(1);
            }
        }
        self::$counters = array();
        self::$events = array();
    }

    public static function init($settings) {
        self::$_instance = new StatService();
        self::$settings = $settings;
        if (self::$settings['collector']) {
            self::connectCollector();
        }
    }

    public static function isRegistered($name) {
        if (isset(self::$counters[$name])) {
            return TRUE;
        }
        return FALSE;
    }

    public static function increment($name, $offset = 1) {
        if (isset(self::$counters[$name])) {
            self::$counters[$name] = self::$counters[$name] + $offset;
        } else {
            self::$counters[$name] = $offset;
        }
        return TRUE;
    }

    public static function decrement($name, $offset = 1) {
        if (isset(self::$counters[$name])) {
            self::$counters[$name] == self::$counters[$name] - $offset;
        } else {
            self::$counters[$name] = ($offset * -1);
        }
        return TRUE;
    }

    public static function getCounter($name) {
        $name = (string) $name;
        if (isset(self::$counters[$name])) {
            return self::$counters[$name]; 
        }
        return 0;
    }

    public static function newEvent($event) {
        $event = (string) $event;
        $count = count(self::$events);
        self::$events[$count] = $event;
        return $count;
    }

    public static function getEvents() {
        return self::$events;
    }
}

?>
