<?php
/*  Author:    $Author: zyounker $
    Revision:  $Revision: 1.6 $
    Filename:  $Source: /cvshome/brutis/lib/CollectorStat.php,v $
    Date:      $Date: 2010-03-19 07:35:24 $
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


class CollectorStat {
    private $name = NULL;
    private $total = NULL;
    private $last = NULL;
    private $decimals = 2;

    public function __construct($name) {
        $this->name = (string) $name;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setDecimals($decimal) {
        $this->decimals = (int) $decimal;
    }

    public function getDecimals() {
        return $this->decimals;
    }

    public function updateValue($client_id, $value) {
        $client_id = (int) $client_id;
        if (isset($this->total[$client_id])) {
            $this->total[$client_id] = $this->total[$client_id] + $value;
        } else {
            $this->total[$client_id] = $value;
        }
    }
    public function asFValue() {
        $answer = 0;
        if (($total = $this->asRTotal()) != 0) {
            if (isset($this->last)) {
                foreach ($this->last as $value) {
                    $answer = $answer + $value;
                }
                $answer = $total - $answer;
            } else {
                $answer = $total;
            }
        }
        return number_format($answer, $this->decimals);
    }

    public function asRValue() {
        $answer = 0;
        if (($total = $this->asRTotal()) != 0) {
            if (isset($this->last)) {
                foreach ($this->last as $value) {
                    $answer = $answer + $value;
                }
                $answer = $total - $answer;
            } else {
                $answer = $total;
            } 
        }
        return $answer;
    }

    public function asFSample($time) {
        $answer = $this->asRValue();

        if ($answer != 0 && $time != 0) {
            $answer = $answer / $time;
            return number_format($answer, $this->decimals);
        } else {
            return number_format(0, $this->decimals);
        }
    } 

    public function asRSample($time) {
        $answer = $this->asRValue();
        if ($answer != 0 && $time != 0) {
            $answer = $answer / $time;
            return number_format($answer, $this->decimals);
        } else {
           return number_format(0, $this->decimals);
        }
    } 

    public function asFTotal() {
        $answer = 0;
        if (isset($this->total)) {
            foreach ($this->total as $value) {
                $answer = $answer + $value;
            }
        }
        return number_format($answer, $this->decimals);
    }

    public function asRTotal() {
        $answer = 0;
        if (isset($this->total)) {
            foreach ($this->total as $value) {
                $answer = $answer + $value;
            }
        }
        return $answer;
    }

    public function asFSampleTotal($time) {
        $answer = $this->asRTotal();
        if ($answer != 0 && $time != 0) {
            $answer = $answer / $time;
            return number_format($answer, $this->decimals);
        } else {
           return number_format(0, $this->decimals);
        }
    } 

    public function asRSampleTotal($time) {
        $answer = $this->asRTotal();
        if ($answer != 0 && $time != 0) {
            $answer = $answer / $time;
            return $answer;
        } else {
           return 0;
        }
    } 

    public function reset() {
        $this->last = $this->total;
    }
}

?>
