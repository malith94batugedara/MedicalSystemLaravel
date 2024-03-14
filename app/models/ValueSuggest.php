<?php

class ValueSuggest extends \Eloquent {
	protected $fillable = [
		'lhtid',
		'value'
	];
	protected $table = 'value_suggests';
	public $timestamps = false;
}