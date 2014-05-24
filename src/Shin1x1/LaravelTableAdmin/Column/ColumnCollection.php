<?php
namespace Shin1x1\LaravelTableAdmin\Column;

use Illuminate\Support\Collection;

/**
 * Class ColumnCollection
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class ColumnCollection extends Collection
{
    /**
     * @return Collection
     */
    public function getValidateRules()
    {
        $rules = Collection::make([]);

        $this->filter(function($column) {
            /** @var ColumnInterface $column */
            return !$column->isLabel();
        })->filter(function($column) {
                /** @var ColumnInterface $column */
                return $column->required();
            })->each(function($column) use ($rules) {
                /** @var ColumnInterface $column */
                $rules->put($column->getName(), 'required');
            });

        return $rules;
    }
}