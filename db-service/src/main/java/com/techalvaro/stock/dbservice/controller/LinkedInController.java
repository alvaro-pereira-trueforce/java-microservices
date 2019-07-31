package com.techalvaro.stock.dbservice.controller;

import com.techalvaro.stock.dbservice.dtos.LinkedinDto;
import com.techalvaro.stock.dbservice.service.GenericService;
import com.techalvaro.stock.dbservice.service.LinkedInService;
import com.techalvaro.stock.dbservice.model.Linkedin;
import org.springframework.web.bind.annotation.*;


@RestController
@RequestMapping("/api/linkedin")
public class LinkedInController extends GenericController<Linkedin, LinkedinDto> {

    private LinkedInService linkedInService;

    public LinkedInController(LinkedInService linkedInService) {
        this.linkedInService = linkedInService;
    }

    @Override
    protected GenericService getService() {
        return linkedInService;
    }
}
