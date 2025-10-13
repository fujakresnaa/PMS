<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

use App\Traits\RelationActionBy;
use Carbon\Carbon;

/**
 * @property bigIncrements $id 
 * @property unsignedBigInteger $sprint_id 
 * @property unsignedBigInteger $work_id 
 * @property unsignedBigInteger $status 
 * @property string $name 
 * @property longtext $description 
 * @property dateTime $start_date 
 * @property dateTime $end_date 
 * @property dateTime $actual_start_date 
 * @property dateTime $actual_end_date 
 * @property json $labels 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class Tasks extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'tasks';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'project_id', 
        'sprint_id', 
        'work_id', 
        'status', 
        'name', 
        'description', 
        'start_date', 
        'end_date', 
        'actual_start_date', 
        'actual_end_date', 
        'labels', 
        'mandays', 
        'mandays_actual', 
        'is_overtime', 
        'is_done', 
        'ordering', 
        'progress', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    protected $casts = [
        'labels' => 'array',
        'mandays' => 'real',
        'mandays_actual' => 'real',
        'ordering' => 'integer',
        'project_id' => 'integer',
        'sprint_id' => 'integer',
        'status' => 'integer',
        'is_overtime' => 'boolean',
        'is_done' => 'boolean',
        'work_id' => 'integer',
        'progress' => 'integer',
    ];

    protected $appends = [
        'task_labels', // ngacu ke fungsi getTaskLabelsAttribute
        'status_name', // ngacu ke fungsi getStatusNameAttribute
        'total_day', // ngacu ke fungsi getTotalDayAttribute
        // 'status_order', // ngacu ke fungsi getStatusNameAttribute
    ];


    public function Columns() {
        return $this->fillable;
    }

    public function Works() {
        return $this->belongsTo(Works::class, 'work_id');
    }

    public function Sprints() {
        return $this->belongsTo(Sprints::class, 'sprint_id');
    }

    public function Projects() {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    public function Assignees() {
        return $this->hasMany(TaskAssignees::class, 'task_id');
    }

    public function Components() {
        return $this->hasMany(TaskComponents::class, 'task_id')->orderBy('name');
    }

    public function Docs() {
        return $this->hasMany(TaskComponents::class, 'task_id')->whereType('DOC')->orderBy('name');
    }

    public function DocsApi() {
        return $this->hasMany(TaskComponents::class, 'task_id')->whereType('DOC-API')->orderBy('name');
    }

    public function Comments() {
        return $this->hasMany(TaskComments::class, 'task_id');
    }

    public function TaskStatus() {
        return $this->belongsTo(TaskStatus::class, 'status');
    }

    public function getTaskLabelsAttribute() {
        return $this->TaskLabels()->get();
    }

    public function TaskLabels() {
        return TaskLabels::whereIn('id', $this->labels ?? []);
    }

    public function getStatusNameAttribute() {
        $data = TaskStatus::whereId($this->status)->first();
        return ($data) ? $data->name : '';
    }

    // public function getStatusOrderAttribute() {
    //     $data = TaskStatus::whereId($this->status)->first();
    //     return ($data) ? $data->ordering : '';
    // }

    public function StatusName() {
        return TaskLabels::whereIn('id', $this->labels ?? []);
    }

    public function TaskAssignees() {
        return $this->hasManyThrough(
            Users::class,
            TaskAssignees::class,
            'task_id', // where
            'id', // (?) = table.col
            'id', // --
            'user_id' // table.col = (?)
        );
    }

    public function getTotalDayAttribute() {
        $res = 0;
        if ($this->start_date != null && $this->end_date != null) {
            $start = H_formatDate($this->start_date, 'Y-m-d');
            $end = H_formatDate($this->end_date, 'Y-m-d');
            $res = Carbon::parse($start)->diffInDays($end) + 1;
        } elseif ($this->start_date) $res = 1;
        return $res;
    }

}        
        