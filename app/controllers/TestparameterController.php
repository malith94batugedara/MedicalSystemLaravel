<?php

class TestparameterController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$data2 = \LabHasTest::where('Testgroup_tgid',$id)->get();

		if (!$data2->isEmpty()) {
			
		$results = \DB::table('Lab_has_test as a')
		->select('a.measurement','a.reportname','a.orderno','a.lhtid','b.tid','b.name', 'c.refference_min','c.refference_max','c.listestid')
		->join('test as b', 'a.test_tid', '=', 'b.tid')
		->join('labtestingdetails as c','b.tid', '=', 'c.test_tid')
		->where('a.Lab_lid', '=', '52')
		->where('c.Lab_lid', '=', '52')
		->where('a.Testgroup_tgid', '=', $id)
		->where('a.isActive', '=', '1')
		->orderBy('a.orderNo')
		->get();

		return \Response::json($results);
			
		} 
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id)
	{
        // dd($id);
		$testparameterName = \Input::get('test_name');

		$existingRecord = \Test::where('name', $testparameterName)->first();

		if(!$existingRecord){

		   $test = new \Test;

		   $test->name=\Input::get('test_name');

		   $test->status=1;

		   $test->save();

		   $tid = $test->tid;

		   $idd=$id;

		   // Continue the process with the obtained tid
		   $labResults = $this->continueProcess($tid,$idd);
		   $responseData = array_merge($test->toArray(), $labResults);
		   return \Response::json(['success' => true, 'data' => $responseData]);
	    }
		else{
			return \Response::json(['success' => false, 'error' => 'Test Parameter Name already exists!']);
		}
	}

	private function continueProcess($tid,$idd)
{
	// dd($tid);

	$labtest = new \LabTestingDetail;

	$labtest->test_tid=$tid;

	$labtest->Lab_lid=52;

	$labtest->defaultval=\Input::get('def_val');

	$labtest->listestid=\Input::get('test_id');

	$labtest->refference_min=\Input::get('min_val');

	$labtest->refference_max=\Input::get('max_val');

    $labtest->save();

    $labhastest = new \LabHasTest;

	$labhastest->Lab_lid=52;

	$labhastest->test_tid=$tid;

	$labhastest->Testgroup_tgid=$idd;

	$labhastest->measurement=\Input::get('unit');

	$labhastest->status = implode('#', [
		\Input::get('test_type'),
		\Input::get('chara_count'),
		\Input::get('val_min'),
		\Input::get('val_max'),
		\Input::get('deci_point')
	]);

	$labhastest->reportname=\Input::get('rep_name');

	$labhastest->viewnorvals = \Input::get('nor_val') ? '1' : '0';

	$labhastest->orderno=\Input::get('ord_num');

	$labhastest->advance_ref = \Input::get('gen_wise_ref') ? '1' : '0';

	$labhastest->hide_when_empty = \Input::get('hide_w_emp') ? '1' : '0';

	$labhastest->selactablevals = \Input::get('sel_res') ? '1' : '0';

	$labhastest->align = \Input::get('align');

	$labhastest->bold_values = \Input::get('bold_val');

	$labhastest->isActive = 1;

	$labhastest->save();

	return ['labtest' => $labtest, 'labhastest' => $labhastest];
}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deactive($id)
	{
		$data = \LabHasTest::where('lhtid',$id)->first();
        // dd($data);
		if($data){

		$data->isActive=0;

		$data->update();

		return \Response::json($data);

	    }
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$data8= \LabHasTest::where('lhtid', $id)->first();

        $data9= $data8->test_tid;

		$data4 = \Test::where('tid', $data9)->first();

		$data10= $data4->tid;

		$data5= \LabHasTest::where('lhtid', $id)->first();

		$data6= \LabTestingDetail::where('test_tid', $data9)->first();

		$data7 = $data5->status;
		$parts = explode('#', $data7);
		$firstPart = $parts[0];
		$SecondPart = $parts[1];
		$ThirdPart = $parts[2];
		$FourthPart = $parts[3];
		$FifthPart = $parts[4];
		if ($data4 && $data5) {
			// Merge the relevant attributes from $data4 and $data5 into a single associative array
			$responseData = [
				'tid' => $data10,
				'name' => $data4->name,
				'reportname' => $data5->reportname,
				'measurement' => $data5->measurement,
				'orderno' => $data5->orderno,
				'refmin' => $data6->refference_min,
				'refmax' => $data6->refference_max,
				'listestid' => $data6->listestid,
				'decimal' => $firstPart,
				'charcount' => $SecondPart,
				'minval' => $ThirdPart,
				'maxval' => $FourthPart,
				'decipoint' => $FifthPart,
				'normalvalue' => $data5->viewnorvals,
				'defval' => $data6->defaultval,
				'advnceref' => $data5->advance_ref,
				'hidewhenempty' => $data5->hide_when_empty,
				'selctval' => $data5->selactablevals,
				'align' => $data5->align,
				'boldvalues' => $data5->bold_values,
				'lhtid' => $data8->lhtid
				// Add other attributes as needed
			];
		
			// Return the merged data as JSON
			return \Response::json($responseData);
			// return response()->json($responseData);
		} else {
			// Handle the case where either $data4 or $data5 is null
			return response()->json(['error' => 'Data not found'], 404);
		}
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($ids)
    {
    // Iterate through the IDs and update each record individually
    // $ids = [$id, $id1, $id2];
    // return 'test';
	// \Log::info($ids);
	$idArray = explode(',', $ids);
	\Log::info($idArray);
    foreach ($idArray as $key => $currentId) {

		\Log::info($currentId);
        // Find the LabHasTest record
        $testLabHas = \LabHasTest::where('test_tid', $currentId)->first();

        if ($testLabHas) {
            $testLabHas->measurement = \Request::input('uniit')[$key];
            $testLabHas->orderno = \Request::input('ord_nummm')[$key];
            $testLabHas->reportname = \Request::input('rep_namm')[$key];
            $testLabHas->update();
        }

        // Find the LabTestingDetail record
        $testLabHasTest = \LabTestingDetail::where('test_tid', $currentId)->first();

        if ($testLabHasTest) {
            $testLabHasTest->refference_min = \Request::input('refer_min')[$key];
            $testLabHasTest->refference_max = \Request::input('refer_max')[$key];
            $testLabHasTest->listestid = \Request::input('lisss_id')[$key];
            $testLabHasTest->update();
        }
    }

    return \Redirect::route('test')->with('success', 'Table Test Parameters Updated Successfully!');
    }

	public function updateall($id)
	{
		// $testlabhas= \LabHasTest::find($id);
		$testparameterName = \Input::get('test_name');

		$existingRecord = \Test::where('name', $testparameterName)->first();

        if(!$existingRecord){

		$test= \Test::where('tid',$id)->first();

		$test->name= \Input::get('test_name');
		
		$test->update();

		$testlabhas= \LabHasTest::where('test_tid',$id)->first();

		$testlabhas->measurement = \Input::get('unit');
		$testlabhas->orderno = \Input::get('ord_num');
		$testlabhas->reportname = \Input::get('rep_name');
		$testlabhas->viewnorvals = \Input::get('nor_val') ? '1' : '0';
		$testlabhas->advance_ref = \Input::get('gen_wise_ref') ? '1' : '0';
		$testlabhas->hide_when_empty = \Input::get('hide_w_emp') ? '1' : '0';
		$testlabhas->selactablevals = \Input::get('sel_res') ? '1' : '0';
		$testlabhas->align = \Input::get('align');
		$testlabhas->bold_values = \Input::get('bold_val');
		$testlabhas->status = implode('#', [
			\Input::get('test_type'),
			\Input::get('chara_count'),
			\Input::get('val_min'),
			\Input::get('val_max'),
			\Input::get('deci_point')
		]);

		$testlabhas->update();

		$testlabdetail= \LabTestingDetail::where('test_tid',$id)->first();

		$testlabdetail->refference_min = \Input::get('min_val');
		$testlabdetail->refference_max = \Input::get('max_val');
		$testlabdetail->listestid = \Input::get('test_id');
		$testlabdetail->defaultval = \Input::get('def_val');

		$testlabdetail->update();

		$combinedData = [
			'test' => $test->toArray(),
			'testlabhas' => $testlabhas->toArray(),
			'testlabdetail' => $testlabdetail->toArray(),
		];
		
		return \Response::json($combinedData);

		// return \Redirect::route('test')->with('success', 'Test Parameter Updated Successfully!');
		
	}
	else{

		$testlabhas= \LabHasTest::where('test_tid',$id)->first();

		$testlabhas->measurement = \Input::get('unit');
		$testlabhas->orderno = \Input::get('ord_num');
		$testlabhas->reportname = \Input::get('rep_name');
		$testlabhas->viewnorvals = \Input::get('nor_val') ? '1' : '0';
		$testlabhas->advance_ref = \Input::get('gen_wise_ref') ? '1' : '0';
		$testlabhas->hide_when_empty = \Input::get('hide_w_emp') ? '1' : '0';
		$testlabhas->selactablevals = \Input::get('sel_res') ? '1' : '0';
		$testlabhas->align = \Input::get('align');
		$testlabhas->bold_values = \Input::get('bold_val');
		$testlabhas->status = implode('#', [
			\Input::get('test_type'),
			\Input::get('chara_count'),
			\Input::get('val_min'),
			\Input::get('val_max'),
			\Input::get('deci_point')
		]);

		$testlabhas->update();

		$testlabdetail= \LabTestingDetail::where('test_tid',$id)->first();

		$testlabdetail->refference_min = \Input::get('min_val');
		$testlabdetail->refference_max = \Input::get('max_val');
		$testlabdetail->listestid = \Input::get('test_id');
		$testlabdetail->defaultval = \Input::get('def_val');

		$testlabdetail->update();

		$combinedData = [
			'testlabhas' => $testlabhas->toArray(),
			'testlabdetail' => $testlabdetail->toArray(),
		];
		
		return \Response::json($combinedData);
		// return \Redirect::route('test')->with('success', 'Test Parameter Name Cannot Update! It already exists! Other fields Updated Successfully!');
	}

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
