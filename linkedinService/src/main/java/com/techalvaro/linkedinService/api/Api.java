package com.techalvaro.linkedinService.api;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

@Component
public class Api {
    @Value("${api.persistence.service}")
    private String persistence;

    @Value("${api.linkedin.service}")
    private String linkedin;

    public String getPersistence() {
        return persistence;
    }

    public void setPersistence(String persistence) {
        this.persistence = persistence;
    }

    public String getLinkedin() {
        return linkedin;
    }

    public void setLinkedin(String linkedin) {
        this.linkedin = linkedin;
    }
}
