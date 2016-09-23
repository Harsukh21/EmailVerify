<?php

/**
 * Description of EmailValidation
 *
 * @author Harsukh Makwana <harsukh21@gmail.com>
 */
class EmailValidation
{
    public $hellodomain = 'Your Domail Name';
    public $mailfrom    = 'Sender Email ID';
    public $rcptto;
    public $mx;
    public $ip;

    public function __construct()
    {
        $this->ip = '127.0.0.1';
    }

    public function checkEmail($email = null)
    {
        $this->rcptto = $email;
        $array        = explode('@', $this->rcptto);
        $dom          = $array[1];
        if (getmxrr($dom, $mx)) {
            $this->mx = $mx[rand(0, count($mx) - 1)];
            return $this->processRange($this->ip);
        }
        return false;
    }

    private function asyncRead($sock)
    {
        $read_sock   = array($sock);
        $write_sock  = NULL;
        $except_sock = NULL;
        
        if (socket_select($read_sock, $write_sock, $except_sock, 5) != 1) {
            return FALSE;
        }
        $ret = socket_read($sock, 512);
        return $ret;
    }

    private function smtpConnect($mta, $src_ip)
    {
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($sock == FALSE) {
            return array(FALSE, 'unable to open socket');
        }
        if (!socket_bind($sock, $src_ip)) {
            return array(FALSE, 'unable to bind to src ip');
        }

        $timeout = array('sec' => 10, 'usec' => 0);
        socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout);

        socket_set_nonblock($sock);
        
        @socket_connect($sock, $mta, 25);

        $ret = $this->asyncRead($sock);
        if ($ret === FALSE) {
            return array(FALSE, 'inital read timed out');
        }

        if (!preg_match('/^220/', $ret)) { // Not a good connection.
            return array(FALSE, $ret);
        }

        // Now do the EHLO.
        socket_write($sock, "HELO ".$this->hellodomain."\r\n");
        $ret = $this->asyncRead($sock);
        if ($ret === FALSE) {
            return array(FALSE, 'ehlo timed out');
        }

        if (!preg_match('/^250/', $ret)) { // Not a good response.
            return array(FALSE, $ret);
        }

        // Now MAIL FROM.
        socket_write($sock, "MAIL FROM:<".$this->mailfrom.">\r\n");
        $ret = $this->asyncRead($sock);
        if ($ret === FALSE) {
            return array(FALSE, 'from timed out');
        }

        if (!preg_match('/^250/', $ret)) // Not a good response.
                return array(FALSE, $ret);

        // Now RCPT TO.
        socket_write($sock, "RCPT TO:<".$this->rcptto.">\r\n");
        $ret = $this->asyncRead($sock);
        var_dump($ret);exit;
        if ($ret === FALSE) {
            return array(FALSE, 'rcpt to timed out');
        }

        if (!preg_match('/^250/', $ret)) {
            // Not a good response.
            return array(FALSE, $ret);
        }
        // All good.


        socket_close($sock);
        return array(true, $ret);
    }

    private function processRange($ip)
    {
        list($ret, $msg) = $this->smtpConnect($this->mx, $ip);
        $msg = trim($msg);
        return $ret;
    }
}