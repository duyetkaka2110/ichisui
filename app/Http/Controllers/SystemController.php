<?php

namespace App\Http\Controllers;

use App\Models\M_System;

class SystemController extends Controller
{
    public static function getSystemByCD($CD, bool $type = false)
    {
        if ($type) {
            return M_System::select('InternalValue', 'DispText')->where("SystemCD", $CD)->orderBy("seq")->get();
        }
        return M_System::select('InternalValue', 'DispText')->where("SystemCD", $CD)->get();
    }
}
