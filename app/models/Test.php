<?php

class Test extends \Eloquent {
	protected $fillable = [
		'name'
	];

	protected $table = 'test';

	protected $primaryKey = 'tid';

	public $timestamps = false;
}