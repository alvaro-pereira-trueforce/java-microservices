<?php
/**
 * Created by PhpStorm.
 * User: pablo.daza
 * Date: 4/19/18
 * Time: 12:08 PM
 */

namespace App\Database\Traits;


trait toArrayFiltered {
    /**
     * Use this trait on models to filter and remove all the null attributes when you convert the
     * model or collection to
     * array or json 
     *
     * @return array
     */
    public function toArray() {
        $attributes = array_filter($this->attributesToArray());
        $relations = array_filter($this->relationsToArray());
        return array_merge($attributes, $relations);
    }
}