<?php

use yii\db\Migration;

/**
 * Class m240930_191224_loan_applications
 */
class m240930_191224_loan_applications extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('loan_applications', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull(),
            'term' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-loan_applications-user_id',
            'loan_applications',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240930_191224_loan_applications cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240930_191224_loan_applications cannot be reverted.\n";

        return false;
    }
    */
}
