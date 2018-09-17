<?php
namespace SmartPage\Model;

use Illuminate\Database\Eloquent\Model;

class SPConfig extends Model {

    protected $table = 'sp_config';
    protected $primaryKey = 'id';
    protected $fillable = [
    	  'group_id',
    	  'type_id',
        'name',
		    'description',
        'data',
        'opt'
    ];

    public function group(){
        return $this->hasOne('\SmartPage\Model\SPConfigGroups', 'id');
    }

    public function type(){
        return $this->hasOne('\SmartPage\Model\SPConfigTypes', 'type_id');
    }
}
