package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.GenericService;
import com.techalvaro.stock.dbservice.Service.InstagramService;
import com.techalvaro.stock.dbservice.model.Instagram;
import org.springframework.web.bind.annotation.*;


@RestController
@RequestMapping("/rest-api/instagram")
public class InstagramController extends GenericController<Instagram> {

    private InstagramService instagramService;

    public InstagramController(InstagramService instagramService) {
        this.instagramService = instagramService;
    }

    @Override
    protected GenericService getService() {
        return instagramService;
    }
}
