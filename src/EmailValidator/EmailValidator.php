<?php
namespace EmailValidator;

class EmailValidator
{
    protected $email;
    protected $currentHost;
    protected $fromEmail;
    protected $mx;

    public function __construct($checkEmail, $currentHost, $fromEmail)
    {
        $this->email = $checkEmail;
        $this->currentHost = $currentHost;
        $this->fromEmail = $fromEmail;
    }

    public function validate()
    {
        if (!$this->isValidName()) {
            return false;
        }

        list(, $host) = explode('@', $this->email);

        $this->getMxHost($host);

        return $this->checkOnServer();
    }

    protected function isValidName()
    {
        return preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,12}/i', $this->email);
    }

    protected function getMxHost($host)
    {
        $ret = getmxrr($host, $mxhosts);
        if ($ret) {
            $this->mx = array_shift($mxhosts);
        } else {
            $this->mx = $host;
        }
    }

    protected function checkOnServer()
    {
        $fp = fsockopen($this->mx, 25, $errno, $errstr, 30);

        if ($fp === false) {
            return false;
        }

        fwrite($fp, "HELO {$this->currentHost}\r\n");
        $response = fread($fp, 1024);
        if (!preg_match('/^2\d\d.*$/', $response)) {
            return false;
        }

        fwrite($fp, "mail from:<{$this->fromEmail}>\r\n");
        $response = fread($fp, 1024);
        if (!preg_match('/^2\d\d.*$/', $response)) {
            return false;
        }

        fwrite($fp, "rcpt to:<{$this->email}>\r\n");
        $response = fread($fp, 1024);
        if (!preg_match('/^2\d\d.*$/', $response)) {
            return false;
        }

        return true;
    }
}
