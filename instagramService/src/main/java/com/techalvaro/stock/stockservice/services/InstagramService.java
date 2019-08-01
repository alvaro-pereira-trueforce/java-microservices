package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.api.InstaApi;
import com.techalvaro.stock.stockservice.repository.InstagramRepository;
import org.springframework.stereotype.Service;

@Service
public class InstagramService extends BaseService implements InstagramRepository {

    private final InstaApi instaApi;

    public InstagramService(DBApi dbApi, InstaApi instaApi) {
        super(dbApi);
        this.instaApi = instaApi;
    }

    public <T> T getPosts(String id) throws Exception {
        return instaApi.getPosts(this.getIngramCredentials(id));
    }

}

