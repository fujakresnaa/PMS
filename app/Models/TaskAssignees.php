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
 * @property unsignedBigInteger $user_id 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class TaskAssignees extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'task_assignees';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'task_id', 
        'user_id', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    protected $casts = [
        'user_id' => 'integer',
        'task_id' => 'integer',
    ];

    protected $appends = [
        'name', // ngacu ke fungsi getNameAttribute
        'picture', // ngacu ke fungsi getPictureAttribute
    ];

    public function Columns() {
        return $this->fillable;
    }

    public function Users() {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    public function Tasks() {
        return $this->belongsTo(Tasks::class, 'task_id', 'id');
    }

    public function getNameAttribute() {
        $data = $this->Name()->first();
        return ($data) ? $data->name : '';
    }

    public function Name() {
        return Users::where('id', $this->user_id);
    }

    public function getPictureAttribute() {
        $data = $this->Picture()->first();
        return ($data) ? $data->picture : '';
    }

    public function Picture() {
        return Users::where('id', $this->user_id);
    }

}        
        