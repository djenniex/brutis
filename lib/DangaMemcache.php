<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.17 $
    Filename:  $Source: /cvshome/brutis/lib/DangaMemcache.php,v $
    Date:      $Date: 2010-03-19 08:06:47 $
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


class DangaMemcache {
    private $memcache = NULL;
    private $version = NULL;

    public function __construct() {
        $this->name = MEMCACHE_DANGA;
        if (class_exists('Memcache', FALSE)) {
            if (class_exists("MemcachePool", FALSE)) {
                $this->version = 3;
                $this->memcache = new MemcachePool();
            } else {
                $this->version = 2;
                $this->memcache = new Memcache();
            }
        } else {
            trigger_error("Danga library doesn't seem to be installed!"
                , E_USER_ERROR);
        }
    }

    public function connect() {
        global $settings;

        foreach ($settings['servers'] as $server) {     
            $this->memcache->addServer(
                $server['server'], 
                $server['tcp_port'], 
                FALSE, 
                1,
                MEMCACHE_TIMEOUT
            );
        }
    }

    public function disconnect() {
        $this->memcache->close();
    }

    public function set($keyset, $dataset, $expire) {
        $key = DataService::getNextKey($keyset);
        $data = DataService::getNextData($dataset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_SET);
        $result = $this->memcache->set($key, $data, NULL, $expire);
        if ($result != FALSE) {
            return TRUE;
        } else {
            StatService::increment(STAT_SET_FAIL);
            return FALSE;
        }
    }

    public function setMulti($keyset, $dataset, $expire, $count) {
        trigger_error
            ("Error, Danga memcache library does not support multisets!\n"
            , E_USER_ERROR);
        return FALSE;
    }

    public function get($keyset) {
        $key = DataService::getNextKey($keyset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_GET);
        $result = $this->memcache->get($key);
        if ($result != FALSE) {
            StatService::increment(STAT_HIT);
            return TRUE;
        } else {
            StatService::increment(STAT_MISS);
            return FALSE;
        }
    }

    public function getMulti($keyset, $count) {
        for ($i = 0; $i < $count; $i++) {
            $keys[] = DataService::getNextKey($keyset);
        }
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_GET, $count);
        $result = $this->memcache->get($keys);
        if ($result != FALSE) {
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
        $result = $this->memcache->delete($key);
        if ($result != FALSE) {
            return TRUE;
        } else {
            StatService::increment(STAT_DELETE_FAIL);
            return FALSE;
        }
    }

    public function append($keyset, $dataset) {
        if ($this->version == 2) {
            trigger_error(
                "Error doing append. append is not supported in danga v2\n"
                , E_USER_ERROR);
        }
        $key = DataService::getNextKey($keyset);
        $data = DataService::getNextData($dataset);
        StatService::increment(STAT_TOTAL_OPS);
        StatService::increment(STAT_APPEND);
        $result = $this->memcache->append($key, $data);
        if ($result != FALSE) {
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
        $result = $this->memcache->replace($key, $data, $expire);
        if ($result != FALSE) {
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
        $result = $this->memcache->increment($key, $offset);
        if ($result != FALSE) {
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
        $result = $this->memcache->decrement($key, $offset);
        if ($result !== NULL) {
            return TRUE;
        } else {
            StatService::increment(STAT_DECREMENT_FAIL);
            return FALSE;
        }
    }
}

?>
