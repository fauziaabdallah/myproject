<?php

namespace App\Models;
use App\Models\Gapco;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = [
        "station_name",
        "location",
        "organization_id"
    ];
    public function organization(){
        return $this->belongsTo(Gapco::class);
    }
}
