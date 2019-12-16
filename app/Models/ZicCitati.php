<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Zrtev
 *
 * @property int $gtid
 * @property int $slo
 * @property int $cid
 * @property int $COBISSid
 * @property int $sistoryId
 * @property int $cnastrani
 * @property int $avtor0
 * @property int $avtor1
 * @property int $naslov0
 * @property int $naslov1
 * @property int $vir
 * @property int $kraj
 * @property int $zalozba
 * @property int $letnik
 * @property int $leto
 * @property int $stevilka
 * @property int $str
 * @property int $URL
 * @property int $DOI
 * @property int $STATUS
 * @property int $DATETIME_ADDED
 * @property int $USER_ID_ADDED
 * @property int $GROUP_ID_ADDED
 * @mixin \Eloquent
 */
class ZicCitati extends Model
{
    protected $table = 'ZIC_CITATI_V2';
    protected $fillable = [
        'gtid',
        'slo',
        'cid',
        'COBISSid',
        'sistoryId',
        'cnastrani',
        'avtor0',
        'avtor1',
        'naslov0',
        'naslov1',
        'vir',
        'kraj',
        'zalozba',
        'letnik',
        'leto',
        'stevilka',
        'str',
        'URL',
        'DOI',
        'STATUS',
        'DATETIME_ADDED',
        'USER_ID_ADDED',
        'GROUP_ID_ADDED',
    ];

}
