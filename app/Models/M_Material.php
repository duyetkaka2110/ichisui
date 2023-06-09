<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_Material extends Model
{

    protected $table = 'M_Material as mmat';
    protected $primaryKey = 'MaterialID';
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

}
