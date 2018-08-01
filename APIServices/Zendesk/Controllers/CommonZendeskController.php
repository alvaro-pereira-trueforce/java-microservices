<?php

namespace APIServices\Zendesk\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ManifestRepository;

abstract class CommonZendeskController extends Controller implements IZendeskController
{
    protected $manifest;
    protected $service;

    public function __construct(ManifestRepository $repository)
    {
        $this->manifest = $repository;
    }
}