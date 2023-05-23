<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_Supplier extends Model
{

    protected $table = 'M_Supplier';
    protected $primaryKey = 'SupplierID';
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

}
