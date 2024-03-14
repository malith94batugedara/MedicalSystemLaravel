<?php

class TestLabMaterial extends \Eloquent {
	protected $fillable = [
		'test_tid',
		'Lab_has_materials_lmid',
		'qty',
		'unit'
	];

	protected $table = 'test_Labmeterials';

	public $timestamps = false;
}