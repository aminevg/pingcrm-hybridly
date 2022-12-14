<?php

namespace App\Models;

use App\Data\SearchData;
use App\Enums\TrashedOption;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    /** @return BelongsTo<Organization,Contact> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Account,Contact> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeOrderByName(Builder $query): Builder
    {
        return $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeFilter(Builder $query, SearchData $data): Builder
    {
        return $query
            ->when(
                $data->keyword,
                fn (Builder $query, $search) => $query->where(
                    fn ($query) => $query
                        ->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%'),
                ),
            )
            ->when($data->trashedOption, function (
                Builder $query,
                $trashedOption,
            ) {
                if ($trashedOption === TrashedOption::WITH_TRASHED) {
                    $query->withTrashed();
                } elseif ($trashedOption === TrashedOption::ONLY_TRASHED) {
                    $query->onlyTrashed();
                }
            });
    }
}
