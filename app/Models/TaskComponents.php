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
 * @property unsignedBigInteger $task_id 
 * @property string $name 
 * @property text $summary 
 * @property longtext $description 
 * @property enum $type 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class TaskComponents extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'task_components';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'project_id', 
        'task_id', 
        'name', 
        'summary', 
        'description', 
        'type', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    protected $casts = [
        'project_id' => 'integer',
        'task_id' => 'integer',
    ];

    public function Columns() {
        return $this->fillable;
    }

    public function Tasks() {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function Projects() {
        return $this->belongsTo(Projects::class, 'task_id');
    }

    // public function Works() {
    //     return $this->hasOneThrough(
    //         Works::class,
    //         Tasks::class,
    //         'id', // tasks : where
    //         'id', // work : (?) = table.col
    //         'id', // --
    //         'work_id' // tasks: table.col = (?)
    //     );
    // }

}        
        