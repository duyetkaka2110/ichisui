<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class W_OrderMaterialAsOmat extends Model
{
    const CREATED_AT = 'AddDate';
    const UPDATED_AT = 'UpdateDate';
    protected $table = 'W_OrderMaterial as omat';
    protected $primaryKey = 'UseMaterialID';
    
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y/m/d H:i:s';
}
