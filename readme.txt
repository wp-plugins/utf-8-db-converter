=== Plugin Name ===
Contributors: yihui
Donate link: http://yihui.name
Tags: utf-8, database, MySQL, converter, charset
Requires at least: 2.0.2
Tested up to: 2.8
Stable tag: trunk

Converts the WordPress database (both tables and columns) to UTF-8 character set.

== Description ==

This plugin can convert your WordPress database (both tables and columns) to UTF-8 character set. It will be especially useful when you move your database from one server to another where the default CHARSET is not UTF-8.

== Installation ==

1. Upload the directory `utf-8-db-converter` to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Then you'll see the sub-menu `UTF-8 DB Converter` under the `Plugins` menu
4. Follow the instructions - Done!

== Screenshots == 

1. Admin interface

== Frequently Asked Questions ==

= What's happening "behind the scene"? =

1. Change tables: `ALTER TABLE $table DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci`
2. Change columns: `ALTER TABLE $table CHANGE $field_name $field_name $field_type CHARACTER SET utf8 COLLATE utf8_general_ci`

= What about the WP version? =

As you've seen, only MySQL and PHP are required. Roughly speaking, it has nothing to do with WordPress, so you can use almost any version of WordPress.

== Changelog == 

2011-10-14 version 1.0.2

* use COLLATE utf8_general_ci instead of utf8_bin

2009-06-20 version 1.0.0 released
