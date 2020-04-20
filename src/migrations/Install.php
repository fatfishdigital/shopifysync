<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\migrations;

use fatfish\shopifysync\ShopifySync;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%shopifysync_shopifysyncrecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%shopifysync_shopifysyncrecord}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'FieldLayoutRecord' => $this->integer()->notNull(),
                    'FieldLayoutTab' => $this->integer()->notNull(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%shopifysync_shopifysyncrecord}}',
                'some_field',
                true
            ),
            '{{%shopifysync_shopifysyncrecord}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%shopifysync_shopifysyncrecord}}', 'siteId'),
            '{{%shopifysync_shopifysyncrecord}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%shopifysync_shopifysyncrecord}}');
    }
}
