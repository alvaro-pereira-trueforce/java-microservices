<?php

namespace APIServices\Zendesk\Models;


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