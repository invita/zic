<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Zrtev
 *
 * @property int $ZIC_ID
 * @property int $IDX
 * @property int $IME
 * @property int $PRIIMEK
 * @mixin \Eloquent
 */
class ZicEditors extends Model
{
    protected $table = 'ZIC_EDITORS_IP';
    protected $fillable = [
        'ZIC_ID',
        'IDX',
        'IME',
        'PRIIMEK',
    ];



}
