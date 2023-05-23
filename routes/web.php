<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\testController;
use App\Http\Controllers\KinkyuController;
use App\Http\Controllers\KintaiController;
use App\Http\Controllers\MatterController;
use App\Http\Controllers\MatterExportController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderListController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UseMaterialController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockCardReadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// ログイン画面 */
Route::get('/', [UsersController::class, "login"])->name("login");
Route::get('/login', [UsersController::class, "login"]);
Route::post('/login', [UsersController::class, "login"]);
Route::get('/logout', [UsersController::class, "logout"]);
Route::get('/loginphone', [UsersController::class, "loginPhone"]);
Route::post('/loginphone', [UsersController::class, "loginPhone"]);
Route::get('/getMsg',  [OrderController::class, "getMsgJson"]);
Route::post('/getMsg',  [OrderController::class, "getMsgJson"]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/order', [OrderController::class, "index"])->name("r.order");
    Route::post('/deleteOrderMaterial',  [OrderController::class, "deleteOrderMaterial"]);
    Route::get('/updateOrderMaterial',  [OrderController::class, "updateOrderMaterial"]); //bo
    Route::post('/updateOrderMaterial',  [OrderController::class, "updateOrderMaterial"]);
    Route::post('/addbulk',  [OrderController::class, "addbulk"]);
    Route::get('/addbulk',  [OrderController::class, "addbulk"]); //bo
    Route::post('/addbulkOk',  [OrderController::class, "addbulkOk"]);
    Route::get('/addbulkOk',  [OrderController::class, "addbulkOk"]); //
    Route::post('/orderexport',  [OrderController::class, "export"]);
    Route::get('/orderexport',  [OrderController::class, "export"]); //bo
    Route::post('/checkexport',  [OrderController::class, "checkexport"]);
    Route::get('/checkexport',  [OrderController::class, "checkexport"]); //bo
    Route::post('/insertupdateOrder',  [OrderController::class, "insertupdateOrder"]);
    Route::get('/insertupdateOrder',  [OrderController::class, "insertupdateOrder"]); //bo
    Route::post('/updateOrderByID',  [OrderController::class, "updateOrderByID"]);
    Route::get('/updateOrderByID',  [OrderController::class, "updateOrderByID"]); //bo
    Route::post('/exportValidation',  [OrderController::class, "exportValidation"]);
    Route::get('/exportValidation',  [OrderController::class, "exportValidation"]); //bo
    



    Route::post('/getListMater',  [MaterialController::class, "getList"]);
    Route::get('/getListMater',  [MaterialController::class, "getList"]); //bo
    Route::get('/work',  [WorkController::class, "index"])->name("b.work");
    Route::post('/work',  [WorkController::class, "index"])->name("b.work");
    Route::get('/workExportUchiwake',  [WorkController::class, "workExportUchiwake"])->name("b.workexportuchiwake");
    Route::post('/workExportUchiwake',  [WorkController::class, "workExportUchiwake"])->name("b.workexportuchiwake");

    // 発注一覧
    Route::get('/orderlist', [OrderListController::class, "index"])->name("r.orderlist");
    Route::post('/orderlist', [OrderListController::class, "index"])->name("r.orderlist");
    Route::post('/getCheckedHistory', [OrderListController::class, "getCheckedHistory"])->name("r.getCheckedHistory");
    Route::post('/KenpinDetail', [OrderListController::class, "KenpinDetail"])->name("r.KenpinDetail");
    Route::post('/setCheckDetail', [OrderListController::class, "setCheckDetail"])->name("r.setCheckDetail");
    Route::get('/setCheckDetail', [OrderListController::class, "setCheckDetail"])->name("r.setCheckDetail");


    // スケジュール
    Route::get('/schedule',  [ScheduleController::class, "index"])->name("b.schedule.week");
    Route::get('/scheduleday',  [ScheduleController::class, "day"])->name("b.schedule.day");
    Route::get('/getdata',  [ScheduleController::class, "getdata"])->name("b.getdata");
    Route::post('/getdata',  [ScheduleController::class, "getdata"])->name("b.getdata");


    //StockController Start
    Route::get('/stock', [StockController::class, "index"])->name("r.stock");
    Route::post('/stock', [StockController::class, "index"]);
    Route::get('getListStock', [StockController::class, "renderListStock"]); //bo
    Route::post('getListStock', [StockController::class, "renderListStock"]);
    Route::post('createNewStock', [StockController::class, "createNewStock"]);
    Route::get('getStockDetail', [StockController::class, "getStockDetail"]); //bo
    Route::post('getStockDetail', [StockController::class, "getStockDetail"]);
    Route::get('setStockDetailPopup', [StockController::class, "setStockDetail"]); //bo
    Route::post('setStockDetailPopup', [StockController::class, "setStockDetail"]);
    Route::get('updateInvenConfirm', [StockController::class, "updateInvenConfirm"]); //bo
    Route::post('updateInvenConfirm', [StockController::class, "updateInvenConfirm"]);
    Route::get('updateInvenCorrec', [StockController::class, "updateInvenCorrec"]); //bo
    Route::post('updateInvenCorrec', [StockController::class, "updateInvenCorrec"]);
    Route::get('stock/export', [StockController::class, "export"]);
    Route::post('stock/export', [StockController::class, "export"]); //bo
    Route::get('importStock', [StockController::class, "importStock"]); //bo
    Route::post('importStock', [StockController::class, "importStock"]);
    //StockController End

    //StockCardReadController Start
    Route::get('readcard', [StockCardReadController::class, "index"])->name("phone.readcard");
    Route::post('readcard', [StockCardReadController::class, "index"])->name("phone.readcard");;
    Route::get('stockregister', [StockCardReadController::class, "register"])->name("phone.stockregister");
    Route::post('stockregister', [StockCardReadController::class, "register"])->name("phone.stockregister");
    Route::get('setStockDetail', [StockCardReadController::class, "setStockDetail"])->name("phone.setStockDetail"); //bo
    Route::post('setStockDetail', [StockCardReadController::class, "setStockDetail"])->name("phone.setStockDetail");
    Route::get('getStockByBarcode', [StockCardReadController::class, "getStockByBarcode"])->name("phone.getStockByBarcode"); //bo
    Route::post('getStockByBarcode', [StockCardReadController::class, "getStockByBarcode"])->name("phone.getStockByBarcode");


    //勤怠編集
    Route::get('/kintaiedit', [KintaiController::class, "Kintaiedit"])->name("kintaiedit");
    Route::get('/kintaiinsert', [KintaiController::class, "Kintaiinsert"]);
    Route::get('/sample', 'kintaiController@getValidates');
    Route::post('/kintaiinsert', [KintaiController::class, "Kintaiinsert"]);
    Route::post('/kintaidelete', [KintaiController::class, "Kintaidelete"]);

    //案件入力
    Route::get('/matterinput', [MatterController::class, "Matterinput"])->name("matterinput");
    Route::get('/matterinsert', [MatterController::class, "Matterinsert"]);
    Route::post('/matterinsert', [MatterController::class, "Matterinsert"]);
    Route::post('/deleteMater', [MatterController::class, "deleteMater"]);
    Route::post('/deleteWorkImg', [MatterController::class, "deleteWorkImg"]);

    // 案件入力Excel
    Route::get('/ExportUketsuke', [MatterExportController::class, "ExportUketsuke"])->name("uketsuke");
    Route::get('/ExportUchiWake', [MatterExportController::class, "ExportUchiWake"])->name("uchiwake");
    Route::get('/ExportMitsuMore', [MatterExportController::class, "ExportMitsuMore"])->name("mitsumore");
    Route::get('/ExportSeikyu', [MatterExportController::class, "ExportSeikyu"])->name("seikyu");
    Route::get('/ExportNohin', [MatterExportController::class, "ExportNohin"])->name("nohin");
    Route::get('/ExportRyoshu', [MatterExportController::class, "ExportRyoshu"])->name("ryoshu");
    Route::get('/import', [MatterExportController::class, "index"])->name("index");


    //使用資材詳細入力
    Route::get('/usematerial', [UseMaterialController::class, "Usematerialsearch"])->name("usematerial");
    Route::get('/usematerialsearch', [UseMaterialController::class, "Usematerialsearch"]);
    Route::get('/usematerialinsert', [UseMaterialController::class, "Usematerialinsert"]);
    Route::post('/usematerialinsert', [UseMaterialController::class, "Usematerialinsert"]);
    Route::get('/getListMaterial', [UseMaterialController::class, "getListMaterial"]);
    Route::post('/getListMaterial', [UseMaterialController::class, "getListMaterial"]);

    Route::get('/test', [TestController::class, "Test"]);
    Route::get('/test2', [TestController::class, "Test2"]);

    Route::get('/edit', [KinkyuController::class, "edit"]);

    Route::get('/kintaiedit', [KintaiController::class, "Kintaiedit"])->name("kintaiedit");
    Route::get('/kintaiinsert', [KintaiController::class, "Kintaiinsert"]);
    Route::post('/kintaiinsert', [KintaiController::class, "Kintaiinsert"]);
    Route::post('/kintaidelete', [KintaiController::class, "Kintaidelete"]);
});
