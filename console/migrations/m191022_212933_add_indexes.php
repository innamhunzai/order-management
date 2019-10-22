<?php

use yii\db\Migration;

/**
 * Class m191022_212933_add_indexes
 */
class m191022_212933_add_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // creates index for column `created_at`
        $this->createIndex(
            'idx-order-created_at',
            'order',
            'created_at'
        );

        // creates index for column `updated_at`
        $this->createIndex(
            'idx-order-updated_at',
            'order',
            'updated_at'
        );
        // todo: Apply Index on Product Name in future if required
        // UserName has already unique key
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `created_at`
        $this->dropIndex(
            'idx-order-created_at',
            'order'
        );

        // drops index for column `created_at`
        $this->dropIndex(
            'idx-order-updated_at',
            'order'
        );
    }
}
