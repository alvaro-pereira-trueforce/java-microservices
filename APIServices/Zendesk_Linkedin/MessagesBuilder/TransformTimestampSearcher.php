<?php


namespace APIServices\Zendesk_Linkedin\MessagesBuilder;

use APIServices\LinkedIn\Services\LinkedinService;
use Illuminate\Support\Facades\Log;

/**
 * Class TransformTimestampSearcher
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class TransformTimestampSearcher
{
    /**
     * @var $linkedinService
     */
    protected $linkedinService;

    /**
     * @var $params
     */
    protected $params;

    /**
     * @var array $comment
     */
    protected $comment;

    /**
     * @var $treads
     */
    protected $treads;

    /**
     * TransformTimestampSearcher constructor.
     * @param $params
     * @param $treads
     * @param LinkedinService $linkedinService
     * @throws \Exception
     */
    public function __construct($params, $treads, LinkedinService $linkedinService)
    {
        $this->linkedinService = $linkedinService;
        $this->params = $params;
        $this->treads = $treads;
        $this->comment = $this->linkedinService->getPostLinkedIn($params, $treads);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function searchTimestampByIdComment()
    {
        try {
            $timePost = $this->comment['updateContent']['companyStatusUpdate']['share']['timestamp'] / 1000;
            $response = gmdate('Y-m-d\TH:i:s\Z', $timePost);
            return $response;
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'was not able to track this message');
            throw $exception;
        }
    }

}