<?php
namespace SmartPage\Model;

use Illuminate\Database\Eloquent\Model;

class SPConfigTypes extends Model {

    protected $table = 'sp_config_types';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];

    public function config() {
        return $this->hasMany('\SmartPage\Model\SPConfig', 'type_id');
    }
}
