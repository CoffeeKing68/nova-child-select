<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;

Route::get('/options-new/{resource}', function (NovaRequest $request) {
    $attribute = $request->input('attribute');
    $multiParents = json_decode($request->input('multiParents'), true);
    $parentValues = json_decode($request->input('parents'), true);
    
    $resource = $request->newResource();
    $fields = $resource->updateFields($request);
    $field = $fields->findFieldByAttribute($attribute);
    $options = $field->getOptions($parentValues, $multiParents);
    
    return $options;
});
