<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class FirstInit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Schema::create('users', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('name');
        //     $table->string('username');
        //     $table->string('password');
        //     $table->string('email');
        //     $table->text('picture')->nullable();
        //     $table->unsignedBigInteger('role_id')->nullable();
        //     $table->unsignedBigInteger('menu_id')->nullable();
        //     $table->tinyInteger('active')->default(0);
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('user_sessions', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('user_id')->index();
        //     $table->text('token');
        //     $table->string('ip')->nullable();
        //     $table->string('agent')->nullable();
        //     $table->string('platform')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('permissions', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('name');
        //     $table->string('slug')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('role_permissions', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('permission_id')->index();
        //     $table->unsignedBigInteger('role_id')->index();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('roles', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('name');
        //     $table->string('slug')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('menu_items', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('name');
        //     $table->string('slug')->index();
        //     $table->text('icon')->nullable();
        //     $table->text('path')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('menus', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('parent_id')->index()->nullable();
        //     $table->unsignedBigInteger('menu_item_id')->index();
        //     $table->unsignedBigInteger('master_menu_id')->index();
        //     $table->string('overline')->nullable();
        //     $table->integer('ordering')->default(0);
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('master_menus', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('name');
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('user_notifications', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('user_id')->index();
        //     $table->boolean('is_read')->default(false);
        //     $table->string('title')->nullable();
        //     $table->text('description')->nullable();
        //     $table->string('type')->nullable();
        //     $table->string('link_path')->nullable();
        //     $table->json('link_params')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        // DB::statement('ALTER TABLE 
        // user_notifications ADD FULLTEXT 
        // user_notifications_fulltext(title,description)
        // '); 
        // Schema::create('projects', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('code')->nullable();
        //     $table->string('name')->nullable();
        //     $table->dateTime('start_date')->nullable();
        //     $table->dateTime('end_date')->nullable();
        //     $table->dateTime('actual_start_date')->nullable();
        //     $table->dateTime('actual_end_date')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('project_config', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('project_id')->index();
        //     $table->string('name')->index();
        //     $table->string('value')->nullable();
        //     $table->string('description')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('project_members', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('project_id')->index();
        //     $table->unsignedBigInteger('user_id')->index();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('works', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('project_id')->index();
        //     $table->json('related')->nullable();
        //     $table->string('name')->nullable();
        //     $table->text('summary')->nullable();
        //     $table->longtext('description')->nullable();
        //     $table->enum('priority', ['REGULAR', 'LOW', 'HIGH', 'URGENT'])->default('REGULAR');
        //     $table->enum('status', ['OPEN', 'RUNNING', 'COMPLETED'])->default('OPEN');
        //     $table->json('labels')->nullable();
        //     $table->tinyInteger('is_module')->default(0);
        //     $table->tinyInteger('is_done')->default(0);
        //     $table->dateTime('start_date')->nullable();
        //     $table->dateTime('end_date')->nullable();
        //     $table->dateTime('actual_start_date')->nullable();
        //     $table->dateTime('actual_end_date')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('work_labels', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->string('name')->index();
        //     $table->unsignedBigInteger('project_id')->index();
        //     $table->string('color')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('work_assignees', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('work_id')->index();
        //     $table->unsignedBigInteger('user_id')->index();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('sprints', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('project_id')->index();
        //     $table->unsignedBigInteger('work_id')->index()->nullable();
        //     $table->string('name')->index();
        //     $table->longtext('goals')->index()->nullable();
        //     $table->enum('status', ['OPEN', 'STARTED', 'FINISHED'])->default('OPEN');
        //     $table->dateTime('start_date')->nullable();
        //     $table->dateTime('end_date')->nullable();
        //     $table->dateTime('actual_start_date')->nullable();
        //     $table->dateTime('actual_end_date')->nullable();
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        // Schema::create('tasks', function (Blueprint $table) {
        //     $table->bigIncrements('id')->index();
        //     $table->unsignedBigInteger('project_id')->index()->nullable();
        //     $table->unsignedBigInteger('sprint_id')->index()->nullable();
        //     $table->unsignedBigInteger('work_id')->index();
        //     $table->unsignedBigInteger('status')->index()->nullable();
        //     $table->string('name')->index();
        //     $table->longtext('description')->index()->nullable();
        //     $table->dateTime('start_date')->nullable();
        //     $table->dateTime('end_date')->nullable();
        //     $table->dateTime('actual_start_date')->nullable();
        //     $table->dateTime('actual_end_date')->nullable();
        //     $table->json('labels')->nullable();
        //     $table->integer('ordering')->default(1);
        //     $table->double('mandays', 18, 2)->nullable()->default(0);
        //     $table->double('mandays_actual', 18, 2)->nullable()->default(0);
        //     $table->tinyInteger('is_overtime')->default(0);
        //     $table->tinyInteger('is_done')->default(0);
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->unsignedBigInteger('deleted_by')->nullable();

        //     $table->timestamps(0);
        //     $table->softDeletes('deleted_at');
        // });

        
        Schema::create('task_comments', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->unsignedBigInteger('task_id')->index();
            $table->longtext('description');
            $table->unsignedBigInteger('reply')->index()->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps(0);
            $table->softDeletes('deleted_at');
        });

        
        Schema::create('task_components', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->unsignedBigInteger('project_id')->index()->nullable();
            $table->unsignedBigInteger('task_id')->index();
            $table->string('name');
            $table->text('summary')->nullable();
            $table->longtext('description');
            $table->enum('type', ['DOC', 'DOC-API', 'SQL', 'RULE', 'NOTE', 'OTHER'])->default('DOC');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps(0);
            $table->softDeletes('deleted_at');
        });

        
        Schema::create('task_status', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('name')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps(0);
            $table->softDeletes('deleted_at');
        });

        
        Schema::create('task_labels', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('name')->index();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps(0);
            $table->softDeletes('deleted_at');
        });

        
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->unsignedBigInteger('task_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps(0);
            $table->softDeletes('deleted_at');
        });

        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::dropIfExists('users'); 
        Schema::dropIfExists('user_sessions'); 
        Schema::dropIfExists('permissions'); 
        Schema::dropIfExists('role_permissions'); 
        Schema::dropIfExists('roles'); 
        Schema::dropIfExists('menu_items'); 
        Schema::dropIfExists('menus'); 
        Schema::dropIfExists('master_menus'); 
        Schema::dropIfExists('user_notifications'); 
        Schema::dropIfExists('projects'); 
        Schema::dropIfExists('project_config'); 
        Schema::dropIfExists('project_members'); 
        Schema::dropIfExists('works'); 
        Schema::dropIfExists('work_labels'); 
        Schema::dropIfExists('work_assignees'); 
        Schema::dropIfExists('sprints'); 
        Schema::dropIfExists('tasks'); 
        Schema::dropIfExists('task_comments'); 
        Schema::dropIfExists('task_components'); 
        Schema::dropIfExists('task_status'); 
        Schema::dropIfExists('task_labels'); 
        Schema::dropIfExists('task_assignees'); 

    }
}        
        