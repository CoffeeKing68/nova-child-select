<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

Route::get('/options-new/{resource}', function (NovaRequest $request) {
    $attribute = $request->input('attribute');
    $multiParents = json_decode($request->input('multiParents'), true);
    $parentValues = json_decode($request->input('parents'), true);

    $resource = $request->newResource();
    $fields = $resource->updateFields($request);

    function recursiveFind(array $haystack, $callback)
    {
        $iterator  = new RecursiveArrayIterator($haystack);
        $recursive = new RecursiveIteratorIterator(
            $iterator,
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if ($callback($key, $value)) {
                yield $value;
            }
        }
    }
    function recursiveToArray($collection)
    {
        return collect($collection)->map(function ($value) {
            if ($value instanceof Collection) {
                return recursiveToArray($value->toArray());
            } else if (is_array($value)) {
                return recursiveToArray($value);
            } else {
                return $value;
            }
        })->toArray();
    }

    $test = recursiveToArray($fields->toArray());
    $fields = recursiveFind($test, function ($key, $value) use ($attribute) {
        return ($value instanceof Field && isset($value->attribute) && $value->attribute == $attribute);
    });

    $field = collect($fields)->first();
    $options = $field->getOptions($parentValues, $multiParents);

    return $options;
});
