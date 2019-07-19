package com.techalvaro.stock.dbservice.Service;

import com.techalvaro.stock.dbservice.model.Linkedin;
import com.techalvaro.stock.dbservice.repository.GenericRepository;
import com.techalvaro.stock.dbservice.repository.LinkedinRepository;
import org.springframework.stereotype.Service;


@Service
public class LinkedInServiceImp extends GenericServiceImp<Linkedin> implements LinkedInService {
    private LinkedinRepository linkedinRepository;

    public LinkedInServiceImp(LinkedinRepository linkedinRepository) {
        this.linkedinRepository = linkedinRepository;
    }

    @Override
    protected GenericRepository<Linkedin> getRepository() {
        return linkedinRepository;
    }

}
