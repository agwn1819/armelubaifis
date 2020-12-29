<?php

function createNewTable($wpdb, $table) {
	//echo $table;
	$table_name = $wpdb->prefix . "user_recognition";
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9),
		browser text NOT NULL,
		browser_version text NOT NULL,
		IP text NOT NULL,
		user_agent text NOT NULL,
		platform text NOT NULL,
		login_attempt int DEFAULT 0,
		login_date DATETIME,
		logout_date DATETIME,
		duration text NOT NULL,
		loginstatus text NOT NULL,
		subpage text NOT NULL,
		PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

function createSessionDataTable($wpdb){
    $table_name = $wpdb->prefix . "session_logs";
    $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        session_id text NOT NULL,
        ip_address varchar(15) NOT NULL,
        session_date DATETIME,
        countrycode varchar(2) NOT NULL,
        state text NOT NULL,
        user_agent text NOT NULL,
        platform text NOT NULL,
        browser text NOT NULL,
        browser_version text NOT NULL,
        subpage text NOT NULL,
        PRIMARY KEY (id)
    )";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function createUserMovementTable($wpdb){
	$table_name = $wpdb->prefix . "usermovement";
	$sql = "CREATE TABLE $table_name(
		id int NOT NULL AUTO_INCREMENT,
		session_id text NOT NULL,
		type text,
		button text,
		click_positions text,
		start_movement text,
		end_movement text,
		subpage text NOT NULL,
		PRIMARY KEY(id)
	)";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

function createUserLoginDataTable($wpdb){
    $table_name = $wpdb->prefix . "user_login_data";
    $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        session_id text NOT NULL,
        user_id int NOT NULL,
        login_attempt int DEFAULT 0,
        login_attempt_date DATETIME,
        logout_date DATETIME,
        duration int DEFAULT 0,
        PRIMARY KEY (id)
    )";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function createSessionTable($wpdb){
	$table_name = $wpdb->prefix . "session";
	$sql = "CREATE TABLE $table_name (
	    id mediumint(9) NOT NULL AUTO_INCREMENT,
		session_id text NOT NULL,
		user_id mediumint(9) NOT NULL,
		ip_address text NOT NULL,
		login_attempt int DEFAULT 0,
		attempt_date DATETIME,
		countrycode text NOT NULL,
		state text NOT NULL,
	  PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function insertToSessionDataTable($wpdb, $session_id, $countrycode, $state, $device){

    $table_name = $wpdb->prefix . "session_data";



    $wpdb->insert(
        $table_name,
        array(
            'session_id' => $session_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'session_date' => date('Y-m-d H:i:s'),
            'countrycode' => $countrycode,
            'state' => $state,
            'user_agent' => $device["user_agent"],
            'platform' => $device["platform"],
            'browser' => $device["browser"],
            'browser_version' => $device["browser_version"],
            'subpage' => $_SERVER["REQUEST_URI"],

        )
    );

}

function insertToUserMovementTable($wpdb, $session_id, $type, $button, $clickPositions, $coordinateX, $coordinateY, $subpage){

	$table_name = $wpdb->prefix . "usermovement";

	$wpdb->insert(
		$table_name,
		array(
				'session_id' => $session_id,
				'type' => $type,
				'button' => $button,
				'click_positions' => $clickPositions,
				'start_movement' => $coordinateX,
				'end_movement' => $coordinateY,
				'subpage'=> $subpage,
		)
	);

}

function insertToSessionTable($wpdb, $session_id, $countrycode, $state){

$table_name = $wpdb->prefix . "session";

$wpdb->insert(
	$table_name,
	array(
		'session_id' =>  $session_id,
		'user_ID' => get_current_user_id(),
		'ip_address' => $_SERVER['REMOTE_ADDR'],
		'countrycode' => $countrycode,
		'state' => $state,
		'attempt_date' => date('Y-m-d H:i:s'),
	)
);
}

function insertToDB($wpdb, $table, $device, $user_id) {

	$table_name = $wpdb->prefix . $table;
	$push_array = array_merge($device, array("user_id" => $user_id, 'subpage' => $_SERVER["REQUEST_URI"],));

	//print_r($push_array);
	$wpdb->insert(
		$table_name,
		$push_array
	);
}
 ?>
