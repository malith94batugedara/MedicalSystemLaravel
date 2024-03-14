<?php

class Material extends \Eloquent {
	protected $fillable = [
		'name'
	];

	protected $table = 'materials';

	public $timestamps = false;
}