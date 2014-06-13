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
        $rules = new Collection();

        $this->each(function(ColumnInterface $v) use($rules) {
            if (is_null($v->getValidationRule())) {
                return;
            }

            $rules->put($v->getName(), $v->getValidationRule());
        });

        return $rules;
    }
}