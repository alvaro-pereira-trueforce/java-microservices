package com.techalvaro.instagram.service.instagram.services;

import com.techalvaro.instagram.service.instagram.api.InstagramApi;
import com.techalvaro.instagram.service.instagram.http.HttpClient;
import com.techalvaro.instagram.service.instagram.repository.ImstagramImplRepository;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.stereotype.Service;

@Service
public class InstagramImplService implements ImstagramImplRepository {
    private HttpClient httpClient;
    private InstagramApi instagramApi;

    public InstagramImplService(HttpClient httpClient, InstagramApi instagramApi) {
        this.httpClient = httpClient;
        this.instagramApi = instagramApi;
    }

    public <T> T getPosts() throws Exception {

        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_JSON);
        headers.set("Authorization", "Bearer " + instagramApi.getToken());
        httpClient.setHttpHeaders(headers);
        return (T) httpClient.makeGetRequest(instagramApi.getInstagramPosts(), Object.class);
    }

}
