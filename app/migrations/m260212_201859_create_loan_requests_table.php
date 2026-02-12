<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%loan_requests}}`.
 */
class m260212_201859_create_loan_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%loan_requests}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull(),
            'term' => $this->integer()->notNull(),
            'status' => $this->string()->notNull()->defaultValue('pending'),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-loan_requests-user_id',
            '{{%loan_requests}}',
            'user_id'
        );

        $this->createIndex(
            'idx-loan_requests-status',
            '{{%loan_requests}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%loan_requests}}');
    }
}
