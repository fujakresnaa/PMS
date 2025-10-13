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
 * @property unsignedBigInteger $work_id 
 * @property string $name 
 * @property longtext $goals 
 * @property enum $status 
 * @property dateTime $start_date 
 * @property dateTime $end_date 
 * @property dateTime $actual_start_date 
 * @property dateTime $actual_end_date 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class Sprints extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'sprints';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'project_id', 
        'work_id', 
        'name', 
        'goals', 
        'status', 
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

    protected $casts = [
        'project_id' => 'integer',
        'work_id' => 'integer',
    ];

    public function Columns() {
        return $this->fillable;
    }

    public function Works() {
        return $this->belongsTo(Works::class, 'work_id', 'id');
    }

    public function Projects() {
        return $this->belongsTo(Projects::class, 'project_id', 'id');
    }

}        
        