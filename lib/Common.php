<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.37 $
    Filename:  $Source: /cvshome/brutis/lib/Common.php,v $
    Date:      $Date: 2010-03-26 21:33:17 $
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

define('DM_GENERATED', 'generated');
define('DM_STATIC', 'static');
define('AP_RANDOM', 'random');
define('AP_SEQUENTIAL', 'sequential');
define('AP_REVERSE_SEQUENTIAL', 'reverse_sequential');
define('STAT_TOTAL_OPS', 'operations');
define('STAT_SET', 'sets');
define('STAT_SET_FAIL', 'set_fails');
define('STAT_GET', 'gets');
define('STAT_HIT', 'hits');
define('STAT_MISS', 'misses');
define('STAT_APPEND', 'appends');
define('STAT_APPEND_FAIL', 'append_fails');
define('STAT_DELETE', 'deletes');
define('STAT_DELETE_FAIL', 'delete_fails');
define('STAT_REPLACE', 'replaces');
define('STAT_REPLACE_FAIL', 'replace_fails');
define('STAT_INCREMENT', 'increment');
define('STAT_INCREMENT_FAIL', 'increment_fails');
define('STAT_DECREMENT', 'decrement');
define('STAT_DECREMENT_FAIL', 'decrement_fails');
define('STAT_LATENCY', 'latency');
define('MEMCACHE_DANGA', 'danga');
define('MEMCACHE_LIBMEMCACHE', 'libmemcache');
define('MEMCACHE_TIMEOUT', '5');
define('MEMCACHE_SET', 'set');
define('MEMCACHE_SETMULTI', 'setmulti');
define('MEMCACHE_GET', 'get');
define('MEMCACHE_GETMULTI', 'getmulti');
define('MEMCACHE_REPLACE', 'replace');
define('MEMCACHE_APPEND', 'append');
define('MEMCACHE_DELETE', 'delete');
define('MEMCACHE_INCREMENT', 'increment');
define('MEMCACHE_DECREMENT', 'decrement');
define('BRUTIS_EVENT', 'event');
define('BRUTIS_PREFIX', 'brutis-');
define('BRUTIS_SLEEP', 'sleep');
define('TM_SLEEP_BETWEEN_JOBS', 0);
define('COLLECTOR_TIMEOUT', 5);
define('COLLECTOR_SAMPLE_TIME', 5);
define('CLIENT_REPORT_TIME', 2);
define('COLLECTOR_COLUMN_SIZE', 10);
define('COLLECTOR_RIGHT', 'right');
define('COLLECTOR_LEFT', 'left');
define('MAX_CLIENT_PROCCESSES', 64);



function set_default_library() {
    if (test_libmemcache()) {
        define('LIBRARY_DEFAULT', MEMCACHE_LIBMEMCACHE);
    } else {
        if (test_danga()) {
            define('LIBRARY_DEFAULT', MEMCACHE_DANGA);
        } else {
            trigger_error(
                "Neither libmemcache or danga memcache modules are available!\n" .
                " Exiting."
                , E_USER_ERROR);
        }
    }
}



function test_danga() {
    if (class_exists('Memcache', FALSE)) {
        return TRUE;
    } else {
        return FALSE;
    }
}


function test_libmemcache() {
    if (class_exists("Memcached", FALSE)) {
        return TRUE;
    } else {
        return FALSE;
    }
}


function validate_int($string) {
    if (is_int($int)) {
        return TRUE;
    }
    return FALSE;
}

function validate_string($string) {
    if (preg_match('/[^.a-zA-Z0-9\040\137\055]{1,255}$/', $string)) {
        return FALSE;
    }
    return TRUE;
}

function validate_host($host) {
    if (preg_match('/^[.a-zA-Z0-9_-]{1,255}$/', $host)) {
        return TRUE;
    }
    return FALSE;
}

function validate_ip($ip) {
    if (preg_match('/(\d+).(\d+).(\d+).(\d+)/', $ip)) {
        return TRUE;
    }
    return FALSE;
}


function get_divided_range($forks, $range_id, $start, $end) {
    if ($range_id > $forks) {
        echo("Error range greater then number of forks!\n");
        exit(1);
    }
    $data = array();

    /* Get the total number of keys in the rage */
    $data['total_keys'] = ($end - ($start - 1));

    /* Get the total number of keys per fork */
    $data['keys_per_range'] = (int) ($data['total_keys'] / $forks); 

    /* Figure out if there are any remaining keys after dividing up the range */
    $data['remainder'] = 
        $data['total_keys'] - ($forks * $data['keys_per_range']); 

    if ($range_id == 1) {
        $data['start'] = $start;
        $data['end'] = $data['keys_per_range'];
    } else {
        $data['start'] = $start + (($range_id - 1) * $data['keys_per_range']) ;
        if ($range_id == $forks) {
            $data['end'] = $data['start'] 
                + ($data['keys_per_range'] - 1) 
                + $data['remainder'];
        } else {
            $data['end'] =  $data['start'] + ($data['keys_per_range'] - 1);
        }
    }
    return $data;
}

function host_to_addr($host) {
/*      host_to_addr()
        Convert host to IP Address
        @params string $host hostname/ip to validate        
        @return bool
*/
    if (validate_ip($host)) {
        return $host;
    } elseif (validate_host($host)) {
        $ip = gethostbyname($host);
        if ($ip == $host) {
            echo ("Error resolving dns name: $host\n");
            exit(1);
        } else {
            return $ip;
        }
    } else {
        echo ("Error parsing $host\n");
        exit(1);
    }
}

function parse_server_list($servers) {
/*      parseServerList()
        @params mixed $servers runtime argument array
        @params string $arg variable name that contains server list setting
*/
    global $settings;

    if (!isset($settings['serverlist'])) {
        $settings['serverlist'] = $servers;
    }

    if (ereg(",", $servers)) {
        $split_options = split(',', $servers);
        foreach ($split_options as $current) {
            parse_server_list($current);
        }
    } else {
        $server = array();
        $server['server'] = host_to_addr('localhost');
        $server['tcp_port'] = '11211';
        $server['udp_port'] = '11211'; 
        if (ereg(":", $servers)) {
            $curr_explode = explode(':', $servers);
            $host = trim($curr_explode[0]);
            $host = strtolower($host);

            $server['server'] = host_to_addr($host);
            if (isset($curr_explode[1])) {
                $server['tcp_port'] = (int) $curr_explode[1];
            }
            if (isset($curr_explode[2])) {
                $server['udp_port'] = (int) $curr_explode[2];
            }
        } else {
            $host = trim($servers);
            $host = strtolower($host);
            $server['server'] = host_to_addr($host);
            $server['tcp_port'] = 11211;
            $server['udp_port'] = 0;
        }
        $settings['servers'][] = $server;
    }
}

function parse_collector($collector) {
    global $settings;

    if (ereg(':', $collector)) {
        $split_collector = split(':', $collector);
        $host = trim($split_collector[0]);
        $port = (int) $split_collector[1];
        $settings['collector']['host'] = host_to_addr($host);
        $settings['collector']['port'] = $port; 
    } else {
        $settings['collector']['host'] = host_to_addr($collector);
        $settings['collector']['port'] = '9091'; 
    }
}

function setup_library($library) {
    global $memcache;

    $library = (string) trim(strtolower($library));
    switch ($library) {
        case MEMCACHE_DANGA:
            $memcache = new DangaMemcache();
        break;
        case MEMCACHE_LIBMEMCACHE:
            $memcache = new Libmemcache();
        break;
        default:
            echo("Error Unknown Library\n");
            exit(1);
        break;
    }
    $memcache->connect();
}

function parse_library($library) {
    global $settings;
    global $memcache;

    $library = (string) trim(strtolower($library));
    switch ($library) {
        case MEMCACHE_DANGA:
            $settings['library'] = MEMCACHE_DANGA;
        break;
        case MEMCACHE_LIBMEMCACHE:
            $settings['library'] = MEMCACHE_LIBMEMCACHE;
        break;
        default:
            echo("Error Unknown Library\n");
            exit(1);
        break;
    }
    return $settings['library'];
}

function parse_test($test) {
    global $settings; 

    $settings['test'] = trim($test);
    $filename = $settings['test'];


    if (class_exists('DOMDocument', FALSE)) {
        $dom = new DOMDocument;
        if ($dom->Load($filename)) {
            if (!$dom->validate()) {
                trigger_error("Error parsing XML test.", E_USER_ERROR);
            }
        } else {
            trigger_error("Error opening XML test. File not found."
                , E_USER_ERROR);
        }
        $xml = simplexml_import_dom($dom);
    } else {
        trigger_error(
            "Warning could not validate XML. Class DOMDocument missing"
            , E_USER_NOTICE);
        $fp = fopen($filename, 'r');
        $data = fread($fp, filesize($filename));
        fclose($fp);
        $xml = new SimpleXMLElement($data);
    }

    $object = new BrutisTest();
    $object->loadTest($xml->test);
    $settings['config'] = $object;
}

?>
