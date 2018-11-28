<?php

namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;

use Illuminate\Support\Facades\Log;


/**
 * Class CompanyInformation
 * @package APIServices\Zendesk_Linkedin\Models\CommandTypes
 */
class CompanyInformation extends CommandType
{
    /**
     *
     * @throws \Throwable
     */
    function handleCommand()
    {
        Log::notice("this is the command ..." . $this->nameCommand);
        try {
            $newArray = $this->getTransformArray($this->companyInfo);
            $modelInformationCompany = $this->getInformationModel($newArray);

            if (!empty($modelInformationCompany)) {
                $zendeskResponseBody = $this->getZendeskModel('The following message respond the Command');
                $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskResponseBody, view('linkedin.commands.information_viewer', [
                    'information' => $modelInformationCompany,
                    'message' => 'Company Global Information'
                ])->render());
                $this->getZendeskAPIServiceInstance()->pushNewMessage($zendeskResponse);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $companyInfo
     * @return array
     * @throws \Exception
     */
    public function getInformationModel($companyInfo)
    {
        try {
            $this->companyInformation = $this->linkedInModelService->getCurrentCompanyInfo($companyInfo);
            $response = [
                'company_type' => $this->companyInformation->getCompanyType()->getName(),
                'description' => $this->companyInformation->getDescription(),
                'emailDomains' => $this->companyInformation->getDomain(),
                'employeeCountRange' => $this->companyInformation->getEmployeeCountRange()->get('name'),
                'founded_year' => $this->companyInformation->getFoundedYear(),
                'industries' => $this->companyInformation->getIndustries()->getName(),
                'locations' => [
                    'city' => $this->companyInformation->getLocations()->getAddress()->getCity(),
                    'postalCode' => $this->companyInformation->getLocations()->getAddress()->get('postalCode'),
                    'street' => $this->companyInformation->getLocations()->getAddress()->getStreet1(),
                ],
                'logo-url' => $this->companyInformation->getLogoUrl(),
                'name' => $this->companyInformation->getName(),
                'specialties' => $this->companyInformation->getSpecialties()->getSpecialty()->get('specialities'),
                'website_url' => $this->companyInformation->getWebsiteUrl()
            ];
            return $response;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}