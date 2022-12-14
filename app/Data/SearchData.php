<?php

namespace App\Data;

use App\Enums\TrashedOption;
use Spatie\LaravelData\Data;

class SearchData extends Data
{
    public function __construct(
        public readonly ?string $keyword,
        public readonly ?TrashedOption $trashedOption,
    ) {
    }
}
