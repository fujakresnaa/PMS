<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users;
use App\Models\MenuItems;
use App\Models\Menus;
use App\Models\MasterMenus;
use App\Models\Roles;
use App\Models\Permissions;
use App\Models\RolePermission;
use App\Models\RolePermissions;
use App\Models\UserNotifications;
//
use App\Models\Projects;
use App\Models\ProjectConfig;
use App\Models\ProjectMembers;
//
use App\Models\Works;
use App\Models\WorkAssignees;
use App\Models\WorkLabels;
//
use App\Models\Sprints;
//
use App\Models\Tasks;
use App\Models\TaskAssignees;
use App\Models\TaskComments;
use App\Models\TaskComponents;
use App\Models\TaskLabels;
use App\Models\TaskStatus;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->initUser();
        $this->DefaultData();
        $this->sample(); //
    }

    public function DefaultData () {
       

        $modules = [ // id start dari 2
            "Activity",
            "Users",
            "UserSessions",
            "Permissions",
            "RolePermissions",
            "Roles",
            "MenuItems",
            "Menus",
            "MasterMenus",
            "UserNotifications",
            "Projects",
            "Works",
            "WorkLabels",
            "Sprints",
            "Tasks",
            "TaskStatus",
            "TaskLabels",
        ];
        // sort($modules);

        $customs = [
            // 'GeneralConfigurations',
        ];

        // init permission
        $this->initPermission($modules, $customs);

        $masterRole = Roles::create([
            'name' => 'ADMINISTRATOR',
            'slug' => 'administrator',
            'created_by' => 1,
        ]);

        // init menus
        $customMenu = [
            'MasterData',
        ];

        $menuLists = array_merge($modules, $customMenu);
        // sort($menuLists);
        $this->initMenu($menuLists);

        // create first notif
        UserNotifications::create([
            'user_id' => 1,
            'is_read' => 0,
            'title' => 'Welcome!',
            'description' => 'hope you enjoy use this app.',
            'type' => 'info',
            'link_path' => 'view-users',
            'link_params' => ['id' => 1],
            'created_by' => 1,
        ]);

    }

    public function initMenu($modules) {

        // init master menu
        $masterMenu = MasterMenus::create([
            'name' => 'Default',
            'created_by' => 1,
        ]);

        // init first menu : Home
        $menu = MenuItems::create([
            'name' => 'Home',
            'slug' => 'home',
            'path' => '/',
            'icon' => 'home',
            'created_by' => 1,
        ]);

        // injecting first menu to master menu
        Menus::create([
            'parent_id' => null,
            'menu_item_id' => $menu->id,
            'master_menu_id' => $masterMenu->id,
            'ordering' => 1,
            'created_by' => 1,
        ]);

        // generate menu from modules
        $order = 1;
        foreach ($modules as $name) {
            $fixName = H_splitUppercaseWithSpace($name);
            $slug = H_makeSlug($name);
            $path = '/' . $slug;

            // generatin menus
            $menu = MenuItems::create([
                'name' => $fixName,
                'slug' => $slug,
                'path' => $path,
                'icon' => null,
                'created_by' => 1,
                
            ]);
            
            Menus::create([
                'parent_id' => null,
                'menu_item_id' => $menu->id,
                'master_menu_id' => $masterMenu->id,
                'ordering' => $order + 1,
                'created_by' => 1,
            ]);

            $order ++;
        }


    }

    public function initPermission($modules, $custom = []) {

        $masterRole = Roles::create([
            'name' => 'Default',
            'slug' => 'default',
            'created_by' => 1,
        ]);

        $inputPermissions = [];
        foreach ($modules as $name) {
            $fixName = H_splitUppercaseWithSpace($name);
            $slug = H_makeSlug($name);
            $crud = [ 'Browse', 'Create', 'Read', 'Update', 'Delete', 'Restore', ];

            foreach ($crud as $role) {
                $name = $fixName.' '.$role;
                $slugs = strtolower($slug.'-'.$role);
                $inputPermissions[] = [
                    'name' => $name,
                    'slug' => $slugs,
                    'created_by' => 1,
                ];
            }
        }

        foreach ($custom as $name) {
            $fixName = H_splitUppercaseWithSpace($name);
            $slug = H_makeSlug($name);
            $inputPermissions[] = [
                'name' => $fixName,
                'slug' => $slug,
                'created_by' => 1,
            ];
        }
        Permissions::insert($inputPermissions);

        $dataPermissions = Permissions::all();
        $inputRolePermissions = [];
        foreach ($dataPermissions as $r) $inputRolePermissions[] = [
            'permission_id' => $r->id,
            'role_id' => $masterRole->id,
            'created_by' => 1,
        ];
        RolePermissions::insert($inputRolePermissions);
    }

    public function initUser() {

        $data = [
            'boss',
            'ikhbal',
            'alif',
            'rizky', // om qinoy
            'cuytamvan',
            'fuza',
            'tengku',
            'febri',
            'hilmy',
            'reza',
            'fresha',
            'firlian',
            'dimi',
            'dimas',
        ];

        foreach ($data as $key => $value) {
            Users::create([
                'name'  => $value,
                'username'  => $value,
                'email' => $value .'@sopeus.com',
                'menu_id' => 1,
                'role_id' => 1,
                'password'  => 'LxC61B52HvV/ce0ZjUNSHQ==', // P@ssw0rd
                'active' => 1,
                'created_by' => 1,
            ]);
        }
    }

    public function sample() {

        // project
            $project = Projects::create([
                'code' => 'PMP',
                'name' => 'Patria Maritim Perkasa',
                'start_date' => '2021-01-01 00:00:00',
                'end_date' => '2021-10-01 00:00:00',
                'actual_start_date' => '2021-01-01 00:00:00',
                'actual_end_date' => null,
            ]);

            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 1 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 2 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 3 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 4 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 5 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 6 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 7 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 8 ]);
            $projectMember = ProjectMembers::create(['project_id' => $project->id, 'user_id' => 9 ]);

        // work
            $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'PROD', 'color' => '#1e9e3a' ]);
            $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'DEV', 'color' => '#3557bd' ]);
            $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'ANALYS', 'color' => '#ff9900' ]);
            $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'NEED ANALYSIS', 'color' => '#ff9900' ]);
            $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'ADDITIONAL', 'color' => '#ff9900' ]);

            $work = Works::create([
                'project_id' => $project->id,
                'name' => 'List Master Data',
                'summary' => 'List Master Data untuk modul awal',
                'description' => 'List Master Data untuk modul awal',
                'priority' => 'REGULAR',
                'status' => 'OPEN',
                'labels' => [1,2],
                'start_date' => '2021-06-01 00:00:00',
                'end_date' => '2021-06-06 00:00:00',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
            $workAssign = WorkAssignees::create(['work_id' => $work->id, 'user_id' => 1 ]);

            $workChild = Works::create([
                'project_id' => $project->id,
                'related' => [$work->id],
                'name' => 'Master User',
                'summary' => 'List Master User',
                'description' => 'List MasterUser',
                'priority' => 'REGULAR',
                'status' => 'OPEN',
                'labels' => [1,2],
                'is_module' => 1,
                'start_date' => '2021-06-01 00:00:00',
                'end_date' => '2021-06-03 00:00:00',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
            $workAssign = WorkAssignees::create(['work_id' => $workChild->id, 'user_id' => 1 ]);

            $workChild = Works::create([
                'project_id' => $project->id,
                'related' => [$work->id],
                'name' => 'Master Role',
                'summary' => 'List Master Role',
                'description' => 'List Master Role',
                'priority' => 'REGULAR',
                'status' => 'OPEN',
                'labels' => [1,2],
                'is_module' => 1,
                'start_date' => '2021-06-04 00:00:00',
                'end_date' => '2021-06-06 00:00:00',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
            $workAssign = WorkAssignees::create(['work_id' => $workChild->id, 'user_id' => 1 ]);

            $work2 = Works::create([
                'project_id' => $project->id,
                'name' => 'Authenticate Services',
                'summary' => 'Authenticate user',
                'description' => 'Authenticate service user',
                'priority' => 'REGULAR',
                'status' => 'OPEN',
                'labels' => [1],
                'start_date' => '2021-07-01 00:00:00',
                'end_date' => '2021-07-06 00:00:00',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
            $workAssign = WorkAssignees::create(['work_id' => $work2->id, 'user_id' => 2 ]);

        // Sprint
            $sprint = Sprints::create([
                'project_id' => $project->id,
                'work_id' => $work->id,
                'name' => 'SPRINT_1_AUTHENTICATE_SERVICES',
                'goals' => 'login, register, middleware handler for user',
                'status' => 'OPEN',
                'start_date' => '2021-01-05 00:00:00',
                'end_date' => '2021-01-15 00:00:00',
                'actual_start_date' => null,
                'actual_end_date' => null,
            ]);
        
        // Task
        $taskStatusTodo = TaskStatus::create(['project_id' => $project->id, 'name' => 'TODO' ]);
        $taskStatusProgress = TaskStatus::create(['project_id' => $project->id, 'name' => 'PROGRESS' ]);
        $taskStatus = TaskStatus::create(['project_id' => $project->id, 'name' => 'RETURNED' ]);
        $taskStatusReview = TaskStatus::create(['project_id' => $project->id, 'name' => 'REVIEW' ]);
        $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'DONE' ]);
        $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'SIT' ]);
        $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'UAT' ]);
        $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'PREPARE TO LIVE' ]);

        $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'API', 'color' => '#0089ca' ]);
        $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'UI', 'color' => '#ff9900' ]);
        $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'INTEGERATION', 'color' => '#10a160' ]);
        $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'BUGS', 'color' => '#751e9e' ]);
        $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'FINDINGS-SIT', 'color' => '#a3125b' ]);
        $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'FINDINGS-UAT', 'color' => '#8db00e' ]);

            $Task = Tasks::create([
                'project_id' => $project->id,
                'sprint_id' => $sprint->id,
                'work_id' => $work2->id,
                'status' => 1,
                'name' => 'Login Users',
                'description' => 'login user API',
                'start_date' => '2021-07-01 00:00:00',
                'end_date' => '2021-07-03 00:00:00',
                'actual_start_date' => null,
                'actual_end_date' => null,
                'labels' => [1],
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $Task2 = Tasks::create([
                'project_id' => $project->id,
                'sprint_id' => $sprint->id,
                'work_id' => $work2->id,
                'status' => 1,
                'name' => 'Login Users',
                'description' => 'login user UI',
                'start_date' => '2021-07-04 00:00:00',
                'end_date' => '2021-07-06 00:00:00',
                'actual_start_date' => null,
                'actual_end_date' => null,
                'labels' => [1,2],
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $Task3 = Tasks::create([
                'project_id' => $project->id,
                'sprint_id' => null,
                'work_id' => $work->id,
                'status' => 1,
                'name' => 'Master Users',
                'description' => 'user data structure',
                'start_date' => '2021-06-01 00:00:00',
                'end_date' => '2021-06-03 00:00:00',
                'actual_start_date' => null,
                'actual_end_date' => null,
                'labels' => [4],
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $Task4 = Tasks::create([
                'project_id' => $project->id,
                'sprint_id' => null,
                'work_id' => $work->id,
                'status' => 1,
                'name' => 'Users Sessions',
                'description' => 'sessions login user',
                'start_date' => '2021-06-04 00:00:00',
                'end_date' => '2021-06-06 00:00:00',
                'actual_start_date' => null,
                'actual_end_date' => null,
                'labels' => [4],
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $TaskAssignees = TaskAssignees::create(['task_id' => $Task->id, 'user_id' => 1 ]);
            $TaskAssignees = TaskAssignees::create(['task_id' => $Task2->id, 'user_id' => 2 ]);
            $TaskAssignees = TaskAssignees::create(['task_id' => $Task3->id, 'user_id' => 1 ]);
            $TaskAssignees = TaskAssignees::create(['task_id' => $Task3->id, 'user_id' => 2 ]);
            $TaskAssignees = TaskAssignees::create(['task_id' => $Task4->id, 'user_id' => 1 ]);

            $TaskComments = TaskComments::create([
                'task_id' => $Task->id,
                'description' => 'Login nya gimana ini bos ',
                'created_by' => 1,
            ]);

            $TaskComments = TaskComments::create([
                'task_id' => $Task->id,
                'description' => 'siang kita zoom',
                'created_by' => 2,
                'reply' => 1,
            ]);

            $TaskComments = TaskComments::create([
                'task_id' => $Task->id,
                'description' => 'Oke boss',
                'created_by' => 1,
                'reply' => 1,
            ]);

            $TaskComments = TaskComments::create([
                'task_id' => $Task->id,
                'description' => 'Jangan sampe ngaret ini task',
                'created_by' => 2,
            ]);

            $typeComp = ['DOC', 'SQL', 'RULE', 'NOTE', 'OTHER'];

            foreach ($typeComp as $val) {
                $TaskComponents = TaskComponents::create([
                    'project_id' => $project->id,
                    'task_id' => $Task->id,
                    'name' => 'Task Component ' . $val,
                    'summary' => 'summary Task Component ' . $val,
                    'description' => 'description Task Component ' . $val,
                    'type' => $val,
                ]);

            }

            ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-done', 'value' => $taskStatusDone->id ]);
            ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-required', 'value' => $taskStatusTodo->id ]);
            ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-progress', 'value' => $taskStatusProgress->id ]);
            ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-review', 'value' => $taskStatusReview->id ]);

    }
}
