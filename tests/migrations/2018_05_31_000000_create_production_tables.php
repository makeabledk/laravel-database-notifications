<?php

use Illuminate\Database\Migrations\Migration;

require __DIR__.'/../../database/create_makeable_database_notifications_table.php.stub';

class CreateProductionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        (new CreateMakeableDatabaseNotificationsTable())->up();
    }
}
