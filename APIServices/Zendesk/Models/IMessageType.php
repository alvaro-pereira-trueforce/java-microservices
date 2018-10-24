<?php

namespace APIServices\Zendesk\Models;

/**
 * This is an interface to handle the process of transform the data whether they are
 * Comments, Images or Videos.
 * Interface IMessageType
 * @package APIServices\Zendesk\Models
 */
interface IMessageType {
    /**
     * @return array
     */
    function getTransformedMessage();

    /**
     * @return string
     */
    function getAuthorExternalID();

    /**
     * @return string
     */
    function getExternalID();

    /**
     * @return string
     */
    function getAuthorName();

    /**
     * @return string
     */
    function getParentID();

    /**
     * @return array
     */
    function getBasicResponse();
}