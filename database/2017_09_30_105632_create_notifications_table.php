<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type')->unsigned();
            $table->integer('notifiable_id')->unsigned();
//            $table->string('receiver')->nullable();
//            $table->string('subject')->nullable();
//            $table->text('body');
//            $table->string('action_text')->nullable();
//            $table->text('action_url')->nullable();
            $table->text('data')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id', 'available_at']);


//                $table->uuid('id')->primary();
//                $table->string('type');
//                $table->morphs('notifiable');
//                $table->text('data');
//                $table->timestamp('read_at')->nullable();
//                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notifications');
    }
}
