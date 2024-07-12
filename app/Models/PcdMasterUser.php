<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcdMasterUser extends Model
{
    use HasFactory;

    protected $table = 'pcd_master_users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'bigint';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'npk',
        'username',
        'password',
        'rfid_tag',
        'mapping',
        'department_id',
        'usergroup',
        'op_skill',
        'picture',
        'last_login',
        'status',
        'updated_by',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'status' => 'boolean',
        'department_id' => 'integer',
        'op_skill' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get the department associated with the user.
     */

}
