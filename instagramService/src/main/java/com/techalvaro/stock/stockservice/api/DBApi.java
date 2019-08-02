package com.techalvaro.stock.stockservice.api;

import com.techalvaro.stock.stockservice.http.HttpClient;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

@Component
public class DBApi {
    @Value("${api.persistence.instagram}")
    private String accountPersistence;

    @Value("${api.persistence.settings}")
    private String settingPersistence;

    private final HttpClient httpClient;

    public DBApi(HttpClient httpClient) {
        this.httpClient = httpClient;
    }

    public <T> T getAccounts() throws Exception {
        return (T) httpClient.makeGetRequest(accountPersistence, Object.class);
    }

    public <T> T getSettings(String id) throws Exception {
        return (T) httpClient.makeGetRequest(settingPersistence+ "/company/" + id, Object.class);
    }

    public <T> T getById(String id)  {
        return (T) httpClient.makeGetRequest(accountPersistence + "/" + id, Object.class);
    }

    public <T> T saveAccount(T body) throws Exception {
        return (T) httpClient.makePostRequest(accountPersistence, body, Object.class);
    }

    public <T> T saveSettings(T body) throws Exception {
        return (T) httpClient.makePostRequest(settingPersistence, body, Object.class);
    }

}
