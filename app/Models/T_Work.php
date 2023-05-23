<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships; //https://github.com/topclaudy/compoships

class T_Work extends Model
{
    // use Compoships;
    const CREATED_AT = 'AddDate';
    const UPDATED_AT = 'UpdateDate';
    protected $dateFormat = 'Y/m/d H:i:s';

    protected $table = 'T_Work';
    protected $primaryKey = 'WorkID';
    // work 1-n workuser
    public function T_WorkUser()
    {
        return $this->hasMany('App\Models\T_WorkUser', ['WWID', 'WorkID'], ['WWID', 'WorkID']);
    }
}
