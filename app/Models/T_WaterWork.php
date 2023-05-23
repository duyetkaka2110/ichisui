<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class T_WaterWork extends Model
{
    const CREATED_AT = 'AddDate';
    const UPDATED_AT = 'UpdateDate';
    protected $dateFormat = 'Y/m/d H:i:s';

    protected $table = 'T_WaterWork';
    protected $primaryKey = 'WWID';
    public function T_Work(){
        return $this->hasmany('App\Models\T_Work','WWID');
    }
}
