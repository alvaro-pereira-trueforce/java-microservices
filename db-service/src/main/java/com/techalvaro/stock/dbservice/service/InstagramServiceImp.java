package com.techalvaro.stock.dbservice.service;

import com.techalvaro.stock.dbservice.model.Instagram;
import com.techalvaro.stock.dbservice.repository.GenericRepository;
import com.techalvaro.stock.dbservice.repository.InstagramRepository;
import org.springframework.stereotype.Service;

@Service
public class InstagramServiceImp extends GenericServiceImp<Instagram> implements InstagramService {

    private final InstagramRepository instagramRepository;

    public InstagramServiceImp(InstagramRepository instagramRepository) {
        this.instagramRepository = instagramRepository;
    }

    @Override
    protected GenericRepository<Instagram> getRepository() {
        return instagramRepository;
    }

}
