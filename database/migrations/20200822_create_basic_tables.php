<?php

namespace WooCommerceTimedVouchers\Migrations;

class CreateBasicTables
{
    protected const TABLE_NAME = 'secret_order_keys';

    /**
     * @var \wpdb
     */
    public $db;
    public $prefix;
    public $charset;

    protected $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->prefix = $this->db->prefix;
        $this->charset = $this->db->get_charset_collate();

        $this->table_name = $this->prefix . static::TABLE_NAME;
    }

    public function up()
    {
        /** @noinspection SqlNoDataSourceInspection */
        $sql = "
CREATE TABLE {$this->table_name} (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  order_id mediumint(9) NOT NULL,
  product_id mediumint(9) NOT NULL,
  secret text NOT NULL,
  valid_until datetime,
  PRIMARY KEY  (id)
) {$this->charset}
        ";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
