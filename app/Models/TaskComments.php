<?php

namespace App\Models;

use Carbon\Carbon;
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
 * @property longtext $description 
 * @property unsignedBigInteger $created_by 
 * @property unsignedBigInteger $updated_by 
 * @property unsignedBigInteger $deleted_by 

 */
class TaskComments extends Model
{
    use Authenticatable, Authorizable, HasFactory;
    use SoftDeletes, RelationActionBy; 

    /**
     * Table Configuration
     * @var string
     */
    protected $table = 'task_comments';
    protected $primaryKey = 'id';

    /**
     * List of allowed column to insert / update 
     * @var array
     */
    protected $fillable = [
        'task_id', 
        'reply',
        'description', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // disabled timestamps data
    public $timestamps = true;

    // disable update col id
    protected $guarded = ['id'];

    protected $casts = [
        'task_id' => 'integer',
    ];

    protected $appends = [
        'timestamps', // ngacu ke fungsi getWorkLabelsAttribute
        'created_by_user' // ngacu ke fungsi getWorkLabelsAttribute
    ];

    public function Columns() {
        return $this->fillable;
    }

    public function Replies() {
        return $this->hasMany(TaskComments::class, 'reply');
    }

    public function Users() {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function Tasks() {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function getTimestampsAttribute() {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function getCreatedByUserAttribute() {
        return Users::whereId($this->created_by)->first();
    }


}        
        