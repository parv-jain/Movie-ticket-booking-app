<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeatInfo extends Model
{
    protected $primaryKey = 'seat_info_id';

    protected $dates = ['deleted_at'];

    protected $table = 'seat_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'screen', 'row', 'noOfSeats', 'aisleSeats', 'reservedSeats'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
?>