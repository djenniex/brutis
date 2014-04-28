<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.9 $
    Filename:  $Source: /cvshome/brutis/lib/DataService.php,v $
    Date:      $Date: 2010-03-12 07:40:45 $
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

class DataService {
    private static $_instance = NULL;
    private static $key = array();
    private static $data = array();
    private static $dataMode = array();
    private static $dataSize = array();
    private static $accessPattern = array();
    private static $keyPrefix = array();
    private static $keyStartRange = array();
    private static $keyEndRange = array();
    private static $divide = array();

    private function __construct() {
    }

    private function __clone() {
    }

    private static function genBinaryData($name) {
        self::$data[$name] = NULL;
        $size = 0;
        $char = 0;
        while ($size < self::$dataSize[$name]) {
            if ($char > 255) {
                $char = 0;
            }
            self::$data[$name] .= chr($char);
            $size++;
            $char++;
        }
    }

    public static function init() {
        self::$_instance = new DataService();
    }

    public static function register($data, $type) {
        global $settings;
        $name = $data->getName();
        if ($type == 'valueset') { 
            self::$dataSize[$name] =  $data->getSize();
            self::$dataMode[$name] = $data->getMode();
            switch (self::$dataMode[$name]) {
                case DM_STATIC:
                    self::$data[$name] = (string) $data->getData();
                break;
                case DM_GENERATED:
                    self::genBinaryData($name);
                break;
            }
        }
        if ($type == 'keyset') {
            self::$accessPattern[$name] = $data->getPattern();
            self::$keyPrefix[$name] = $data->getPrefix();
            self::$keyStartRange[$name] = $data->getStart();
            self::$keyEndRange[$name] = $data->getEnd();
            self::$key[$name] = ($data->getStart() - 1);
            self::$divide[$name] = $data->getDivide();
            if ($settings['range_id'] != NULL) {
                /*  Reset Range based off RangeID */
                $new_range = get_divided_range($settings['forks'],
                    $settings['range_id'],
                    self::$keyStartRange[$name],
                    self::$keyEndRange[$name]);

                self::$keyStartRange[$name] = $new_range['start'];
                self::$key[$name] = ($new_range['start'] - 1);
                self::$keyEndRange[$name] = $new_range['end'];
            }
        }
    }

    public static function getData() {
        return self::$data;
    }

    public static function getDataSize() {
        return self::$dataSize;
    }

    public static function getNextData($name) {
        return self::$data[$name];
    }
    public static function getDivide($name) {
        return self::$divide[$name];
    }

    public static function getKeyPrefix($name) {
        return self::$keyPrefix[$name];
    }

    public static function getKeyStartRange($name) {
        return self::$keyStartRange[$name];
    }

    public static function getKeyEndRange($name) {
        return self::$keyEndRange[$name];
    }

    public static function getAccessPattern($name) {
        return self::$accessPattern[$name];
    }

    public static function isKey($name) {
        if (isset(self::$key[$name])) {
            return TRUE;
        }
        return FALSE;
    }

    public static function isValue($name) {
        if (isset(self::$data[$name])) {
            return TRUE;
        }
        return FALSE;
    }

    public static function getNextKey($name) {
        $name = (string) $name;
        switch (self::$accessPattern[$name]) {
            case AP_RANDOM:
                $key = self::$keyPrefix[$name]
                    . rand(self::$keyStartRange[$name],
                        self::$keyEndRange[$name]);
                return $key;
            break;
            case AP_SEQUENTIAL:
                if (self::$key[$name] >= self::$keyEndRange[$name]) {
                    self::$key[$name] = (self::$keyStartRange[$name] - 1);
                }
                self::$key[$name]++;
                $key = self::$keyPrefix[$name] . self::$key[$name];
                return $key;
            break;
            case AP_REVERSE_SEQUENTIAL:
                if (self::$key[$name] <= self::$keyEndRange[$name]) {
                    self::$key[$name] = (self::$keyStartRange[$name] - 1);
                }
                self::$key[$name]--;
                $key = self::$keyPrefix[$name] . self::$key[$name];
                return $key;
            break;
        }
    }
}

?>
