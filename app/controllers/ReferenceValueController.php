<?php

class ReferenceValueController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$data = \ReferenceValue::where('Lab_has_test_lhtid',$id)->get();

		return \Response::json($data);
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
		//  dd($id);
		$refval= new \ReferenceValue; 

		$refval->ageType = \Input::get('age_type');

		$refval->ageMin = \Input::get('min_age');

		$refval->ageMax = \Input::get('max_age');

		$refval->gender_idgender = \Input::get('gender');

		$refval->rangeMin = \Input::get('min_ref');

		$refval->rangeMax = \Input::get('max_ref');

		$refval->Lab_has_test_lhtid = $id;

		$refval->save();

		return \Response::json($refval);
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
		$refvall= \ReferenceValue::where('id',$id);

		if($refvall){
			$refvall->delete();
		}
	}


}
