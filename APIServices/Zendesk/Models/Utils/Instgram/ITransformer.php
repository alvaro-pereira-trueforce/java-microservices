<?php
namespace APIServices\Zendesk\Models\Utils\Instagram;


interface ITransformer {
    /**
     * @return array
     */
   function generateToTransformedMessage();
}