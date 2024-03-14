<?php

class TestGroup extends \Eloquent {

	protected $table = 'Testgroup';

	// protected $fillable = [];
	protected $fillable = [
		
		'isActive',
		'name',
		'price',
		'Lab_lid',
		'testingtime',
		'comment',
		'cost',
		'name_col',
		'value_col',
		'unit_col',
		'flag_col',
		'ref_col',
		'name_col_head',
		'value_col_head',
		'unit_col_head',
		'flag_col_head',
		'ref_col_head',
		'name_col_width',
		'value_col_width',
		'unit_col_width',
		'flag_col_width',
		'ref_col_width',
		'custom_configs',
		'name_col_align',
		'value_col_align',
		'unit_col_align',
		'flag_col_align',
		'ref_col_align',
		'age_ref',
		'testCode',
		'sample_containers_scid',
		'barcode_name',
		'tgsection_id',
		'parameter_wise_barcode',
		'analyzers_anid',
		'testingcategory_tcid',
		'testinginput_tiid',
		'view_analyzer'
		
	];

	protected $primaryKey = 'tgid';

	public $timestamps = false;

	public function lab_has_test(){
        return $this->belongsTo(\LabHasTest::class , 'Testgroup_tgid' ,'tgid');
    }

}