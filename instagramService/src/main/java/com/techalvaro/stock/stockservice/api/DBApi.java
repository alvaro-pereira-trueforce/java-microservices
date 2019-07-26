package com.techalvaro.stock.stockservice.api;

import com.techalvaro.stock.stockservice.http.HttpClient;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

import java.util.UUID;

@Component
public class DBApi {
    @Value("${api.persistence.service}")
    private String persistence;

    private final HttpClient httpClient;

    public DBApi(HttpClient httpClient) {
        this.httpClient = httpClient;
    }

    public <T> T getAccounts() throws Exception {
        return (T) httpClient.makeGetRequest(persistence, Object.class);
    }

    public <T> T getById(UUID id) throws Exception {
        return (T) httpClient.makeGetRequest(persistence + "/" + id, Object.class);
    }

    public <T> T saveAccount(T element) throws Exception {
        return (T) httpClient.makePostRequest(persistence, element, Object.class);
    }

}
