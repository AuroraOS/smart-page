<?php
namespace SmartPage\Model;

use Illuminate\Database\Eloquent\Model;

class SPConfigGroups extends Model {

    protected $table = 'sp_config_groups';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'type'
    ];

    public function config() {
        return $this->hasMany('\SmartPage\Model\SPConfig', 'group_id');
    }



}
