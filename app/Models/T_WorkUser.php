<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class T_WorkUser extends Model
{
    // use Compoships;
    const CREATED_AT = 'AddDate';
    const UPDATED_AT = 'UpdateDate';
    protected $dateFormat = 'Y/m/d H:i:s';
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';


    protected $table = 'T_WorkUser';
    protected $primaryKey = 'UserID';
    
    public function T_Work()
    {
        return $this->belongsTo('App\Models\T_Work', ['WWID', 'WorkID'], ['WWID', 'WorkID']);
    }
}
