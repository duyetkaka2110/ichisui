<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class T_Stock extends Model
{
    const CREATED_AT = 'AddDate';
    const UPDATED_AT = 'UpdateDate';
    protected $dateFormat = 'Y/m/d H:i:s';
    protected $table = 'T_Stock';
    protected $primaryKey = 'StockID';

}
