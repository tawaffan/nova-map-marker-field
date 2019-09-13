<?php

namespace Tawaffan\NovaMapMarkerField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaMapMarkerField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-map-marker-field';

    /**
     * The field show on index
     * 
     * @var boolean
     */
    public $showOnIndex = false;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        $attribute = [
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        ];

        parent::__construct($name, $attribute, $resolveCallback);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    public function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        foreach ($requestAttribute as $field => $modelField) {
            if (in_array($field, ["latitude", "longitude"])
                && $request->exists($modelField)
            ) {
                $model->{$modelField} = json_decode($request[$modelField], true);
            }
        }
    }

    /**
     * Get the validation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function getRules(NovaRequest $request)
    {
        return [
            $this->attribute["latitude"] => is_callable($this->rules)
                ? call_user_func($this->rules, $request)
                : $this->rules,
            $this->attribute["longitude"] => is_callable($this->rules)
                ? call_user_func($this->rules, $request)
                : $this->rules,
        ];
    }

    /**
     * Get the creation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array|string
     */
    public function getCreationRules(NovaRequest $request)
    {
        $rules = [
            $this->attribute["latitude"] => is_callable($this->creationRules)
                ? call_user_func($this->creationRules, $request)
                : $this->creationRules,
            $this->attribute["longitude"] => is_callable($this->creationRules)
                ? call_user_func($this->creationRules, $request)
                : $this->creationRules,
        ];

        return array_merge_recursive(
            $this->getRules($request), $rules
        );
    }

    /**
     * Get the update rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array|string
     */
    public function getUpdateRules(NovaRequest $request)
    {
        $rules = [
            $this->attribute["latitude"] => is_callable($this->updateRules)
                ? call_user_func($this->updateRules, $request)
                : $this->updateRules,
            $this->attribute["longitude"] => is_callable($this->updateRules)
                ? call_user_func($this->updateRules, $request)
                : $this->updateRules,
        ];

        return array_merge_recursive(
            $this->getRules($request), $rules
        );
    }

    /**
     * Set default latitude
     * 
     * @param  $value
     * @return $this
     */
    public function defaultLatitude($value)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["default_latitude"] = $value;

        return $this->withMeta([__FUNCTION__ => $value]);
    }

    /**
     * Set default longitude
     * 
     * @param  $value
     * @return $this
     */
    public function defaultLongitude($value)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["default_longitude"] = $value;

        return $this->withMeta([__FUNCTION__ => $value]);
    }

    /**
     * Set default zoom
     * 
     * @param  $value
     * @return $this
     */
    public function defaultZoom($value)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["default_zoom"] = $value;

        return $this->withMeta([__FUNCTION__ => $value]);
    }

    /**
     * Set default zoom for detail
     * 
     * @param  $value
     * @return $this
     */
    public function defaultZoomDetail($value)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["default_zoom_detail"] = $value;

        return $this->withMeta([__FUNCTION__ => $value]);
    }

    /**
     * Set height map
     * 
     * @param  $value
     * @return $this
     */
    public function height($value)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["height"] = $value;

        return $this->withMeta([__FUNCTION__ => $value]);
    }

    /**
     * Set latitude field
     * 
     * @param  $field
     * @return $this
     */
    public function latitude($field)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["latitude"] = $field;

        return $this->withMeta([__FUNCTION__ => $field]);
    }

    /**
     * Set longitude field
     * 
     * @param  $field
     * @return $this
     */
    public function longitude($field)
    {
        if (! is_array($this->attribute)) {
            $this->attribute = [];
        }

        $this->attribute["longitude"] = $field;

        return $this->withMeta([__FUNCTION__ => $field]);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $attribute = $attribute ?? $this->attribute;

        if (is_array($attribute)) {
            if (! is_array($this->value)) {
                $this->value = [];
            }

            foreach ($attribute as $field) {
                $this->value[$field] = $this->resolveAttribute($resource, $field);
            }

            return;
        }

        parent::resolve($resource, $attribute);
    }
}
