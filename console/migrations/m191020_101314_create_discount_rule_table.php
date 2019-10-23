<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%discount_rule}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%product}}`
 */
class m191020_101314_create_discount_rule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%discount_rule}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'min_quantity' => $this->integer()->notNull(),
            'percentage' => $this->decimal(10,2)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // creates index for column `product_id`
        $this->createIndex(
            '{{%idx-discount_rule-product_id}}',
            '{{%discount_rule}}',
            'product_id'
        );

        // add foreign key for table `{{%product}}`
        $this->addForeignKey(
            '{{%fk-discount_rule-product_id}}',
            '{{%discount_rule}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%product}}`
        $this->dropForeignKey(
            '{{%fk-discount_rule-product_id}}',
            '{{%discount_rule}}'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            '{{%idx-discount_rule-product_id}}',
            '{{%discount_rule}}'
        );

        $this->dropTable('{{%discount_rule}}');
    }
}
