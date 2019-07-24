package com.techalvaro.instagram.service.instagram.api;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

@Component
public class InstagramApi {
    @Value("${api.posts}")
    private String instagramPosts;

    @Value("${api.token}")
    private String token;

    public String getInstagramPosts() {
        return instagramPosts;
    }

    public void setInstagramPosts(String instagramPosts) {
        this.instagramPosts = instagramPosts;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }
}
