<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.19 $
    Filename:  $Source: /cvshome/brutis/lib/BrutisTest.php,v $
    Date:      $Date: 2010-03-26 18:53:20 $
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

class BrutisTest {
    private $name = NULL;
    private $desc = NULL;
    private $version = NULL;
    private $jobs = array();

    private function importOperations(&$job, $operations) {
        foreach ($operations as $operation) {
            if (!validate_string($operation['type'])) {
                trigger_error('Error reading operation type. ' .
                    'Contains invalid characters.', E_USER_ERROR);
            }
            $type =  strtolower((string) $operation['type']);

            switch ($type) {
                case MEMCACHE_SET:
                    $current_op = $job->addOperation(MEMCACHE_SET);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    if (!$job->getValueset($operation['valueset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown valueset " .$operation['valueset']
                            , E_USER_ERROR);
                    }
                    $current_op->setValueset($operation['valueset']);
                    $current_op->setExpire($operation['expire']);
                break;
                case MEMCACHE_SETMULTI:
                    $current_op = $job->addOperation(MEMCACHE_SETMULTI);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    if (!$job->getValueset($operation['valueset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown valueset " .$operation['valueset']
                            , E_USER_ERROR);
                    }
                    $current_op->setValueset($operation['valueset']);
                    $current_op->setExpire($operation['expire']);
                    $current_op->setCount($operation['count']);
                break;
                case MEMCACHE_GET:
                    $current_op = $job->addOperation(MEMCACHE_GET);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                break;
                case MEMCACHE_GETMULTI:
                    $current_op = $job->addOperation(MEMCACHE_GETMULTI);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    $current_op->setCount($operation['count']);
                break;
                case MEMCACHE_DELETE:
                    $current_op = $job->addOperation(MEMCACHE_DELETE);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                break;
                case MEMCACHE_APPEND:
                    $current_op = $job->addOperation(MEMCACHE_APPEND);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    if (!$job->getValueset($operation['valueset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown valueset " .$operation['valueset']
                            , E_USER_ERROR);
                    }
                    $current_op->setValueset($operation['valueset']);
                break;
                case MEMCACHE_REPLACE:
                    $current_op = $job->addOperation(MEMCACHE_REPLACE);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    if (!$job->getValueset($operation['valueset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown valueset " .$operation['valueset']
                            , E_USER_ERROR);
                    }
                    $current_op->setValueset($operation['valueset']);
                    $current_op->setExpire($operation['expire']);
                break;
                case MEMCACHE_INCREMENT:
                    $current_op = $job->addOperation(MEMCACHE_INCREMENT);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    $current_op->setOffset($operation['offset']);
                break;
                case MEMCACHE_DECREMENT:
                    $current_op = $job->addOperation(MEMCACHE_DECREMENT);
                    if (!$job->getKeyset($operation['keyset'])) {
                        trigger_error("Error adding operation."
                            . "Unknown keyset " .$operation['keyset']
                            , E_USER_ERROR);
                    }
                    $current_op->setKeyset($operation['keyset']);
                    $current_op->setOffset($operation['offset']);
                break;
                case BRUTIS_SLEEP:
                    $current_op = $job->addOperation(BRUTIS_SLEEP);
                    $current_op->setCount($operation['count']);
                break;
            }
        }
    }

    public function loadTest($xml) {

        $this->setName($xml['name']);
        $this->setDesc($xml['desc']);
        $this->setVersion($xml['version']);

        $jobs = $xml->jobset;
        foreach ($jobs as $job) {
            $current_job = $this->addJob($job['name']);
            $current_job->setForks($job['forks']);
            if (isset($job['time'])) {
                $current_job->setMaxTime($job['time']);
            }
            if (isset($job['operations'])) {
                $current_job->setMaxOperations($job['operations']);
            }
            $current_job->setLibrary($job['library']);
            if (isset($job->libmemcache)) {
                $options = $job->libmemcache->option;
                foreach ($options as $option) {
                    $current_option = $current_job->addOption($option['name']);
                    $current_option->setValue($option);
                }
            }
            if (isset($job->dataset)) {
                $keyset = $job->dataset->key;
                foreach ($keyset as $key) {
                    $current_keyset = $current_job->addKeyset($key['name']);
                    $current_keyset->setStart($key['start']);
                    $current_keyset->setEnd($key['end']);
                    $current_keyset->setPattern($key['pattern']);
                    $current_keyset->setPrefix($key['prefix']);
                    $current_keyset->setDivide($key['divide']);
                }
                $valueset = $job->dataset->value;
                foreach ($valueset as $value) {
                    $current_valueset = $current_job->addValueSet($value['name']);
                    $current_valueset->setMode($value['mode']);
                    if ($current_valueset->getMode() == DM_STATIC) {
                        $current_valueset->setData($value);
                    }
                    if ($current_valueset->getMode() == DM_GENERATED) {
                        $current_valueset->setSize((int) $value['size']);
                    }
                }
            }
            if (isset($job->operations)) {
                $this->importOperations($current_job
                    , $job->operations->operation);
            } else {
                trigger_error("Error no operations for job ". $job->getName()
                    , E_USER_ERROR);
            }
        }
    }

    public function setName($name) {
        if (!validate_string($name)) {
            trigger_error("Error reading test name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->name =  (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setDesc($desc) {
        if (!validate_string($desc)) {
            trigger_error("Error reading test desc: '$desc'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->desc = (string) $desc;
    }

    public function getDesc() {
        return $this->desc;
    }

    public function setVersion($version) {
        if (!validate_string($version)) {
            trigger_error("Error reading test version: '$version'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->version = (string) $version;
    }

    public function getVersion() {
        return $this->version;
    }

    public function addJob($name) {
        if (!validate_string($name)) {
            trigger_error("Error reading job name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (@isset($this->jobs[$name])) {
            trigger_error(
                "Error adding job " . $name . 
                ". Job with that name already exists!\n"
                , E_USER_NOTICE);
        }
        $this->jobs[$name] = new BrutisJob();
        $this->jobs[$name]->setName($name);
        $jobref = &$this->jobs[$name];
        return $jobref;
    }

    public function deleteJob($name) {
        if (!validate_string($name)) {
            trigger_error("Error deleting job name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 

        $name =  strtolower((string) $name);
        if (!isset($this->jobs[$name])) {
            trigger_error("Error removing job " . $name . 
                ". Job does not exist!\n"
                , E_USER_NOTICE);
        }
        unset($this->jobs[$name]);
    }

    public function getJob($name) {
        if (!validate_string($name)) {
            trigger_error("Error getting job name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (!isset($this->jobs[$name])) {
            trigger_error("Error getting job " . $name . 
                ". Job does not exist!\n"
                , E_USER_NOTICE);
        }
        $jobref = &$this->jobs[$name];
        return $jobref;
    }

    public function getJobs() {
        $jobref = &$this->jobs;
        return $jobref;
    }
}
    
class BrutisJob {
    private $name = NULL;
    private $forks = 1;
    private $max_time = NULL;
    private $max_operations = NULL;
    private $library = MEMCACHE_LIBMEMCACHE;
    private $options = NULL;
    private $keysets = NULL;
    private $valuesets = NULL;
    private $operations = NULL;
    private $nextOperationId = 0;

    public function setName($name) {
        if (!validate_string($name)) {
            trigger_error("Error setting job name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->name =  strtolower((string) $name);
    }

    public function getName() {
        return $this->name;
    }

    public function setForks($forks) {
        if ($forks === NULL) {
            $this->forks = 1;
        } else {
            $forks = (int) $forks;
            if ($forks <= 0 || $forks >= MAX_CLIENT_PROCCESSES) {
                trigger_error("Error setting forks to " . $forks . 
                    ". Must be between 0 and " . MAX_CLIENT_PROCESSES, E_USER_ERROR);
            }
            $this->forks = $forks;
        }
    }

    public function getForks() {
        return $this->forks;
    }

    public function setMaxTime($time) {
        $time = (int) $time;
        if ($time <= 0) {
            trigger_error("Error setting max time to " . $time . 
                ". Must be greater then 0", E_USER_ERROR);
        }
        $this->max_time = $time;
    }

    public function getMaxTime() {
        return $this->max_time;
    }

    public function setMaxOperations($operations) {
        $operations = (int) $operations;
        if ($operations <= 0) {
            trigger_error("Error setting operations to " . $operations . 
                ". Must be greater then 0", E_USER_ERROR);
        }
        $this->max_operations = $operations;
    }

    public function getMaxOperations() {
        return $this->max_operations;
    }

    public function setLibrary($library) {
        if ($library === NULL) {
            $this->library = LIBRARY_DEFAULT;
        } else {
            if (!validate_string($library)) {
                trigger_error("Error setting library name: '$library'. " .
                    'Contains invalid characters.', E_USER_ERROR);
            } 
            $library =  strtolower((string) $library);
            switch ($library) {
                case MEMCACHE_DANGA:
                    $this->library = MEMCACHE_DANGA;
                break;
                case MEMCACHE_LIBMEMCACHE:
                    $this->library = MEMCACHE_LIBMEMCACHE;
                break;
                default:
                    trigger_error("Error setting library " . $library . 
                        ". Library not supported. Try libmemcache or danga"
                        , E_USER_ERROR);
                break;
            }
        }
    }

    public function getLibrary() {
        return $this->library;
    }

    public function addOption($name) {
        if (!validate_string($name)) {
            trigger_error("Error adding option name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  (string) $name;

        if (isset($this->options[$name])) {
            trigger_error("Error adding option " . $option . 
                ". Option already exist!\n"
                , E_USER_ERROR);
        }
        $this->options[$name] = new BrutisLibraryOption();
        $this->options[$name]->setName($name);
        $optionref = &$this->options[$name];
        return $optionref;
    }

    public function getOption($name) {
        if (!validate_string($name)) {
            trigger_error("Error getting option: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  (string) $name;

        if (!isset($this->options[$name])) {
            trigger_error("Error getting option " . $option . 
                ". Option does not exist!\n"
                , E_USER_ERROR);
        }
        $optionref = &$this->options[$name];
        return $optionref;
    }

    public function getOptions() {
        $optionref = &$this->options;
        return $optionref;
    }

    public function addKeyset($name) {
        if (!validate_string($name)) {
            trigger_error("Error adding keyset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (isset($this->keysets[$name])) {
            trigger_error(
                "Error adding keyset " . $name . 
                ". Keyset with that name already exists!"
                , E_USER_NOTICE);
        }
        $this->keysets[$name] = new BrutisKeySet();
        $this->keysets[$name]->setName($name);
        $keysetref = &$this->keysets[$name];
        return $keysetref;
    }

    public function deleteKeyset($name) {
        if (!validate_string($name)) {
            trigger_error("Error deleting keyset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (!isset($this->keysets[$name])) {
            trigger_error("Error removing keyset " . $name . 
                ". Keyset does not exist!\n"
                , E_USER_NOTICE);
        }
        unset($this->keysets[$name]);
    }

    public function getKeyset($name) {
        if (!validate_string($name)) {
            trigger_error("Error getting keyset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (!isset($this->keysets[$name])) {
            trigger_error("Error getting keyset " . $name .
                ". Keyset does not exist!\n"
                , E_USER_ERROR); 
        }
        $keysetref = &$this->keysets[$name];
        return $keysetref;
    }

    public function getKeysets() {
        $keysetref = &$this->keysets;
        return $keysetref;
    }

    public function addValueset($name) {
        if (!validate_string($name)) {
            trigger_error("Error adding valueset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (isset($this->valuesets[$name])) {
            trigger_error(
                "Error adding valueset " . $name . 
                ". Valueset with that name already exists!"
                , E_USER_NOTICE);
        }
        $this->valuesets[$name] = new BrutisValueSet();
        $this->valuesets[$name]->setName($name);
        $datasetref = &$this->valuesets[$name];
        return $datasetref;
    }

    public function deleteValueset($name) {
        if (!validate_string($name)) {
            trigger_error("Error deleting valueset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (!isset($this->valuesets[$name])) {
            trigger_error("Error removing valueset " . $name . 
                ". Dataset does not exist!\n"
                , E_USER_NOTICE);
        }
        unset($this->valuesets[$name]);
    }

    public function getValueset($name) {
        if (!validate_string($name)) {
            trigger_error("Error getting valueset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $name =  strtolower((string) $name);

        if (!isset($this->valuesets[$name])) {
            trigger_error("Error getting valueset " . $name .
                ". Valueset does not exist!\n"
                , E_USER_ERROR); 
        }
        $valuesetref = &$this->valuesets[$name];
        return $valuesetref;
    }

    public function getValuesets() {
        $valuesetref = &$this->valuesets;
        return $valuesetref;
    }

    public function addOperation($type) {
        if (!validate_string($type)) {
            trigger_error("Error adding operation: '$type'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $type =  strtolower((string) $type);

        $id = $this->nextOperationId;
        $this->nextOperationId++;
        switch ($type) {
            case MEMCACHE_SET:
                $this->operations[$id] = new BrutisOperationSet($id);
            break;
            case MEMCACHE_SETMULTI:
                $this->operations[$id] = new BrutisOperationSetMulti($id);
            break;
            case MEMCACHE_GET:
                $this->operations[$id] = new BrutisOperationGet($id);
            break;
            case MEMCACHE_GETMULTI:
                $this->operations[$id] = new BrutisOperationGetMulti($id);
            break;
            case MEMCACHE_DELETE:
                $this->operations[$id] = new BrutisOperationDelete($id);
            break;
            case MEMCACHE_APPEND:
                $this->operations[$id] = new BrutisOperationAppend($id);
            break;
            case MEMCACHE_REPLACE:
                $this->operations[$id] = new BrutisOperationReplace($id);
            break;
            case MEMCACHE_INCREMENT:
                $this->operations[$id] = new BrutisOperationIncrement($id);
            break;
            case MEMCACHE_DECREMENT:
                $this->operations[$id] = new BrutisOperationDecrement($id);
            break;
            case BRUTIS_SLEEP:
                $this->operations[$id] = new BrutisOperationSleep($id);
            break;
        }
        $operationref = &$this->operations[$id];
        return $operationref;
    }

    public function deleteOperation($id) {
        $id = (int) $id;
        if (!isset($this->operations[$id])) {
            trigger_error("Error deleting operation #" . $id . 
                ". Operation does not exist!"
                , E_USER_NOTICE);
        }
        unset($this->operations[$id]);
    }

    public function getOperations() {
        if ($this->operations !== NULL) {
            $operationsref = &$this->operations;
            return $operationsref;
        }
        return NULL;
    }
}

class BrutisKeySet {
    private $name = NULL;
    private $start = NULL;
    private $end = NULL;
    private $pattern = AP_RANDOM;
    private $prefix = BRUTIS_PREFIX;
    private $divide = FALSE;

    public function setName($name) {
        if (!validate_string($name)) {
            trigger_error("Error setting keyset name: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->name =  strtolower((string) $name);
    }

    public function getName() {
        return $this->name;
    }

    public function setStart($start) {
        $start = (int) $start;
        if ($start < 0) {
            trigger_error("Error setting start " . $start .
                ". Keyset start needs to be greater then or equal to 0"
                , E_USER_ERROR);
        }
        $this->start = $start;
    }

    public function getStart() {
        return $this->start;
    }

    public function setEnd($end) {
        $end = (int) $end;
        if ($end < $this->start) {
            trigger_error("Error setting end ". $end .
            ". Keyset end must be greated then start", E_USER_ERROR);
        }
        $this->end = $end;
    }

    public function getEnd() {
        return $this->end;
    }

    public function setPattern($pattern) {
        if ($pattern === NULL) {
            $pattern = AP_RANDOM;
        }
        if (!validate_string($pattern)) {
            trigger_error("Error setting pattern: '$pattern'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $pattern =  strtolower((string) $pattern);

        if ($pattern === NULL) {
            $pattern = AP_RANDOM;
        }
        switch ($pattern) {
            case AP_RANDOM:
                $this->pattern = AP_RANDOM;
            break;
            case AP_SEQUENTIAL:
                $this->pattern = AP_SEQUENTIAL;
            break;
            case AP_REVERSE_SEQUENTIAL:
                $this->pattern = AP_REVERSE_SEQUENTIAL;
            break;
            default:
                trigger_error("Error setting pattern " . $pattern . 
                    ". Keyset Pattern Not found"
                    , E_USER_ERROR);
            break;
        }
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function setPrefix($prefix) {
        if (!validate_string($prefix)) {
            trigger_error("Error setting prefix: '$prefix'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->prefix =  (string) $prefix;

        if ($prefix === NULL) {
            $this->prefix = BRUTIS_PREFIX;
        }
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function setDivide($divide) {
        if (is_bool($divide)) {
            $this->divide = $divide;
        } else {
            if (preg_match('/true/i', $divide)) {
                $this->divide = TRUE;
            } else {
                $this->divide = FALSE;
            }
        }
    }

    public function getDivide() {
        return $this->divide;
    }
}

class BrutisValueSet {
    private $name = NULL;
    private $mode = DM_GENERATED;
    private $size = NULL;
    private $data = NULL;

    public function setName($name) {
        if (!validate_string($name)) {
            trigger_error("Error setting valueset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->name =  strtolower((string) $name);
    }

    public function getName() {
        return $this->name;
    }

    public function setMode($mode) {
        if (!validate_string($mode)) {
            trigger_error("Error setting mode: '$mode'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $mode =  strtolower((string) $mode);

        switch ($mode) {
            case DM_GENERATED:
                $this->mode = DM_GENERATED;
            break;
            case DM_STATIC:
                $this->mode = DM_STATIC;
            break;
            default:
                trigger_error("Error setting mode " . $mode . 
                    ". Valueset Mode does not exist!\n"
                    , E_USER_ERROR);
            break;
        }
    }

    public function getMode() {
        return $this->mode;
    }

    public function setSize($size) {
        $size = (int) $size;
        if ($this->mode != DM_GENERATED) {
            trigger_error("Error setting size " . $size . 
                ". Mode is not set to " . DM_GENERATED . "!\n"
                , E_USER_ERROR);
        } elseif ($size <= 0 ) {
            trigger_error("Error setting size " . $size .
                ". Generated valueset needs size greater then 0", E_USER_ERROR);
        }
        $this->size = $size;
    }

    public function getSize() {
        return $this->size;
    }

    public function setData($data) {
        $data = htmlspecialchars($data);
        if ($this->mode != DM_STATIC) {
            trigger_error("Error setting data " . $data . 
                ". Mode is not set to static!\n"
                , E_USER_ERROR);
        }
        $this->data = $data;
        $this->size = strlen($data);
    }

    public function getData() {
        return htmlspecialchars_decode($this->data);
    }
}

class BrutisLibraryOption  {
    private $name = NULL;
    private $value = NULL;

    public function setName($name) {
        if (!validate_string($name)) {
            trigger_error("Error setting library option: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->name =  (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setValue($value) {
        if (!validate_string($value)) {
            trigger_error("Error setting library option value: '$value'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->value =  (string) $value;
    }

    public function getValue() {
        return $this->value;
    }
}

class BrutisOperationSet {
    private $id = NULL;
    private $keyset = NULL;
    private $valueset = NULL;
    private $expire = 0; 

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$name'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function setValueset($valueset) {
        if (!validate_string($valueset)) {
            trigger_error("Error setting valueset: '$valueset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->valueset =  strtolower((string) $valueset);
    }

    public function getValueset() {
        return $this->valueset;
    }

    public function setExpire($expire) {
        if ($expire === NULL) {
           $this->expire = 0;
        } else {
            $this->expire = (int) $expire;
        }
    }

    public function getExpire() {
        return $this->expire;
    }

    public function exec() {
        global $memcache;
        $memcache->set($this->keyset, $this->valueset, $this->expire);
    }
}

class BrutisOperationSetMulti {
    private $id = NULL;
    private $keyset = NULL;
    private $valueset = NULL;
    private $count = 1;
    private $expire = 0; 

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function setValueset($valueset) {
        if (!validate_string($valueset)) {
            trigger_error("Error setting valueset: '$valueset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        } 
        $this->valueset =  strtolower((string) $valueset);
    }

    public function getValueset() {
        return $this->valueset;
    }

    public function setCount($count) {
        $count = (int) $count;
        if ($count < 1) {
            trigger_error("Error setting count " . $count . 
                ". MultiSet count needs to be greated then 0\n"
                , E_USER_ERROR);
        }
        $this->count = $count;
    }

    public function getCount() {
        return $this->count;
    }

    public function setExpire($expire) {
        if ($expire === NULL) {
            $expire = 0;
        }
        $this->expire = (int) $expire;
    }

    public function getExpire() {
        return $this->expire;
    }

    public function exec() {
        global $memcache;
        $memcache->setMulti($this->keyset
            , $this->valueset
            , $this->expire
            , $this->count
            );
    }
}

class BrutisOperationGet {
    private $id = NULL;
    private $keyset = NULL;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function exec() {
        global $memcache;
        $memcache->get($this->keyset);
    }
}

class BrutisOperationGetMulti {
    private $id = NULL;
    private $keyset = NULL;
    private $count = 1;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }
    
    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function setCount($count) {
        $count = (int) $count;
        if ($count <= 0) {
            trigger_error("Error setting count " . $count .
                ". MultiGet count needs to be greated then 0\n"
                , E_USER_ERROR);
        }
        $this->count = $count;
    }

    public function getCount() {
        return $this->count;
    }

    public function exec() {
        global $memcache;
        $memcache->getMulti($this->keyset, $this->count);
    }
}

class BrutisOperationDelete {
    private $id = NULL;
    private $keyset = NULL;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function exec() {
        global $memcache;
        $memcache->delete($this->keyset);
    }
}

class BrutisOperationAppend {
    private $id = NULL;
    private $keyset = NULL;
    private $valueset = NULL;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function setValueset($valueset) {
        if (!validate_string($valueset)) {
            trigger_error("Error setting valueset: '$valueset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->valueset =  strtolower((string) $valueset);
    }

    public function getValueset() {
        return $this->valueset;
    }

    public function exec() {
        global $memcache;
        $memcache->append($this->keyset, $this->valueset);
    }
}

class BrutisOperationReplace {
    private $id = NULL;
    private $keyset = NULL;
    private $valueset = NULL;
    private $expire = 0;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }
    
    public function setValueset($valueset) {
        if (!validate_string($valueset)) {
            trigger_error("Error setting valueset: '$valueset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->valueset =  strtolower((string) $valueset);
    }

    public function getValueset() {
        return $this->valueset;
    }

    public function setExpire($expire) {
        if ($expire === NULL) {
            $expire = 0;
        }
        $this->expire = (int) $expire;
    }

    public function getExpire() {
        return $this->expire;
    }

    public function exec() {
        global $memcache;
        $memcache->replace($this->keyset, $this->valueset, $this->expire);
    }
}

class BrutisOperationIncrement {
    private $id = NULL;
    private $keyset = NULL;
    private $offset = 0;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function setOffset($offset) {
        if ($offset === NULL) {
            $offset = 0;
        }
        $this->offset = (int) $offset;
    } 

    public function getOffset() {
        return $this->offset;
    }

    public function exec() {
        global $memcache;
        $memcache->increment($this->keyset, $this->offset);
    }
}

class BrutisOperationDecrement {
    private $id = NULL;
    private $keyset = NULL;
    private $offset = 0;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setKeyset($keyset) {
        if (!validate_string($keyset)) {
            trigger_error("Error setting keyset: '$keyset'. " .
                'Contains invalid characters.', E_USER_ERROR);
        }
        $this->keyset =  strtolower((string) $keyset);
    }

    public function getKeyset() {
        return $this->keyset;
    }

    public function setOffset($offset) {
        if ($offset === NULL) {
            $offset = 0;
        }
        $this->offset = (int) $offset;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function exec() {
        global $memcache;
        $memcache->decrement($this->keyset, $this->offset);
    }
}

class BrutisOperationSleep {
    private $id = NULL;
    private $count = 1;

    public function __construct($id) {
        $this->id = (int) $id;
    }

    public function getId() {
       return $this->id;
    }

    public function setCount($count) {
        $count = (int) $count;
        if ($count <= 0) {
            trigger_error('Sleep count needs to be greater then 0.'
                , E_USER_ERROR);
        }
        $this->count = $count;
    }

    public function getCount() {
        return $this->count;
    }
    public function exec() {
        StatService::report();
        time_nanosleep(0, $this->count);
    }
}

?>
