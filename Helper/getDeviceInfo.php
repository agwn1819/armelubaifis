<?php

  include 'Browser.php';

  function getDevice(){
    $deviceInfo = new Browser();
    $deviceUser = array(
      "browser" => $deviceInfo->getBrowser(),
      "platform" => $deviceInfo->getPlatform(),
      "browser_version" => $deviceInfo->getVersion(),
      "user_agent" => $deviceInfo->getUserAgent(),
      "IP" => $_SERVER['REMOTE_ADDR'],
    );
    return $deviceUser;

  }
 ?>
