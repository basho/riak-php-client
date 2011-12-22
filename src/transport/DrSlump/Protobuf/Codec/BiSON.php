<?php

// Codec compatible with BiSON by Ivo Wetzel - http://github.com/BonsaiDen/BiSON.js

namespace DrSlump\Protobuf\Codec;

use DrSlump\Protobuf;

class BiSON extends PhpArray
{
    /** @var \SplFixedArray */
    protected $tokens;

    public function __construct()
    {
        $this->tokens = new \SplFixedArray(65536);
        for ($i=0; $i<65536; $i++) {
            $this->tokens[$i] = $i < 128
                              ? chr($i)
                              : html_entity_decode("&#$i;", ENT_NOQUOTES, 'UTF-8');
        }
    }

    /**
     * @param \DrSlump\Protobuf\Message $message
     * @return array
     */
    public function encode(Protobuf\Message $message)
    {
        $data = $this->encodeMessage($message);
        return $this->_encode($data, true);
    }

    /**
     * @param \DrSlump\Protobuf\Message $message
     * @param array $data
     * @return \DrSlump\Protobuf\Message
     */
    public function decode(Protobuf\Message $message, $data)
    {
        return $this->decodeMessage($message, $data);
    }


    protected function _encode($data, $top)
    {
        $tok = $this->tokens;
        $enc = '';

        if (is_float($data) || is_int($data)) {

            $m = $data | 0;

            // Float
            if ($data !== $m) {
                $r = ($data - $m) * 100;
                if ($r < 0) {
                    $add = ($r+1 | 0) - $r;
                    $r = ($add >= 1.0 && $add <= 1.5) ? $r | 0 : $r-1 | 0;
                } else {
                    $add = $r | 0;
                    $r = $r - $add >= 0.5 ? $r+1 | 0 : $add;
                }

                $add = 0;
                if ($data < 0) {
                    $m = -$m;
                    $r = -$r;
                    $add = 1;
                }

                if ($m < 65536) {
                    if ($m === 0) {
                        $enc .= $tok[13 + $add] . $tok[$r + 128];
                    } else {
                        $enc .= $tok[13 + $add] . $tok[$r] . $tok[$m];
                    }
                } else {
                    $enc .= $tok[15 + $add] . $tok[$m >> 16 & 0xFFFF] .
                            $tok[$m & 0xFFFF] . $tok[$r];
                }

            // Int
            } else {
                $add = 0;
                if ($data <= 0) {
                    $data = -$data;
                    $add = 1;
                } else {
                    $data--;
                }

                if ($data < 116) {
                    $enc .= $tok[17 + $data + $add * 116];
                } else if ($data < 65536) {
                    $enc .= $tok[1 + $add] . $tok[$data];
                } else {
                    $enc .= $tok[3 + $add] . $tok[$data >> 16 & 0xFFFF] .
                            $tok[$data & 0xFFFF];
                }
            }

        // String
        } else if (is_string($data)) {
            // TODO: Do we need to use mb_ for utf8/utf16?
            $l = strlen($data);
            $enc .= $tok[7];
            while ($l >= 65535) {
                $l -= 65535;
                $enc .= $tok[65535];
            }
            $enc .= $tok[$l] + $data;

        // Bool
        } else if (is_bool($data)) {
            $enc .= $data ? $tok[5] : $tok[6];

        // Null
        } else if (is_null($data)) {
            $enc .= $tok[0];

        // Arrays
        } else if (is_array($data)) {

            //  Numeric arrays
            reset($data);
            if (key($data) === 0) {
                $enc .= $tok[8];
                for ($i=0; $l=count($data), $i < $l; $i++) {
                    $enc .= $this->_encode($data[$i], false);
                }
                if (!$top) {
                    $enc .= $tok[9];
                }

            // Associative arrays
            } else {
                $enc .= $tok[10];
                foreach ($data as $k=>$v) {
                    $enc .= $tok[17 + strlen($k)] + $k;
                    $enc .= $this->_encode($v, false);
                }
                if (!$top) {
                    $enc .= $tok[11];
                }
            }

        // Objects
        } else if (is_object($data)) {
            $data = get_object_vars($data);
            $enc .= $this->_encode($data, false);
        }

        return $enc;
    }
}



