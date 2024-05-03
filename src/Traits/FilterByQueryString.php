<?php

namespace HamidrezaMozhdeh\FilterByQueryString\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

trait FilterByQueryString
{
    /**
     * Filter the query.
     *
     * @param Builder $query The query builder instance.
     * @param FormRequest $request It's better to define a specific FormRequest for each action.
     * @param array $only Accept only a list of methods that have scopes.
     * @param array $except Accept all scopes from the URL query string except these methods listed.
     * @param string|null $prefix The prefix of filters. The default is null.
     * @param string $requestMethod The method of validation. The default method of validation is 'Validated'.
     * @return Builder The modified query builder instance.
     */
    public function scopeFilter(
        Builder $query,
        FormRequest $request,
        array $only = [],
        array $except = [],
        string $prefix = null,
        string $requestMethod = 'validated'
    ): Builder {
        if (! $request->{$requestMethod}($prefix)) {
            return $query;
        }

        foreach ($request->{$requestMethod}($prefix) as $key => $values) {

            if ($only && ! in_array($key, $only)) {
                continue;
            }

            if ($except && in_array($key, $except)) {
                continue;
            }

            $method = Str::camel("scope-{$key}");
            if (! method_exists($this, $method)) {
                continue;
            }

            $this->{$method}($query, ...(array) $values);
        }

        return $query;
    }
}
