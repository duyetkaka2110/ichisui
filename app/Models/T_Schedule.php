<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class T_Schedule extends Model
{
    const CREATED_AT = 'AddDate';
    const UPDATED_AT = 'UpdateDate';
    protected $dateFormat = 'Y/m/d H:i:s';

    protected $table = 'T_Schedule';
    protected $primaryKey = 'SchedID';
}
