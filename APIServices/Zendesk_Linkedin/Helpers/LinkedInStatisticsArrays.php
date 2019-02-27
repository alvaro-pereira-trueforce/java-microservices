<?php


namespace APIServices\Zendesk_Linkedin\Helpers;

/**
 * Class LinkedInStatisticsArrays
 * @package APIServices\Zendesk_Linkedin\Helpers
 */
class LinkedInStatisticsArrays
{

    /**
     * @return array
     */
    public function getFunctionsCode()
    {
        $response = [
            '-1' => 'None',
            '1' => 'Accounting',
            '2' => 'Administrative',
            '3' => 'Arts and Design',
            '4' => 'Business Development',
            '5' => 'Community & Social Services',
            '6' => 'Consulting',
            '7' => 'Education',
            '8' => 'Engineering',
            '9' => 'Entrepreneurship',
            '10' => 'Finance',
            '11' => 'Healthcare Services',
            '12' => 'Human Resources',
            '13' => 'Information Technology',
            '14' => 'Legal',
            '15' => 'Marketing',
            '16' => 'Media & Communications',
            '17' => 'Military & Protective Services',
            '18' => 'Operations',
            '19' => 'Product Management',
            '20' => 'Program & Product Management',
            '21' => 'Purchasing',
            '22' => 'Quality Assurance',
            '23' => 'Real Estate',
            '24' => 'Rersearch',
            '25' => 'Sales',
            '26' => 'Support'
        ];
        return $response;
    }

    /**
     * @return array
     */
    public function getSeniorityCodes()
    {

        $response = [
            '1' => 'Unpaid',
            '2' => 'Training',
            '3' => 'Entry-level',
            '4' => 'Senior',
            '5' => 'Manager',
            '6' => 'Director',
            '7' => 'Vice President (VP)',
            '8' => 'Chief X Officer (CxO)',
            '9' => 'Partner',
            '10' => 'Owner'
        ];
        return $response;
    }

    /**
     * @return array
     */
    public function getMonthCodes()
    {
        $response = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];
        return $response;
    }

    public function getGeographyCodes()
    {
        $response = [
            'us' => 'United States',
            'dz' => 'Algeria',
            'cm' => 'Cameroon',
            'eg' => 'Egypt',
            'gh' => 'Ghana',
            'ke' => 'Kenya',
            'ma' => 'Morocco',
            'ng' => 'Nigeria',
            'tz' => 'Tanzania',
            'tn' => 'Tunisia',
            'ug' => 'Uganda',
            'zw' => 'Zimbabwe',
            'bd' => 'Bangladesh',
            'cn' => 'China',
            'hk' => 'Hong Kong',
            'in' => 'India',
            'jp' => 'Japan',
            'kr' => 'Korea',
            'my' => 'Malaysia',
            'np' => 'Nepal',
            'ph' => 'Philippines',
            'sg' => 'Singapore',
            'tw' => 'Taiwan',
            'th' => 'Thailand',
            'vn' => 'Vietnam',
            'eu' => 'Europe',
            'at' => 'Austria',
            'be' => 'Belgium',
            'bg' => 'Bulgaria',
            'hr' => 'Croatia',
            'fi	' => 'Finland',
            'fr' => 'France',
            'de' => 'Germany',
            'gr' => 'Greece',
            'hu' => 'Hungary',
            'ie' => 'Ireland',
            'it' => 'Italy',
            'nl' => 'Netherlands',
            'no' => 'Norway',
            'pl' => 'Poland',
            'pt' => 'Portugal',
            'ro' => 'Romania',
            'ru' => 'Russian Federation',
            'rs' => 'Serbia',
            'sk' => 'Slovak Republic',
            'es' => 'Spain',
            'se' => 'Sweden',
            'ch' => 'Switzerland',
            'tr' => 'Turkey',
            'ua' => 'Ukraine',
            'gb' => 'United Kingdom',
            'ar' => 'Argentina',
            'bo' => 'Bolivia',
            'br' => 'Brazil',
            'cl' => 'Chile',
            'co' => 'Colombia',
            'cr' => 'Costa Rica',
            'do' => 'Dominican Republic',
            'ec' => 'Ecuador',
            'gt' => 'Guatemala',
            'mx' => 'Mexico',
            'pa' => 'Panama',
            'pe' => 'Peru',
            'pr' => 'Puerto Rico',
            'uy' => 'Uruguay',
            've' => 'Venezuela',
            'il' => 'Israel',
            'jo' => 'Jordan',
            'kw' => 'Kuwait',
            'pk' => 'Pakistan',
            'qa' => 'Qatar',
            'sa' => 'Saudi Arabia',
            'ca' => 'Canada',
            'au' => 'Australia',
            'nz' => 'New Zealand'
        ];
        return $response;
    }
}