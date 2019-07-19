package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.GenericService;
import com.techalvaro.stock.dbservice.Service.LinkedInService;
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
