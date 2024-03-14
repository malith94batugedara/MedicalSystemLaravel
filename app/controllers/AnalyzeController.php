<?php

class AnalyzeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
	
		$analyzer = new \Analyzer;

        $analyzer->name = \Input::get('ananame'); // Using Input::get to retrieve the form data

		$analyzer->Lab_lid = '52';

		$analyzer->status = '1';

        $analyzer->save();

        return \Redirect::to('test')->with('success', 'Analyzer added successfully');
		
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
	try {
		$anlyzer =\Analyzer::where('anid',$id)->first();
		if($anlyzer){
			$anlyzer->delete();
			return \Redirect::to('test')->with('success', 'Analyzer deleted successfully');
		}
		else {
			// Handle the case where the analyzer with the specified ID was not found
			return \Redirect::to('test')->with('error', 'Analyzer not found');
		}
	}
	catch (\Exception $exception) {
		// Handle other exceptions
		return \Redirect::to('test')->with('error', 'Already used this Analyzer Cannot delete this analyzer');
	}
	}


}
