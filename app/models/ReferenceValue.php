<?php

class ReferenceValue extends \Eloquent {
	protected $fillable = [
		'gender_idgender',
		'Lab_has_test_lhtid',
		'ageMin',
		'ageMax',
		'rangeMin',
		'rangeMax',
		'ageType'
	];

	protected $table = 'reference_values';

	public $timestamps = false;
}