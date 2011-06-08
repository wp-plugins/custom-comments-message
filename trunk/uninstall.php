<?php
	global $wpdb;

	//delete the option that contains
	//the default message
	delete_option('ccm_title_reply');

	//delete the per-post comment messages
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE 
					meta_key = 'ccm_title_reply'");


?>
