<?php


namespace App\Http\Bots\TaroBot\src\Reps;

use App\Http\Bots\TaroBot\src\ReplyBtns;
use App\Http\Bots\TaroBot\src\Models\Amulet;
use Illuminate\Database\Eloquent\Collection;

class AmuletRep
{
    public const AMULET_LVL = [
        ReplyBtns::HIGH_AMULET_LVL_TEXT => 'high',
        ReplyBtns::MIDD_AMULET_LVL_TEXT => 'midd',
        ReplyBtns::WEAK_AMULET_LVL_TEXT => 'weak',
    ];
    public const AMULET_TYPE = [
        ReplyBtns::HEALTH_CATEGORY_TEXT => 'health',
        ReplyBtns::FINANCE_CATEGORY_TEXT => 'finance',
        ReplyBtns::RELATIONSHIP_CATEGORY_TEXT => 'relationship',
        ReplyBtns::FAMILY_WELL_BEING_CATEGORY_TEXT => 'family',
    ];

    public static function getAmuletsByCategoryAndLevel(string $category, string $lvl): Collection
    {
        return Amulet::where('category', '=', $category)
            ->where('lvl', '=', $lvl)
            ->get();
    }
}
