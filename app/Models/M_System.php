<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_System extends Model
{

    protected $table = 'M_System';
    protected $primaryKey = 'SystemID';
    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

    // デフォルト
    public static $OrderStatusCD = "000002";
    public static $UseUnitCD = "000006";
    public static $OrderUnitCD = "000008";
    public static $MaterialClsCD = "000025"; //old 13
    public static $WWTypeCD = "000015";
    public static $WWReceptCD = "000016";
    public static $WWHandlerCD = "000017";
    public static $ClaimTypeCD = "000018";
    public static $PaymentStatusCD = "000019";
    public static $WorkStatusCD = "000024";
    public static $WorkTypeCD = "000021";
    public static $TargetTypeCD = "000022";
    public static $WorkPlaceCD = "000023";
    public static $UseMaterialClsCD = "000025";
    public static $TaxCD = "9999999";
}
