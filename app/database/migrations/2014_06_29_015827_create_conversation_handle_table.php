<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConversationHandleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('conversation_handle', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('conversation_id')->unsigned()->index();
			$table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
			$table->integer('handle_id')->unsigned()->index();
			$table->foreign('handle_id')->references('id')->on('handles')->onDelete('cascade');
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
		Schema::drop('conversation_handle');
	}

}
