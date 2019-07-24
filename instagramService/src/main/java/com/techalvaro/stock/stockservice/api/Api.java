package com.techalvaro.stock.stockservice.api;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

@Component
public class Api {
    @Value("${api.persistence.service}")
    private String persistence;

    @Value("${api.instagram.service}")
    private String instagram;

    public String getPersistence() {
        return persistence;
    }

    public void setPersistence(String persistence) {
        this.persistence = persistence;
    }

    public String getInstagram() {
        return instagram;
    }

    public void setInstagram(String instagram) {
        this.instagram = instagram;
    }
}
