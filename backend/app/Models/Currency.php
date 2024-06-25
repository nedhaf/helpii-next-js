<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Currency extends Model
{
    protected $table = 'currency';

    protected  $primaryKey = 'id';

    protected $fillable = ['name','iso_code','status','symbol'];

    public static function boot()
    {
        parent::boot();

        
    }

    public static function GetAll() {
        $skills = static::where('status', 1)->get();
        $selection = array();
        $selection[""]=__('strings.new.select_currency');
        foreach ($skills as $key => $value) {
            $selection[$value->id]=$value->symbol;            
        }        
        return $selection;
    }
}
