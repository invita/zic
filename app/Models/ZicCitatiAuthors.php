<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Zrtev
 *
 * @property int $GT_ID
 * @property int $C_ID
 * @property int $IDX
 * @property int $IME
 * @property int $PRIIMEK
 * @mixin \Eloquent
 */
class ZicCitatiAuthors extends Model
{
    protected $table = 'ZIC_CITATI_AUTHORS_IP';
    protected $fillable = [
        'GT_ID',
        'C_ID',
        'IDX',
        'IME',
        'PRIIMEK',
    ];



}
