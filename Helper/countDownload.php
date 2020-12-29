<?php

$counter = 'http://localhost:8800/wp-content/uploads/2020/12/counter.txt'; // text file to store download count - create manually and put a 0 (zero) in it to begin the count
$download = 'http://localhost:8800/wp-content/uploads/2020/12/ubaifis.zip'; // the link to your download file

$number = file_get_contents($counter); // read count file
$number++; // increment count by 1
$fh = fopen($counter, 'w'); // open count file for writing
fwrite($fh, $number); // write new count to count file
fclose($fh); // close count file
header("Location: $download"); // get download
echo file_get_contents('http://localhost:8800/wp-content/uploads/2020/12/counter.txt');
echo $number;
/*
    Get the IP Address of a Visitor with PHP
    http://www.beliefmedia.com/get-ip-address
*/
function track_ip() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
               if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
               echo $ip;   
             }
           }
        }
   
     }
   
   }
?>