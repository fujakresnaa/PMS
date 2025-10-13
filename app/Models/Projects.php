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


/**
 * @property bigIncrements $id 
 * @property string $code 
 * @property string $name 
 * @property dateTime $start_date 
 * @property dateTime $end_date 
 * @property dateTime $actual_start_date 
 * @property dateTime $actual_end_date 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class Projects extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'projects';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'code', 
        'name', 
        'start_date', 
        'end_date', 
        'actual_start_date', 
        'actual_end_date', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    public function Columns() {
        return $this->fillable;
    }

    public function Works() {
        return $this->hasMany(Works::class, 'project_id');
    }

    public function Modules() {
        return $this->hasMany(Works::class, 'project_id')->whereIsModule(true)->orderBy('name');
    }

    public function Members() {
        return $this->hasMany(ProjectMembers::class, 'project_id');
    }

    public function TaskStatus() {
        return $this->hasMany(TaskStatus::class, 'project_id');
    }

    public function Projects() {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    public function TaskLabels() {
        return $this->hasMany(TaskLabels::class, 'project_id');
    }

    public function WorkLabels() {
        return $this->hasMany(WorkLabels::class, 'project_id');
    }

    public function ProjectMembers() {
        return $this->hasManyThrough(
            Users::class,
            ProjectMembers::class,
            'project_id', // where
            'id', // (?) = table.col
            'id', // --
            'user_id' // table.col = (?)
        );
    }

}        
        