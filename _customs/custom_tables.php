<?php
global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

$table_name = $wpdb->prefix . 'url_shortener';

$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  project_id mediumint(9) NOT NULL,
  token_generated longtext,
  remarks longtext,
  author mediumint(9) NOT NULL,
  type tinytext NOT NULL,
  create_date datetime
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );