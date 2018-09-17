<?php
namespace SmartPage\Model;

use Illuminate\Database\Eloquent\Model;

class SPPages extends Model
{
    protected $table = 'sp_pages';
    protected $primaryKey = 'id';
    protected $fillable = [
        'page',
		    'name',
        'tpl',
        'title',
        'description',
		    'keywords',
		    'header_type',
		    'page_modules',
        'image',
        'video',
        'plugin',
        'default'
    ];


}
