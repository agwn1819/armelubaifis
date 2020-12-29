<?php

function dnsblLookup($UserIP) {
    //$UserIP = "216.145.14.142";

    $dnsbl_lookup=array(
                        "blocklist.de/lists/ssh.txt",
                        "blocklist.de/lists/apache.txt",
                        "blocklist.de/lists/asterisk.txt",
                        "blocklist.de/lists/bots.txt",
                        "blocklist.de/lists/courierimap.txt",
                        "blocklist.de/lists/courierpop3.txt",
                        "blocklist.de/lists/email.txt",
                        "blocklist.de/lmostists/ftp.txt",
                        "blocklist.de/lists/imap.txt",
                        "blocklist.de/lists/pop3.txt",
                        "blocklist.de/lists/postfix.txt",
                        "blocklist.de/lists/proftpd.txt",
                        "blocklist.de/lists/sip.txt",
                        "ciarmy.com/list/ci-badguys.txt",
                        "sbl.spamhaus.org",
                        "xbl.spamhaus.org",
                        "zen.spamhaus.org"
                        );

    $BadCount = 0;

    if ($UserIP) :
        $reverse_ip = implode(".", array_reverse(explode(".", $UserIP)));
        foreach($dnsbl_lookup as $host)  :
            if (checkdnsrr($reverse_ip.".".$host.".", "A"))  :
                $BadCount++;
                if ($BadCount > 0) :
                    break;
                endif;
            endif;
        endforeach;
    endif;

    if ($BadCount == 0) :
        echo "False";
        return FALSE;
    else :
        echo "True";
        return TRUE;
    endif;
}
 ?>
