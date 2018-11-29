<?php

namespace APIServices\LinkedIn\Models\API;
/**
 * Class Statistics.
 *
 * @method int                  getEmployeeCount()
 * @method int                  getNonEmployeeCount()
 * @method int                  getCount()
 * @method Countries            getCountries()
 * @method Functions            getFunctions()
 * @method Industries           getIndustries()
 * @method Seniorities          getSeniorities()
 * @method CountsByMonth        getCountsByMonth()
 *
 */
class Statistics extends BaseObject
{

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'countries' => Countries::class,
            'functions' => Functions::class,
            'industries' => Industries::class,
            'seniorities' => Seniorities::class,
            'counts_by_month'=>CountsByMonth::class
        ];

    }
}