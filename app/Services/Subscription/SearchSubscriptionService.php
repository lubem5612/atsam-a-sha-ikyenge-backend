<?php


namespace App\Services\Subscription;


use App\Helpers\SearchHelper;
use Illuminate\Database\Eloquent\Builder;

class SearchSubscriptionService
{
    use SearchHelper;

    public function searchTerms()
    {
        $search = $this->searchParam;
        $reference = request()->query('reference');
        if (isset($reference)) $this->queryBuilder->where('reference', $reference);

        $this->queryBuilder->where(function (Builder $builder) use ($search) {
            $builder->whereLike('code', "%$search%")
                ->orWhereLike('status', "%$search%")
                ->orWhereHas('user', function(Builder $query) use ($search) {
                    $query->whereLike('name', "%$search%")
                        ->orWhereLike('email', "%$search%")
                        ->orWhereLike('phone', "%$search%");
                });
        });
    }
}
