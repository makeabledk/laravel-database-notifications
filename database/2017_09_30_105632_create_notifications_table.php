<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->string('channel');
            $table->string('template');
            $table->morphs('notifiable');
            $table->nullableMorphs('subject');
            $table->text('data')->nullable();
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamp('reserved_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('read_at')->nullable()->index();
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
        Schema::drop('notifications');
    }
}
