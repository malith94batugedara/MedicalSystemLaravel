<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('test', array('as' => 'test', 'uses' => 'TestController@index'));

Route::get('test/statuschange/{id}', array('as' => 'teststatuschange', 'uses' => 'TestController@changeStatus'));

Route::get('test/deletetest/{id}', array('as' => 'testdelete', 'uses' => 'TestController@deleteTest'));

Route::get('test/deleteanalyzer/{id}', array('as' => 'analyzerdelete', 'uses' => 'AnalyzeController@destroy'));

Route::post('addanalyzer',array('as' => 'addanalyzer', 'uses' => 'AnalyzeController@store'));

Route::post('addtestgroup',array('as' => 'addtestgroup', 'uses' => 'TestController@store'));

Route::get('testgroupdata/{id}', array('as' => 'testgroupedit', 'uses' => 'TestController@edit'));

Route::post('updatetestgroup/{id}',array('as' => 'updatetestgroup', 'uses' => 'TestController@update'));

Route::post('updateselectedtestparametes/{ids}',array('as' => 'updateselectedtestparametes', 'uses' => 'TestparameterController@update'));

Route::post('addreferencevalue',array('as' => 'addreferencevalue', 'uses' => 'ReferenceValueController@store'));

Route::get('gettestparameters/{id}',array('as' => 'gettestparameters', 'uses' => 'TestparameterController@index'));

Route::get('edittestparameters/{id}',array('as' => 'edittestparameters', 'uses' => 'TestparameterController@edit'));

Route::get('getvaluesuggestions/{id}',array('as' => 'getvaluesuggestions', 'uses' => 'ValueSuggestionController@index'));

Route::get('getreferencevalues/{id}',array('as' => 'getreferencevalues', 'uses' => 'ReferenceValueController@index'));

Route::post('addvaluesuggestion/{id}',array('as' => 'addvaluesuggestion', 'uses' => 'ValueSuggestionController@store'));

Route::get('deletevaluesuggestion/{id}',array('as' => 'deletevaluesuggestion', 'uses' => 'ValueSuggestionController@destroy'));

Route::post('addreferencerangevalues/{id}',array('as' => 'addreferencerangevalues', 'uses' => 'ReferenceValueController@store'));

Route::get('getmaterialconsumption/{id}',array('as' => 'getmaterialconsumption', 'uses' => 'MaterialConsumptionController@index'));

Route::post('addmaterialconsumptionvalues/{id}',array('as' => 'addmaterialconsumptionvalues', 'uses' => 'MaterialConsumptionController@store'));

Route::get('deleteReferenceValue/{id}',array('as' => 'deleteReferenceValue', 'uses' => 'ReferenceValueController@destroy'));

Route::get('deletematerialconsumption/{id}',array('as' => 'deletematerialconsumption', 'uses' => 'MaterialConsumptionController@destroy'));

Route::post('updatetestparametes/{id}',array('as' => 'updatetestparametes', 'uses' => 'TestparameterController@updateall'));

Route::post('addtestparameter/{id}',array('as' => 'addtestparameter', 'uses' => 'TestparameterController@store'));

Route::post('deactivetestparameter/{id}',array('as' => 'deactivetestparameter', 'uses' => 'TestparameterController@deactive'));

Route::get('enterresults', function () {
    // You can perform any necessary logic here before loading the view
    return View::make('WienterResults');
});


//Enter Test Results~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::post('SearchSampleByDtnSno', 'SampleController@searchSample');
Route::post('UpdateTestResults', 'SampleController@updateSample');

Route::get('lisupdate', 'LISController@updateSample');
Route::get('lisupdate8', 'LISController@updateSample8');
 
Route::post('lisupdategraph', 'LISController@updateSampleGraph');

Route::get('lisupdatemexKX21', 'LISController@updateSampleSysmexKX21');
Route::get('lisupdate12', 'LISController@updateSampleTOSOHAIA360');


Route::post('SearchSampleLIS', 'LISController@searchSample');
Route::post('markblooddrew', 'SampleController@enterBloodDrew');
Route::post('markreportcollected', 'SampleController@enterReportCollected');
Route::post('loadPendingSamples_er', 'SampleController@loadPendings');

Route::post('reportauth', 'SampleController@reportAuthentication');

//Route::get('/lisupdate', function() {
//    return View::make('lisupdate');
//});

Route::get('/printreport/{id}', function($id) {
//    return View::make('Reports.TestingReport')->with('lpsid', $id);

    $arr = explode("&", $id);

    $lid = $_SESSION["lid"];
    return View::make('Reports.TestingReports.tr' . $lid)->with('lpsid', $arr[0])->with('repHead', $arr[1]);
});
