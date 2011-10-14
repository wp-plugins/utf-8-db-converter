<?php
/*
Plugin Name: Convert WP Database to UTF-8
Plugin URI: http://yihui.name/en/2009/05/convert-mysql-database-to-utf-8-in-wordpress/
Description: Converts the WordPress database (both tables and columns) to UTF-8 character set.
Version: 1.0.2
Author: Yihui Xie
Author URI: http://yihui.name/
*/

/*
	Copyright 2009 - 2011  Yihui Xie  (email: xie@yihui.name)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Dont access directly
if ( !defined('ABSPATH') )
	die();

function UTF8_DB_Converter_menu_add() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', 'UTF-8 DB Converter', 'UTF-8 DB Converter', 'level_10', 'utf8-db-converter', 'UTF8_DB_Converter_menu');
}

function UTF8_DB_Converter_menu() {
	$success = false;

	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('level_10') )
			die('Only the blog owner has the access capabilities to this resource.');
		if ( preg_match('/Start/', $_POST['submit']) )
			$success = UTF8_DB_Converter_DoIt();
	}


?>

	<div class="wrap">
		<h2>UTF-8 DB Converter</h2>
<?php if ( $success ) { ?>
		<div id="message" class="updated fade"><p><strong>The database has been succesfully converted to UTF-8</strong>. <a href="<?php bloginfo('url'); ?>/">View site &raquo;</a></p></div>
<?php } else { ?>
		<div class="narrow">
			<p style="padding: .2em; background-color: #DD2222; color: #FFFFFF; font-weight: bold; font-size: large; text-align: center;">
				WARNING<br/>
					DATA MAY BE LOST! PLEASE BACK UP YOUR DATABASE FIRST!
			</p>
			<p>
					It is recommended that you close the public access of your WordPress based blog/website now.<br/>
					<br/>
					The next procedure may take some time, so do not close your navigator or your internet connection
					during the execution of this plugin.<br/>
					<br/>
					Press the button below to proceed.
			</p>
			<form action="" method="post" id="utf8-db-converter">
				<p class="submit"><input type="submit" name="submit" value="Start converting &raquo;"/></p>
			</form>
		</div>
<?php } ?>
	</div>
	<?php
}

function UTF8_DB_Converter_DoIt() {
    $db_server      = DB_HOST;
    $db_user      = DB_USER;
    $db_password   = DB_PASSWORD;
    $db_name      = DB_NAME;
	global $table_prefix;

    set_time_limit(0);

    $connection = mysql_connect($db_server, $db_user, $db_password) or die( mysql_error() );
    $db = mysql_select_db($db_name, $connection) or die( mysql_error() );

    $sql = 'SHOW TABLES LIKE "'.$table_prefix.'%"';
    if ( !($result = mysql_query($sql, $connection)) )
    {
       print '<span style="color: red;">SQL Error: <br>' . mysql_error() . "</span>\n";
    }

    // Loop through all tables in this database
    while ( $row = mysql_fetch_row($result) )
    {
       $table = mysql_real_escape_string($row[0]);
       $sql2 = "ALTER TABLE $table DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
       
       if ( !($result2 = mysql_query($sql2, $connection)) )
       {
          print '<span style="color: red;">SQL Error: <br>' . mysql_error() . "</span>\n";
          
          break;
       }
       
       print "$table changed to UTF-8 successfully.<br>\n";

       // Now loop through all the fields within this table
       $sql3 = "SHOW COLUMNS FROM $table";
       if ( !($result3 = mysql_query($sql3, $connection)) )
       {
          print '<span style="color: red;">SQL Error: <br>' . mysql_error() . "</span>\n";
          
          break;
       }

       while ( $row3 = mysql_fetch_row($result3) )
       {
          $field_name = $row3[0];
          $field_type = $row3[1];
          
          // Change text based fields
          $skipped_field_types = array('char', 'text', 'blob', 'enum', 'set');
          
          foreach ( $skipped_field_types as $type )
          {
             if ( strpos($field_type, $type) !== false )
             {
                $sql4 = "ALTER TABLE $table CHANGE `$field_name` `$field_name` $field_type CHARACTER SET utf8 COLLATE utf8_general_ci";
                if ( !($result4 = mysql_query($sql4, $connection)) )
                {
                   print '<span style="color: red;">SQL Error: <br>' . mysql_error() . "</span>\n";
                   
                   break 3;
                }
                print "---- $field_name changed to UTF-8 successfully.<br>\n";
             }
          }
       }
       print "<hr>\n";
    }
    mysql_close($connection);

	return true;
}

add_action('admin_menu', 'UTF8_DB_Converter_menu_add');
?>
