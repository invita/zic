<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Zrtev
 *
 * @property int $ID
 * @mixin \Eloquent
 */
class Zic extends Model
{
    protected $table = 'ZIC_GLAVNA_TABELA';
    protected $fillable = [
        'ID',
        'OpTipBiblEnote',
        'OpTipologija',
        'OpZvrst',
        'OpJezik',
        'OpDrzava',
        'OpStAvtorjev',
        'OpCobId',
        'OpSistoryUrnId',
        'OpAvtor0',
        'OpAvtor1',
        'OpAvtor2',
        'OpUrednik',
        'OpNaslov',
        'OpVzpNaslov',
        'OpPodnaslov',
        'PvCobId',
        'PvISSN',
        'PvTip',
        'PvAvtor',
        'PvNaslov',
        'PvNaslovKratki',
        'PvPodnaslov',
        'PvVzporedniNaslov',
        'PvZbirka',
        'PvKraj',
        'PvZalozba',
        'PvLetnik',
        'PvLeto',
        'PvSt',
        'PvStran',
        'KwDeskriptor1',
        'text',
        'KwDeskriptor2',
        'text',
        'KwDeskriptor3',
        'text',
        'KwOpombe',
        'text',
        'KwOpombeOBibl',
        'text',
        'KwUDK',
        'KwUDKZaIskanje',
        'BazaINZ',
        'STATUS',
        'DATETIME_ADDED',
        'USER_ID_ADDED',
        'GROUP_ID_ADDED',
    ];



}
