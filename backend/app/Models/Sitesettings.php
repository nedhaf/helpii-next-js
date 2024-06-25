<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Sitesettings extends Model
{
    protected $table = 'site_setting';

    protected  $primaryKey = 'id';

    protected $fillable = ['fieldname','fieldvalue'];

    public static function boot() {
        parent::boot();
    }

    
}
