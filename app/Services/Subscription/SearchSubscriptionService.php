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

        $this->queryBuilder->where(function (Builder $builder) use ($search) {
            $builder->whereLike('reference', "%$search%")
                ->orWhereLike('code', "%$search%")
                ->orWhereLike('merchant_id', "%$search%")
                ->orWhereLike('status', "%$search%")
                ->orWhereHas('user', function(Builder $query) use ($search) {
                    $query->whereLike('name', "%$search%")
                        ->orWhereLike('email', "%$search%")
                        ->orWhereLike('phone', "%$search%");
                });
        });
    }
}
