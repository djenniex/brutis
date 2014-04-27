<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.19 $
    Filename:  $Source: /cvshome/brutis/lib/Libmemcache.php,v $
    Date:      $Date: 2010-03-24 22:06:17 $
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

function check_memcache_result($mc) {
/* Libmemcached result codes

    * Memcached::RES_SUCCESS: 0
    * Memcached::RES_FAILURE: 1
    * Memcached::RES_HOST_LOOKUP_FAILURE: 2
    * Memcached::RES_UNKNOWN_READ_FAILURE: 7
    * Memcached::RES_PROTOCOL_ERROR: 8
    * Memcached::RES_CLIENT_ERROR: 9
    * Memcached::RES_SERVER_ERROR: 10
    * Memcached::RES_WRITE_FAILURE: 5
    * Memcached::RES_DATA_EXISTS: 12
    * Memcached::RES_NOTSTORED: 14
    * Memcached::RES_NOTFOUND: 16
    * Memcached::RES_PARTIAL_READ: 18
    * Memcached::RES_SOME_ERRORS: 19
    * Memcached::RES_NO_SERVERS: 20
    * Memcached::RES_END: 21
    * Memcached::RES_ERRNO: 25
    * Memcached::RES_BUFFERED: 31
    * Memcached::RES_TIMEOUT: 30
    * Memcached::RES_BAD_KEY_PROVIDED: 32
    * Memcached::RES_CONNECTION_SOCKET_CREATE_FAILURE: 11
    * Memcached::RES_PAYLOAD_FAILURE: -1001
*/

    $rc = $mc->getResultCode();
    switch ($rc) {
        case 0:
            return TRUE;
        break;
        case 16:
            /* Libmemcached not found. */
            return FALSE;
        break;
        default:
            StatService::newEvent("Libmemcache error $rc: " 
                . $mc->getResultMessage() . ". Sleeping 5 seconds");
            sleep(5);
            return FALSE;
        break;
    }
}

class Libmemcache {
    private $memcache = NULL;

    public function __construct() {
        $this->name = 'Libmemcache';
        if (class_exists("Memcached", FALSE)) {
            $this->memcache = new Memcached();
        } else {
            trigger_error("libmemcache library doesn't seem to be installed!"
                , E_USER_ERROR);
        }
    }

    public function connect() {
        global $settings;

        if (!isset($settings['servers'])) {
            echo("No Servers defined\n");
           exit(1);
        }

        foreach ($settings['servers'] as $server) {     
            $this->memcache->addServer($server['server'], $server['tcp_port']);
            check_memcache_result($this->memcache);
        }
    }

    public function disconnect() {
        unset($this->memcache);
    }

    public function setOption($option, $value) {
        if (preg_match('/^[:a-zA-Z0-9_-]{1,255}$/', $option)) {
            eval('$e_option = ' . $option . ';');
        } else {
            echo("Invalid option: '" . $option ."'\n");
            exit(1);
        }
        if (preg_match('/^[:a-zA-Z0-9_-]{1,255}$/', $value)) {
            eval('$e_value = ' . $value . ';');
        } else {
            echo("Invalid value: '" . $value ."'\n");
            exit(1);
        }

        return $this->memcache->setOption($e_option, $e_value);
    }

    public function getOption($option) {
        return $this->memcache->getOption($option);
    }

    public function set($keyset, $dataset, $expire) {
        $key = DataService::getNextKey($keyset);
        $data = DataService::getNextData($dataset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_SET);

        $this->memcache->set($key, $data, $expire);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_SET_FAIL);
            return FALSE;
        }
    }

    public function setMulti($keyset, $dataset, $expire, $count) {
        for ($i = 0; $i < $count; $i++) {
            $key = DataService::getNextKey($keyset);
            $keys[$key] = DataService::getNextData($dataset);
        }
        StatService::increment(STAT_TOTAL_OPS, $count);
        StatService::increment(STAT_SET, $count);
        $this->memcache->setMulti($keys, $expire);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_SET_FAIL, $count);
            return FALSE;
        }
    }

    public function get($keyset) {
        $key = DataService::getNextKey($keyset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_GET);
        $this->memcache->get($key);
        if (check_memcache_result($this->memcache)) {
            StatService::increment(STAT_HIT);
            return TRUE;
        } else {
            StatService::increment(STAT_MISS);
            return FALSE;
        }
    }

    public function getMulti($keyset, $count) {
        for ($i = 0; $i < $count; $i++) {
            $keys[$i] = DataService::getNextKey($keyset);
        }
        StatService::increment(STAT_TOTAL_OPS, $count);
        StatService::increment(STAT_GET, $count);
        $result = $this->memcache->getMulti($keys, $expire);
        if (check_memcache_result($this->memcache)) {
            if ($count == count($result)) {
                StatService::increment(STAT_HIT, $count);
            } else {
                $hits = $count - count($result);
                $misses = $count - $hits;
                StatService::increment(STAT_HIT, $hits);
                StatService::increment(STAT_MISS, $misses);
            }
            return TRUE;
        } else {
            StatService::increment(STAT_MISS, $count);
            return FALSE;
        }
    }

    public function delete($keyset) {
        $key = DataService::getNextKey($keyset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_DELETE);
        $this->memcache->delete($key);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_DELETE_FAIL);
            return FALSE;
        }
    }

    public function append($keyset, $dataset) {
        $key = DataService::getNextKey($keyset);
        $data = DataService::getNextData($dataset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_APPEND);
        $this->memcache->append($key, $data);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_APPEND_FAIL);
            return FALSE;
        }
    }

    public function replace($keyset, $dataset, $expire) {
        $key = DataService::getNextKey($keyset);
        $data = DataService::getNextData($dataset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_REPLACE);
        $this->memcache->replace($key, $data, $expire);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_REPLACE_FAIL);
            return FALSE;
        }
    }

    public function increment($keyset, $offset) {
        $key = DataService::getNextKey($keyset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_INCREMENT);
        $this->memcache->increment($key, $offset);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_INCREMENT_FAIL);
            return FALSE;
        }
    }

    public function decrement($keyset, $offset) {
        $key = DataService::getNextKey($keyset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_DECREMENT);
        $this->memcache->decrement($key, $offset);
        if (check_memcache_result($this->memcache)) {
            return TRUE;
        } else {
            StatService::increment(STAT_DECREMENT_FAIL);
            return FALSE;
        }
    }
}

?>
