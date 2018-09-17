<?php
namespace SmartPage\Model;

use Illuminate\Database\Eloquent\Model;

class SPModules extends Model {

    protected $table = 'sp_modules';
    protected $primaryKey = 'id';
    protected $fillable = [
    	'name',
    	'tpl',
		  'data',
      'opt',
      'func',
      'type'
    ];

  


}
