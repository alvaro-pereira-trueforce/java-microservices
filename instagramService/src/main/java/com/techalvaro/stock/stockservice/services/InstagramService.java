package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.api.InstaApi;
import com.techalvaro.stock.stockservice.repository.instagramRepository;
import com.techalvaro.stock.stockservice.utils.StringUtility;
import org.springframework.stereotype.Service;

import java.util.Map;
import java.util.UUID;


@Service
public class InstagramService implements instagramRepository {

    private final DBApi dbApi;
    private final InstaApi instaApi;

    public InstagramService(DBApi dbApi, InstaApi instaApi) {
        this.dbApi = dbApi;
        this.instaApi = instaApi;
    }

    public <T> T getAccounts() throws Exception {
        return dbApi.getAccounts();
    }


    public <T> T getAccountById(UUID id) throws Exception {
        return dbApi.getById(id);
    }

    public <T> T getPosts(UUID id) throws Exception {
        Map<T, T> dbInstance;
        dbInstance = dbApi.getById(id);
        Map<T, T> tok = (Map<T, T>) StringUtility.filterByParameter(dbInstance, "access_token");
        Map<T, T> com = (Map<T, T>) StringUtility.filterByParameter(dbInstance, "company_id");
        return (T) instaApi.getPosts(com.get("company_id").toString(), tok.get("access_token").toString());
    }

}

