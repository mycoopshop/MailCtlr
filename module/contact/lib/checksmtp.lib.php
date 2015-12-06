<?php

    if (!function_exists('checkdnsrr')) {
        function checkdnsrr($host, $type = '')
        {
            if (!empty($host)) {
                $type = (empty($type)) ? 'MX' :  $type;
                exec('nslookup -type='.$type.' '.escapeshellcmd($host), $result);
                $it = new ArrayIterator($result);
                foreach (new RegexIterator($it, '~^'.$host.'~', RegexIterator::GET_MATCH) as $result) {
                    if ($result) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    // array_combine function support for older version
    if (!function_exists('array_combine')) {
        function array_combine($arr1, $arr2)
        {
            $out = [];

            $arr1 = array_values($arr1);
            $arr2 = array_values($arr2);

            foreach ($arr1 as $key1 => $value1) {
                $out[(string) $value1] = $arr2[$key1];
            }

            return $out;
        }
    }

    class EmailVerify
    {
        public $email = null;
        public $domain = null;
        public $ip = null;
        public $mxRecords = [];
        public $fsock = false;
        public $port = 25;    //defualt port
        public $con_timeout = 10;
        public $data_timeout = 5;
        public $local_user = 'localuser';
        public $local_host = 'localhost';
        public $email_regex = "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/";
        public $debug_on = false;
        public $debug_txt = [];

        /**
         *	validate the email pattern.
         *
         * @return bool
         */
        public function validatedEmail($email = null)
        {
            if (!empty($email)) {
                $email = $this->email;
            }

            if (preg_match($this->email_regex, $this->email)) {
                $this->debug_txt[] = 'Email address pattern OK';

                return true;
            } else {
                $this->debug_txt[] = 'Invalid email address pattern.';

                return false;
            }
        }

        /**
         * Verify the host of email address.
         *
         * @return bool
         */
        protected function verifyDNS()
        {
            list($this->user, $this->domain) = explode('@', $this->email);
            if (checkdnsrr($this->domain, 'MX')) {
                $this->debug_txt[] = 'DNS record is valid';

                return true;
            } else {
                $this->debug_txt[] = 'Invalid DNS records';

                return false;
            }
        }

        /**
         * find and return mx records.
         *
         * @return array
         */
        protected function verifyMX()
        {
            $result = getmxrr($this->domain, $hosts, $weights);

            if ($result) {
                $unique_weights = array_unique($weights);
                if (count($unique_weights) == 1 && count($hosts) > 1) {
                    $weights = range(1, count($hosts));
                }

                $this->mxRecords = array_combine($weights, $hosts);
                ksort($this->mxRecords);
                $this->debug_txt[] = 'Found following MX Records';
                $this->debug_txt[] = PHP_EOL;
                $this->debug_txt[] = implode("\n", $this->mxRecords);
                $this->debug_txt[] = PHP_EOL;
            }

            return $result;
        }

        /**
         * Create a socket connection.
         *
         * @return resource
         */
        protected function createSocket()
        {
            foreach ($this->mxRecords as $mxhost) {
                if ($this->fsock = @fsockopen($mxhost, 25, $errno, $error, $this->con_timeout)) {
                    stream_set_blocking($this->fsock, 1);
                    break;
                }
            }

            if ($this->fsock) {
                $this->debug_txt[] = 'Socket created';

                return $this->fsock;
            } else {
                $this->debug_txt[] = 'Socket creation failed. Error no# '.$errno.', Error: '.$error;

                return false;
            }
        }

        /*
        *	Send data using socket conneection
        */

        protected function send($msg)
        {
            if (empty($msg)) {
                return false;
            }

            $this->debug_txt[] = $msg;

            if (!fwrite($this->fsock, $msg."\n")) {
                $this->debug_txt[] = 'Failed to send data ';

                return false;
            }

            $response = null;

            while (1) {
                $buffer = fread($this->fsock, 1028);
                $response .= $buffer;
                if (empty($buffer)) {
                    break;
                }
            }

            //var_dump($response);

            if (!empty($response)) {
                $this->debug_txt[] = 'Received response &raquo; '.$response;
            }

            return $response;
        }

        /**
         *	Send helo message and check mailbox.
         *
         * @return bool
         */
        protected function ping()
        {
            if ($this->fsock) {
                stream_set_timeout($this->fsock, $this->data_timeout);

                if (!$this->send('HELO '.$this->local_host)) {
                    return;
                }
                if (!$this->send('MAIL FROM: <'.$this->local_user.'@'.$this->local_host.'>')) {
                    return;
                }

                $response = $this->send('RCPT TO: <'.$this->user.'@'.$this->domain.'>');

                // Get response code
                list($code, $msg) = @explode(' ', $response);

                $this->user = null;
                $this->domain = null;

                if ($code == '250') {
                    return true;
                } else {
                    return false;
                }
            } else {
                return;
            }
        }

        /**
         * Close created socket connection.
         * 
         * @return void
         */
        protected function closeSocket()
        {
            if ($this->fsock) {
                fclose($this->fsock);
                $this->fsock = false;
            }
        }

        /**
         *	Verify email address if this is exists or not.
         *
         * @return boolan
         */
        public function verify($email)
        {
            $this->email = $email;
            if (!$this->validatedEmail()) {
                return 0;
            }

            if (!$this->verifyDNS()) {
                return 0;
            }

            if (!$this->verifyMX()) {
                return 0;
            }

            //create socket to mail host
            if (!$this->createSocket()) {
                return 0;
            }

            $finalresponse = $this->ping();

            $this->closeSocket();

            return $finalresponse;
        }

        /*
        *	Show debug message
        */

        public function debug()
        {
            if ($this->debug_on == true) {
                echo '<pre style="background:gray; padding: 3px;">Debugging Stats<br />';
                echo implode("\r\n", $this->debug_txt);
                echo '</pre>';
            }
        }

        //Class destructor

        public function __destruct()
        {
            $this->debug();
        }
    }
