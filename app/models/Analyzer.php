<?php

class Analyzer extends \Eloquent {

	protected $table = 'analyzers';

	protected $primaryKey = 'anid';

	protected $fillable = ['name'];

	public $timestamps = false;
}