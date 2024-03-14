<?php

class MaterialConsumptionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$data1= \LabHasTest::where('lhtid',$id)->first();

		$data2= $data1->test_tid;

        if($data1){
		   $result = \DB::table('materials as a')
           ->join('Lab_has_materials as c', 'c.materials_mid', '=', 'a.mid')
           ->join('test_Labmeterials as b', 'b.Lab_has_materials_lmid', '=', 'c.lmid')
           ->join('measurements as d', 'b.unit', '=', 'd.msid')
           ->select('a.name as name', 'b.qty as value', 'd.symble as unit','b.id as id')
           ->where('c.Lab_lid', '52')
           ->where('b.test_tid', $data2)
           ->get();
	
	    $resultnew=json_encode($result);
	
        return \Response::json($resultnew);

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
		$data1= \LabHasTest::where('lhtid',$id)->get();

		foreach ($data1 as $record) {
			// Access properties of each $record
			$id = $record->test_tid;
			// ...
		}

		$data2= $id;

		$data3= new \TestLabMaterial;

		$data3->test_tid=$data2;

		$data4= \Input::get('material');

		$data5= \LabHasMaterial::where('materials_mid',$data4)->first();

		$data6=$data5->lmid;

		$data3->Lab_has_materials_lmid=$data6;

		$data3->qty= \Input::get('val');

		$data3->unit= \Input::get('unit');

		$data3->save();

		return \Response::json($data3);
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
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$labmat= \TestLabMaterial::where('id',$id);

		if($labmat){
			$labmat->delete();
		}
	}


}
