<?php

use yii\db\Migration;

class m151117_174004_create_log_table extends Migration
{
	public function up()
	{
		$options = "ENGINE=InnoDB CHARSET=utf8";
		$this->createTable('log_entry', [
			'id' => $this->primaryKey(),
			'model_id' => $this->integer()->notNull(),
			'model_type' => $this->string()->notNull(),
			'action' =>  $this->string()->notNull(),
			'old_attr' => $this->text(),
			'new_attr' => $this->text(),
			'created_by' =>$this->integer()->notNull(),
			'created_at' =>$this->integer()->notNull(),
		], $options);
	}

	public function down()
	{
		try {
			$this->dropTable('log_entry');
		} catch (\Exception $e) {
			var_dump($e);
			return false;
		}
		return true;
	}


	// Use safeUp/safeDown to run migration code within a transaction
	public function safeUp()
	{
		$this->up();
	}

	public function safeDown()
	{
		$this->down();
	}
}
