<?php


namespace APIServices\LinkedIn\Models\API;

/**
 * Class Company.
 *
 * @method string              getDescription()
 * @method EmployeeCountRage   getEmployeeCountRange()
 * @method int                 getFoundedYear()
 * @method string              getLogoUrl()
 * @method string              getName()
 * @method string              getWebsiteUrl()
 * @method Domains             getDomain()
 * @method Locations           getLocations()
 * @method Specialties         getSpecialties()
 * @method CompanyType         getCompanyType()
 * @method Industries          getIndustries()
 */
class Company extends BaseObject
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'company_type' => CompanyType::class,
            'email_domains' => Domains::class,
            'employee_count_range' => EmployeeCountRage::class,
            'locations' => Locations::class,
            'specialties' => Specialties::class,
            'industries'=> Industries::class
        ];
    }

}