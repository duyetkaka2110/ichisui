<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_MaterialPrice extends Model
{

    protected $table = 'M_MaterialPrice';
    protected $primaryKey = 'MaterialID';
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

}
