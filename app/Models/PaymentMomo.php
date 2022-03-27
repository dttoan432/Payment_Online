<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PaymentMomo extends Model
{
    use HasFactory;
    protected $table = 'payments_momo';

    protected $fillable = [
        'code',
        'money',
        'content',
        'request_id',
        'status',
        'time',
        'result_code',
    ];

    const STATUS = [
        'UNPAID'        => 0,
        'SUCCESS'       => 1,
        'FAILURE'       => 2,
    ];
}
