package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.Api;
import com.techalvaro.stock.stockservice.http.HttpClient;
import com.techalvaro.stock.stockservice.repository.instagramRepository;
import org.springframework.stereotype.Service;

import java.util.UUID;


@Service
public class InstagramService implements instagramRepository {

    private final HttpClient httpClient;
    private final Api api;

    public InstagramService(HttpClient httpClient, Api api) {
        this.httpClient = httpClient;
        this.api = api;
    }

    public <T> T getAllAccounts() throws Exception {
        return (T) httpClient.makeGetRequest(api.getPersistence(), Object.class);
    }

    public <T> T getById(UUID id) throws Exception {
        return (T) httpClient.makeGetRequest(api.getPersistence() + "/" + id, Object.class);
    }


    public <T> T getPosts() throws Exception {
        return (T) httpClient.makeGetRequest(api.getInstagram(), Object.class);
    }

}

