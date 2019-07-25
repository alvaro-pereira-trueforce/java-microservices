package com.techalvaro.linkedinService.services;

import com.techalvaro.linkedinService.api.Api;
import com.techalvaro.linkedinService.http.HttpClient;
import com.techalvaro.linkedinService.repository.LinkedinRepository;
import org.springframework.stereotype.Service;

import java.util.UUID;

@Service
public class LinkedinService implements LinkedinRepository {

    private final HttpClient httpClient;
    private final Api api;

    public LinkedinService(HttpClient httpClient, Api api) {
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
        return (T) httpClient.makeGetRequest(api.getLinkedin(), Object.class);
    }
}
