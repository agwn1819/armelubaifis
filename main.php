<?php

/*
Plugin Name: User Behavior Analysics if(is)
Plugin URI: https://www.internet-sicherheit.de/
Description: This Plugin combine Machine Learning & User and Entity Behavioral Analytics to detect new threats inside a organization
Author: Armel Wonga & Eduard Dege
Version: 1.0.0
Author URI:
License: GPL2
*/



add_action("init", "start_session");
define("ABS_PATH", dirname(__FILE__));

include (ABS_PATH . "/Helper/getDeviceInfo.php");
include (ABS_PATH . "/Helper/dbOperations.php");
include (ABS_PATH . "/Helper/loginOperations.php");
include (ABS_PATH . "/Helper/cookie.php");
include (ABS_PATH . "/Helper/ip_range.php");
include (ABS_PATH . "/Helper/checkBlacklist.php");

/*function wpb_confirm_leaving_js() {

     wp_enqueue_script( 'Confirm Leaving', plugins_url( 'Helper/javascript/confirm-leaving.js', __FILE__ ), array('jquery'), '1.0.0', true );
}
add_action('wp_enqueue_scripts', 'wpb_confirm_leaving_js');*/

function wp_track_mouse_js(){

    #  wp_register_script("track-mouse", plugins_url( '/Helper/javascript/track-mouse.js', __FILE__ ), array("jquery"));
    #  wp_localize_script("track-mouse", "myAjax", array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    #  wp_enqueue_script("jquery");
    #  wp_enqueue_script("track-mouse");
    #  wp_enqueue_script("track-mouse");
    #  wp_enqueue_script("track-mouse", plugins_url( "Helper/javascript/track-mouse.js", __FILE__) , array('jquery'), '1.0.0', true);
    #  wp_localize_script("track-mouse", "myAjax", array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

}
# add_action('init', 'wp_track_mouse_js');


# add_action("init", "my_script_enqueuer");
add_action("wp_ajax_mouse_action", "mouse_action");
add_action("wp_ajax_nopriv_mouse_action", "mouse_action");
add_action("wp_ajax_tap_action", "tap_action");
add_action("wp_ajax_nopriv_tap_action", "tap_action");

function my_mouse_script_enqueuer(){

  $localize = array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'session_id' => session_id()
  );

  wp_enqueue_script("my_mouse_tracker", plugin_dir_url(__FILE__). "/Helper/javascript/track-mouse.js", array("jquery"));
  wp_localize_script("my_mouse_tracker", "myAjax", $localize);


}

function my_smartphone_script_enqueuer(){
  $localize = array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'session_id' => session_id()
  );

  wp_enqueue_script("my_tap_tracker", plugin_dir_url(__FILE__). "/Helper/javascript/track-tap.js", array("jquery"));
  wp_localize_script("my_tap_tracker", "myAjax", $localize);
}





# add_filter( 'authenticate', 'custom_authenticate_username_password', 30, 3);
function custom_authenticate_username_password( $user, $username, $password )
{
    if ( is_a($user, 'WP_User') ) { return $user; }

    if ( empty($username) || empty($password) )
    {
        $error = new WP_Error();
        $user  = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));

        return $error;
    }
}


#add_action("init", "set_cookie");

add_action('admin_menu', 'test_plugin_setup_menu');
add_action('wp_login_failed', 'checkUser');
add_action('wp_login', "addUserToSessionSuccLogin");
add_action('wp_login', 'saveLastLogin');
add_action('wp_login', 'saveLoginToUserLoginData');
add_action('wp_head', 'onTabClosed');
add_action('clear_auth_cookie', "saveLogoutToUserLoginData");
add_action('wp_logout', "getLogout");
if(wp_is_mobile()){
  add_action( 'wp_enqueue_scripts', 'my_smartphone_script_enqueuer' );
}else{
  add_action( 'wp_enqueue_scripts', 'my_mouse_script_enqueuer' );
}
// create custom plugin settings menu
add_action('admin_menu', 'ubaifis_create_menu');
// hook trace user
add_action("template_redirect", "track_user");
add_action("wp_head", "sendMouseData");
/**function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}*/

/**function test_init(){
        global $wpdb;
        $table = "user_recognition";
        $user_id = get_current_user_id();

        createNewTable($wpdb, $table);
        insertToDB($wpdb, $table, getDevice(), $user_id);
        show_cookie();
}*/



/*function sendMouseData(){
  global $wpdb;
  $buttonArray = array();
//  print_r($_POST["mousedown"]);
  foreach($_POST["mousedown"] as $coor){
    array_push($buttonArray, $coor["button"]);
  }

  insertToUserMovementTable($wpdb, session_id(), serialize($buttonArray));
}*/

function ubaifis_create_menu() {
// define admin page in Back-End
    //create new top-level menu
    add_menu_page('UBAifis Einstellungen', 'UBAifis', 'administrator',
        __FILE__, 'ubaifis_settings_page' , "dashicons-performance" , 65 );

    //https://developer.wordpress.org/resource/dashicons/#search

    //call register settings function
    // add_action( 'admin_init', 'register_ubaifis_settings' );
}
function track_user(){
    global $wpdb;
    $table = "user_recognition";
    $user_id = get_current_user_id();

    createNewTable($wpdb, $table);
    createSessionTable($wpdb);
    insertToDB($wpdb, $table, getDevice(), $user_id);
    show_cookie();
}

function ubaifis_settings_page(){
  global $wpdb;
  $table = "user_recognition";
  $user_id = get_current_user_id();

  createNewTable($wpdb, $table);
  createSessionTable($wpdb);
  insertToDB($wpdb, $table, getDevice(), $user_id);
  show_cookie();
  createSessionDataTable($wpdb);
  createUserLoginDataTable($wpdb);
  createUserMovementTable($wpdb);
  #insertToUserMovementTable($wpdb);
  #$ip = $_SE%RVER['REMOTE_ADDR'];
  #echo $ip;
  #echo ip_info("108.171.134.41", "Country");
  dnsblLookup("216.145.14.142");
?>
<div class="wrap">
    <h1>UBAifis</h1>
</div>
<form method="post" action="options.php">
    <?php settings_fields( 'ubaifis' ); ?>
    <?php do_settings_sections( 'ubaifis' ); ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">Inhalt der Datenbanktabelle</th>
        </tr>

        <?php
      //  echo $_SERVER['HTTP_USER_AGENT'];
        /**function getUserIpAddr(){
            if(!empty($_SERVER['HTTP_CLIENT_IP'])){
                //ip from share internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                //ip pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }else{
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }*/

        //echo 'User Real IP - '.getUserIpAddr();
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_recognition", OBJECT);

        echo "<tr>";
        foreach($results[0] AS $key=>$value){
            echo "<th>$key</th>";
        }
        echo "</tr>";

        foreach($results AS $result){
            echo "<tr>";
            foreach($result AS $key=>$value){
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</form>
<?php
}




function mouse_action(){
  if(isset($_REQUEST)){
    global $wpdb;
    $buttonArray = array();
    $clickPositions = array();
    $coordinateX = array();
    $coordinateY = array();
  //  print_r($_POST["mousedown"]);
    foreach($_REQUEST["mousedown"] as $coor){
      array_push($buttonArray, $coor["button"]);
      array_push($clickPositions, "x:".$coor["coordinate_x"]);
      array_push($clickPositions, "y:".$coor["coordinate_y"]);
    }

    insertToUserMovementTable($wpdb, $_REQUEST["session_id"], "mouse",
    serialize($buttonArray), serialize($clickPositions), serialize($_REQUEST["mousemove"][0]), serialize($_REQUEST["mousemove"][1]), $_SERVER["REQUEST_URI"]);
    die();
}
}

function tap_action(){
  if(isset($_REQUEST)){
    print_r($_REQUEST);
    global $wpdb;
    $buttonArray = array();
    $clickPositions = array();
    $startPosition = array();
    $endPosition = array();
    foreach($_REQUEST["touchstart"] as $coor){
      array_push($buttonArray, $coor["taptype"]);
      array_push($clickPositions, "x:".$coor["coordinate_x"]);
      array_push($clickPositions, "y:".$coor["coordinate_y"]);
    }
    $session_id = "no session id found";
    if($_REQUEST["session_id"]){
      $session_id = $_REQUEST["session_id"];
    }

    if($_REQUEST["touchmove"]){
    foreach($_REQUEST["touchmove"] as $coor){
      array_push($startPosition, $coor["coordinate_x"]);
      array_push($startPosition, $coor["coordinate_y"]);
      array_push($endPosition, $coor["coordinate_x"]);
      array_push($endPosition, $coor["coordinate_y"]);
    }
  }
    insertToUserMovementTable($wpdb, $session_id, "touch",
    serialize($buttonArray), serialize($clickPositions), serialize($startPosition), serialize($endPosition), $_SERVER["REQUEST_URI"]);
    die();
  }
}

?>
