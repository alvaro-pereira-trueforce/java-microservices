package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.api.InstaApi;
import com.techalvaro.stock.stockservice.repository.instagramRepository;
import org.springframework.stereotype.Service;

import java.util.UUID;


@Service
public class InstagramService extends BaseService implements instagramRepository {

    private final InstaApi instaApi;

    public InstagramService(DBApi dbApi, InstaApi instaApi) {
        super(dbApi);
        this.instaApi = instaApi;
    }

    public <T> T getAccounts() throws Exception {
        return dbApi.getAccounts();
    }


    public <T> T getAccountById(UUID id) throws Exception {
        return dbApi.getById(id);
    }

    public <T> T getPosts(UUID id) throws Exception {
        return instaApi.getPosts(this.getInstagramCredencials(id));
    }

}

