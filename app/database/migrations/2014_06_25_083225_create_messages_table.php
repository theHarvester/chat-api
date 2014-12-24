<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('messages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('id_hash', 255)->nullable();
            $table->string('name', 100);
            $table->integer('handle_id');
            $table->integer('conversation_id');
            $table->text('content');
            $table->timestamp('burn_ts');
            $table->boolean('burn_after_open');
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
        Schema::drop('messages');
	}

}
