<?php

namespace APIServices\Zendesk\Models\Formatters;


interface IFormatter {
    /**
     * @return array
     */
    function getTransformedMessage();
}