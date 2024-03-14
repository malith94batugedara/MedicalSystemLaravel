<?php

// use app\models\LabHastest;
// use App\models\TestGroup;
// use App\models\LabTestingDetail;
// use App\models\Test;
// use DB;
class TestController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$test=\TestGroup::all();
		$lab_has_tests = \LabHasTest::all();
		$tgidValues = \Testgroup::pluck('tgid');
		$lab_has_test_tgids = \LabHasTest::pluck('Testgroup_tgid');
		$labLidValue = \Testgroup::where('tgid', $tgidValues)->pluck('Lab_lid');
		$_SESSION['lid'] = $labLidValue;
		$testinputs = \TestingInput::all();
		$analyzers = \Analyzer ::all();
		$sample_containers = \SampleContainer::all();
		$test_categories = \TestingCategory::all();
		$test_count = \TestGroup::all()->count();
		$test_countactive = \TestGroup::where('isActive',1)->count();
		$test_countinactive = \TestGroup::where('isActive',0)->count();
		$tg_sections = \TestGroupSection::all();
		$genders = \Gender::all();
		$testdata = \DB::table('Testgroup as a')
            ->select('a.tgid', 'a.price', 'a.name', DB::raw('IFNULL(b.lhtid, "not") as lhtid'))
            ->leftJoin('Lab_has_test as b', 'a.tgid', '=', 'b.Testgroup_tgid')
            ->groupBy('a.tgid')
            ->get();
		$value_suggests = \ValueSuggest::all();
		$materials = \Material::all();
		$measurements = \Measurement::all();
		return View::make('test',array('test' => $test,'testinputs' => $testinputs,'analyzers' => $analyzers,'sample_containers' => $sample_containers,'test_categories' => $test_categories,'test_count' => $test_count,'tg_sections' => $tg_sections,'lid' => $_SESSION['lid'], 'test_countactive'=> $test_countactive ,'test_countinactive'=> $test_countinactive , 'genders'=> $genders , 'tgidValues' => $tgidValues , 'lab_has_test_tgids' => $lab_has_test_tgids , 'lab_has_tests' => $lab_has_tests , 'testdata' => $testdata,'value_suggests' => $value_suggests, 'materials' => $materials , 'measurements'=> $measurements ));
	}

    public function changeStatus($id){
		
		 $testgroup =\TestGroup::where('tgid',$id)->first(); 
		//  dd($testgroup);
		 if($testgroup->isActive == 1){
			$testgroup->isActive = 0;
			$testgroup->update();
			return \Redirect::to('test')->with('success', 'Test group inactivated successfully');
		 }
		 else{
			$testgroup->isActive = 1;
			$testgroup->update();
			return \Redirect::to('test')->with('success', 'Test group activated successfully');
		 }
		//  return back();
		 return Redirect::to('test');
	}

	public function deleteTest($id){
		$testgroup =\TestGroup::where('tgid',$id)->first();
		if($testgroup){
			$testgroup->delete();
			return Redirect::to('test')->with('success', 'Test group deleted successfully');
		}
		else{
			return Redirect::to('test')->with('error', 'Test group cannot delete');
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
	public function store()
	{
		$testName = \Input::get('tg_name');
		$existingRecord = \TestGroup::where('name', $testName)->first();
        // dd($existingRecord);
        if ($existingRecord) {
			return \Redirect::route('test')->with('error', 'Test Group Name already exists');
        }
		$testgroup= new \TestGroup;

		// $testgroup->tgid = \Input::get('tg_id');
		$testgroup->name = \Input::get('tg_name');
		$testgroup->testCode = \Input::get('tcode');
		$testgroup->price = \Input::get('tg_price');
		$testgroup->cost = \Input::get('tg_cost');
		$testgroup->testingtime = \Input::get('tg_time');
		$testgroup->sample_containers_scid = \Input::get('smpcon');
		$testgroup->testingcategory_tcid = \Input::get('rptcat');
		$testgroup->testinginput_tiid = \Input::get('spc');
		$selectedValue = \Input::get('ana');
		$selectedValue = empty($selectedValue) ? null : $selectedValue;
		$testgroup->analyzers_anid = $selectedValue;
		$testgroup->tgsection_id = \Input::get('sec');
		$testgroup->parameter_wise_barcode = \Input::get('par_wise_bar') ? '1' : '0' ;
		$testgroup->view_analyzer = \Input::get('view_ana') ? '1' : '0' ;
		$testgroup->comment = \Input::get('tcomment');
		
		$testgroup->name_col_head = \Input::get('tname');
		$testgroup->name_col_width = \Input::get('twidth');
		$testgroup->name_col_align = \Input::get('talign');

		$testgroup->value_col_head = \Input::get('vname');
		$testgroup->value_col_width = \Input::get('vwidth');
		$testgroup->result_col_align = \Input::get('valign');

		$testgroup->unit_col_head = \Input::get('uname');
		$testgroup->unit_col_width = \Input::get('uwidth');
		$testgroup->unit_col_align = \Input::get('ualign');

		$testgroup->flag_col_head = \Input::get('fname');
		$testgroup->flag_col_width = \Input::get('fwidth');
		$testgroup->flag_col_align = \Input::get('falign');

		$testgroup->ref_col_head = \Input::get('rname');
		$testgroup->ref_col_width = \Input::get('rwidth');
		$testgroup->ref_col_align = \Input::get('ralign');

		$testgroup->isActive = '1';

		$testgroup->Lab_lid = '52';

		$testgroup->save();

        return \Redirect::to('test')->with('success', 'Test group added successfully');


    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		
		$data = \TestGroup::find($id);
		
		return \Response::json($data);
       
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$testName = \Input::get('tg_name');
		$existingRecord = \TestGroup::where('name', $testName)->first();
        // dd($existingRecord);
        if ($existingRecord) {

			$testgroup= \TestGroup::find($id);

			// $testgroup->tgid = \Input::get('tg_id');
			// $testgroup->name = \Input::get('tg_name');
			$testgroup->testCode = \Input::get('tcode');
			$testgroup->price = \Input::get('tg_price');
			$testgroup->cost = \Input::get('tg_cost');
			$testgroup->testingtime = \Input::get('tg_time');
			$testgroup->sample_containers_scid = \Input::get('smpcon');
			$testgroup->testingcategory_tcid = \Input::get('rptcat');
			$testgroup->testinginput_tiid = \Input::get('spc');
			$testgroup->analyzers_anid = \Input::get('ana');
			$testgroup->tgsection_id = \Input::get('sec');
			$testgroup->parameter_wise_barcode = \Input::get('par_wise_bar') ? '1' : '0' ;
			$testgroup->view_analyzer = \Input::get('view_ana') ? '1' : '0' ;
			$testgroup->comment = \Input::get('tcomment');
			
			$testgroup->name_col_head = \Input::get('tname');
			$testgroup->name_col_width = \Input::get('twidth');
			$testgroup->name_col_align = \Input::get('talign');
	
			$testgroup->value_col_head = \Input::get('vname');
			$testgroup->value_col_width = \Input::get('vwidth');
			$testgroup->result_col_align = \Input::get('valign');
	
			$testgroup->unit_col_head = \Input::get('uname');
			$testgroup->unit_col_width = \Input::get('uwidth');
			$testgroup->unit_col_align = \Input::get('ualign');
	
			$testgroup->flag_col_head = \Input::get('fname');
			$testgroup->flag_col_width = \Input::get('fwidth');
			$testgroup->flag_col_align = \Input::get('falign');
	
			$testgroup->ref_col_head = \Input::get('rname');
			$testgroup->ref_col_width = \Input::get('rwidth');
			$testgroup->ref_col_align = \Input::get('ralign');
	
			// $testgroup->isActive = '1';
	
			$testgroup->update();

			return \Redirect::route('test')->with('error', 'Test Group Updated Successfully! Test Group Name Cannot update it already exists');
        }
		else{
		$testgroup= \TestGroup::find($id);

		// $testgroup->tgid = \Input::get('tg_id');
		$testgroup->name = \Input::get('tg_name');
		$testgroup->testCode = \Input::get('tcode');
		$testgroup->price = \Input::get('tg_price');
		$testgroup->cost = \Input::get('tg_cost');
		$testgroup->testingtime = \Input::get('tg_time');
		$testgroup->sample_containers_scid = \Input::get('smpcon');
		$testgroup->testingcategory_tcid = \Input::get('rptcat');
		$testgroup->testinginput_tiid = \Input::get('spc');
		$testgroup->analyzers_anid = \Input::get('ana');
		$testgroup->tgsection_id = \Input::get('sec');
		$testgroup->parameter_wise_barcode = \Input::get('par_wise_bar') ? '1' : '0' ;
		$testgroup->view_analyzer = \Input::get('view_ana') ? '1' : '0' ;
		$testgroup->comment = \Input::get('tcomment');
		
		$testgroup->name_col_head = \Input::get('tname');
		$testgroup->name_col_width = \Input::get('twidth');
		$testgroup->name_col_align = \Input::get('talign');

		$testgroup->value_col_head = \Input::get('vname');
		$testgroup->value_col_width = \Input::get('vwidth');
		$testgroup->result_col_align = \Input::get('valign');

		$testgroup->unit_col_head = \Input::get('uname');
		$testgroup->unit_col_width = \Input::get('uwidth');
		$testgroup->unit_col_align = \Input::get('ualign');

		$testgroup->flag_col_head = \Input::get('fname');
		$testgroup->flag_col_width = \Input::get('fwidth');
		$testgroup->flag_col_align = \Input::get('falign');

		$testgroup->ref_col_head = \Input::get('rname');
		$testgroup->ref_col_width = \Input::get('rwidth');
		$testgroup->ref_col_align = \Input::get('ralign');

		// $testgroup->isActive = '1';

		$testgroup->update();

        return \Redirect::to('test')->with('success', 'Test group updated successfully');
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
