<?php

namespace App\Models\Traits\Filterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

trait FilterByQueryString
{
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

            $method = Str::camel("scope{$key}");
            if (! method_exists($this, $method)) {
                continue;
            }

            $this->{$method}($query, ...(array) $values);
        }

        return $query;
    }
}
