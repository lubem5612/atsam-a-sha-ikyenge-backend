<?php


namespace App\Services\User;


use App\Helpers\SearchHelper;
use Illuminate\Database\Eloquent\Builder;

class SearchUserService
{
    use SearchHelper;

    public function searchTerms()
    {
        $role = request()->query('role');
        $search = $this->searchParam;
        if (isset($role)) {
            $this->queryBuilder->where('role', $role);
        }

        $this->queryBuilder->where(function (Builder $query) use ($search) {
            $query
                ->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        });

        return $this;
    }
}
