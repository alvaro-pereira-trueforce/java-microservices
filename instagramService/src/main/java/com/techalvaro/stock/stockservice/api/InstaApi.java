package com.techalvaro.stock.stockservice.api;

import com.techalvaro.stock.stockservice.dto.InstagramDto;
import com.techalvaro.stock.stockservice.http.HttpClient;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

@Component
public class InstaApi {

    @Value("${api.instagram.service}")
    private String instagram;

    private final HttpClient httpClient;

    public InstaApi(HttpClient httpClient) {
        this.httpClient = httpClient;
    }

    public <T> T getPosts(InstagramDto body) throws Exception {
        String URL = instagram;
        return (T) httpClient.makePostRequest(URL, body, Object.class);
    }
}