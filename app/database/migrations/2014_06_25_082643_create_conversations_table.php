<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('conversations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('id_hash', 255)->nullable();
            $table->string('name', 100)->nullable();
            $table->integer('user_id');
            $table->boolean('is_encrypted');
            $table->timestamp('last_active_ts');
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('conversations');
	}

}
