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
 * @property unsignedBigInteger $project_id 
 * @property string $name 
 * @property text $summary 
 * @property longtext $description 
 * @property enum $priority 
 * @property enum $status 
 * @property json $labels 
 * @property boolean $is_module 
 * @property boolean $is_done 
 * @property dateTime $start_date 
 * @property dateTime $end_date 
 * @property dateTime $actual_start_date 
 * @property dateTime $actual_end_date 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class Works extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'works';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'project_id', 
        'related', 
        'name', 
        'summary', 
        'description', 
        'priority', 
        'status', 
        'labels', 
        'is_module', 
        'is_done', 
        'created_by', 
        'updated_by', 
        'deleted_by',
        'start_date', 
        'end_date', 
        'progress', 
        'actual_start_date', 
        'actual_end_date',
        'mockup_link',
        'flow_link',
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    protected $casts = [
        'project_id' => 'integer',
        'related' => 'array',
        'labels' => 'array',
        'is_module' => 'boolean',
        'is_done' => 'boolean',
        'progress' => 'integer',
    ];

    protected $appends = [
        'work_labels', // ngacu ke fungsi getWorkLabelsAttribute
        'periode_start', // ngacu ke fungsi getPeriodeStartAttribute
        'total_mandays', // ngacu ke fungsi getPeriodeStartAttribute
        'total_day', // ngacu ke fungsi getTotalDayAttribute
    ];

    public function Columns() {
        return $this->fillable;
    }

    public function Project() {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    public function Assignees() {
        return $this->hasMany(WorkAssignees::class, 'work_id');
    }

    public function getWorkLabelsAttribute() {
        return $this->WorkLabels()->get();
    }

    public function WorkLabels() {
        return WorkLabels::whereIn('id', $this->labels ?? []);
    }

    public function Tasks() {
        return $this->hasMany(Tasks::class, 'work_id', 'id')->whereNotNull('start_date')->orderBy('start_date');
    }

    public function TaskReport() {
        return $this->hasMany(Tasks::class, 'work_id', 'id');
    }

    public function getPeriodeStartAttribute() {
        return ($this->start_date) ? H_formatDate($this->start_date, 'Y-m-01') : null;
    }

    public function getTotalMandaysAttribute() {
        $task = $this->Tasks;
        $total = 0;
        if (count($task) > 0) {
            $total = H_sumArrayBy($task, 'mandays');
        } 
        return $total;
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

    // 

}        
        