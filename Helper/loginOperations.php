<?php

include "/getDeviceInfo.php";
//include $_SERVER['DOCUMENT_ROOT']."/getDeviceInfo.php";
include "/ip_range.php";
include "/dbOperations.php";



  function checkUser($username){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
    foreach($results as $row){
      if($row->user_login == $username){
        pushFailedAttempt($wpdb, $row->ID);
        addUserToSessionFailedLogin($wpdb, $row->ID);
        pushFailedLoginToUserLoginData($wpdb, $row->ID);
      }
    }
  }

  function pushFailedAttempt($wpdb, $id){
    $login_attempt = $wpdb->get_var("SELECT login_attempt FROM {$wpdb->prefix}user_recognition WHERE user_id=$id");
    //$wpdb->update("wp_user_recognition", array("lo"));
    $wpdb->update("{$wpdb->prefix}user_recognition", array("login_attempt"=>(int)$login_attempt + 1), array("user_id"=>$id));
  //  echo gettype((int)$login_attempt);
  //  debug_to_console($login_attempt);
  }

  function pushFailedLoginToUserLoginData($wpdb, $id){
    $session_id = session_id();
    $session_exist = false;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_login_data");

    foreach($results as $session){

        if($session->session_id == $session_id ){
        $session_exist = true;

        }
    }
    if($session_exist){

        $user_id = $wpdb->get_var("SELECT `wp_user_login_data`.`user_id` FROM `wp_user_login_data`
        WHERE `wp_user_login_data`.`session_id` = \"$session_id\"");



        if($id == $user_id){
            $login_attempt = $wpdb->get_var("SELECT `wp_user_login_data`.`login_attempt` FROM `wp_user_login_data`
             WHERE `wp_user_login_data`.`session_id` = \"$session_id\" AND `wp_user_login_data`.`user_id`= \"$id\"");

             $wpdb->update("{$wpdb->prefix}user_login_data", array("login_attempt"=>(int)$login_attempt + 1), array("user_id"=>$id, "session_id" => $session_id));
        }else{
            $push_array = array("session_id" => $session_id,
                  "user_id" => $id,
                  "login_attempt"=> 1,
                  "login_attempt_date" => date('Y-m-d H:i:s'),
                  );
        $wpdb->insert($wpdb->prefix . "user_login_data", $push_array);

        }

    }else{
    # echo "here2";
        $push_array = array("session_id" => $session_id,
                  "user_id" => $id,
                  "login_attempt"=> 1,
                  "login_attempt_date" => date('Y-m-d H:i:s'),
                  );
        $wpdb->insert($wpdb->prefix . "user_login_data", $push_array);
    }
  }

  function saveLastLogin(){
    global $wpdb;
    $push_array = array_merge(getDevice(), array("user_id" => wp_get_current_user()->ID,
        "login_date" => date('Y-m-d H:i:s'), "loginstatus" => true));
    $wpdb->insert("{$wpdb->prefix}user_recognition", $push_array);

  }

  function getLogout($user_id){
    global $wpdb;
    $user_id = wp_get_current_user()->ID;
    # echo $user_id;
    $login_date = $wpdb->get_var("SELECT login_date FROM {$wpdb->prefix}user_recognition WHERE user_id = $user_id AND loginstatus = 1");
    $wpdb->update("{$wpdb->prefix}user_recognition",
        array("duration" => strtotime(date('Y-m-d H:i:s')) - strtotime($login_date),
            "logout_date" => date('Y-m-d H:i:s'), "loginstatus" => 0),
        array("user_id"=>$user_id, "loginstatus"=>1));
    //$wpdb->update("{$wpdb->prefix}user_recognition", array("duration"=> strtotime(date('Y-m-d H:i:s')) - strtotime($login_date), "logout_date" => date("Y-m-d H:i:s"), "loginstatus" => 0), array("user_id"=>$id));
    end_session();
  }

  function saveLogoutToUserLoginData(){
    global $wpdb;
    $user_id = wp_get_current_user()->ID;
    $session_id = session_id();

    $login_date = $wpdb->get_var("SELECT `wp_user_login_data`.`login_attempt_date` FROM `wp_user_login_data`
        WHERE `wp_user_login_data`.`session_id` = \"$session_id\" AND `wp_user_login_data`.`user_id`= \"$user_id\"");
    $session = $wpdb->get_var("SELECT session_id FROM {$wpdb->prefix}user_login_data WHERE user_id = $user_id AND session_id = $session_id");

    $wpdb->update($wpdb->prefix . "user_login_data", array("logout_date" => date('Y-m-d H:i:s'), "duration" => strtotime(date('Y-m-d H:i:s')) - strtotime($login_date)),
    array("session_id" => $session_id, "user_id"=>$user_id));
    end_session();
  }

  function saveLoginToUserLoginData($username){
    global $wpdb;
    $user_id = 0;
    $session_exist = false;
    $session_id = session_id();
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_login_data");
    foreach($results as $session){

        if($session->session_id == $session_id){
        $session_exist = true;

        }
    }
    if($session_exist){

    }else{
    $results = $wpdb->get_results("SELECT user_login, ID FROM {$wpdb->prefix}users");
    foreach($results as $row){
      if($row->user_login == $username){
        $user_id = $row->ID;
      }

    }


    $push_array = array("session_id" => session_id(),
                  "user_id" => $user_id,
                  "login_attempt_date" => date('Y-m-d H:i:s'),
                  );

    $wpdb->insert($wpdb->prefix . "user_login_data", $push_array);
    }
  }

  function onTabClosed(){
    global $wpdb;
    $user_id = wp_get_current_user()->ID;
    echo "<script>
    window.onbeforeunload = function(){
    alert('Hello');
    <?php
        $wpdb->update($wpdb->prefix . 'user_login_data', array('logout_date' => date('Y-m-d H:i:s')), array('user_id'=>$user_id));

    ?>
    }
    <?php echo 'im here'; ?>";

    echo "</script>";
  }

  //Tracking Sessions

  function start_session($id){
    global $wpdb;

      if(!session_id()){
        session_start();
        $session_id = session_id();
        insertToSessionDataTable(
          $wpdb,
          $session_id,
          ip_info($_SERVER['REMOTE_ADDR'],"countrycode"),
          ip_info($_SERVER['REMOTE_ADDR'], "state"),
          getDevice()
          );
      }

  }

  function addUserToSessionFailedLogin($wpdb, $id){
    $session_id = session_id();
    $user_id = wp_get_current_user()->ID;
    $login_attempt = $wpdb->get_var("SELECT MAX(login_attempt) FROM {$wpdb->prefix}session WHERE user_id=$id AND attempt_date=(SELECT Max(attempt_date) FROM {$wpdb->prefix}session WHERE user_id=$id)");
    //$wpdb->update("{$wpdb->prefix}session", array("login_attempt"=>(int)$login_attempt + 1, "user_id" => $id), array("session_id"=>$session_id));
    $wpdb->insert("{$wpdb->prefix}session", array("session_id" => $session_id, "user_id" =>$id,
     "login_attempt"=>(int)$login_attempt + 1, "attempt_date" => date('Y-m-d H:i:s'),
        "ip_address"=>$_SERVER['REMOTE_ADDR'],
        "countrycode" => ip_info($_SERVER['REMOTE_ADDR'],"countrycode"),
        "state"=>ip_info($_SERVER['REMOTE_ADDR'], "state")
      ));
  }

  function addUserToSessionSuccLogin($username){
    global $wpdb;
    $user_id = 0;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
    foreach($results as $row){
      if($row->user_login == $username){
        $user_id = $row->ID;
      }
    }
    $session_id = session_id();
    $push_array = array("user_id" => $user_id, "session_id" => $session_id, "login_attempt" => 0,
        "attempt_date" => date('Y-m-d H:i:s'),"ip_address"=>"192.168.0.175",
        "countrycode" => ip_info($_SERVER['REMOTE_ADDR'],"countrycode"),
        "state"=>ip_info($_SERVER['REMOTE_ADDR'], "state")
      );
    $wpdb->insert("{$wpdb->prefix}session", $push_array);
  }

  function end_session(){
    session_regenerate_id(true);
    session_destroy();

  }

  function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
 ?>
