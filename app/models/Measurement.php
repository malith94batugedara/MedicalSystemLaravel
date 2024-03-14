<?php

class Measurement extends \Eloquent {
	protected $fillable = [
		'name',
		'symble',
		'power'
	];

	protected $table = 'measurements';

	public $timestamps = false;
}