package com.techalvaro.stock.dbservice.controller;

import com.techalvaro.stock.dbservice.service.GenericService;
import com.techalvaro.stock.dbservice.service.LinkedInService;
import com.techalvaro.stock.dbservice.model.Linkedin;
import org.springframework.web.bind.annotation.*;


@RestController
@RequestMapping("/rest-api/linkedin")
public class LinkedInController extends GenericController<Linkedin> {

    private LinkedInService linkedInService;

    public LinkedInController(LinkedInService linkedInService) {
        this.linkedInService = linkedInService;
    }

    @Override
    protected GenericService getService() {
        return linkedInService;
    }
}
