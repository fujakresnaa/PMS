<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTaskStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_status', function (Blueprint $table) {
            $table->integer('ordering')->nullable()->after('name');
        });
        
        // UPDATE status
        // UPDATE `task_status` SET `ordering` = '1' WHERE `task_status`.`name` = 'TODO'; 
        // UPDATE `task_status` SET `ordering` = '2' WHERE `task_status`.`name` = 'PROGRESS'; 
        // UPDATE `task_status` SET `ordering` = '3' WHERE `task_status`.`name` = 'RETURNED'; 
        // UPDATE `task_status` SET `ordering` = '4' WHERE `task_status`.`name` = 'REVIEW'; 
        // UPDATE `task_status` SET `ordering` = '5' WHERE `task_status`.`name` = 'DONE'; 
        // UPDATE `task_status` SET `ordering` = '6' WHERE `task_status`.`name` = 'SIT'; 
        // UPDATE `task_status` SET `ordering` = '7' WHERE `task_status`.`name` = 'UAT'; 
        // UPDATE `task_status` SET `ordering` = '8' WHERE `task_status`.`name` = 'PREPARE TO LIVE'; 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
