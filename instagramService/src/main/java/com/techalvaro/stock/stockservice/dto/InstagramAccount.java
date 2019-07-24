package com.techalvaro.stock.stockservice.dto;

import java.util.Date;
import java.util.UUID;

public class InstagramAccount {

    private UUID uuid;
    private Date created_at;
    private Date updated_at;
    private String integration_name;
    private int expires_in;
    private String access_token;

    public InstagramAccount() {
    }

    public InstagramAccount(UUID uuid, Date created_at, Date updated_at, String integration_name, int expires_in, String access_token) {
        this.uuid = uuid;
        this.created_at = created_at;
        this.updated_at = updated_at;
        this.integration_name = integration_name;
        this.expires_in = expires_in;
        this.access_token = access_token;
    }

    public UUID getUuid() {
        return uuid;
    }

    public void setUuid(UUID uuid) {
        this.uuid = uuid;
    }

    public Date getCreated_at() {
        return created_at;
    }

    public void setCreated_at(Date created_at) {
        this.created_at = created_at;
    }

    public Date getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(Date updated_at) {
        this.updated_at = updated_at;
    }

    public String getIntegration_name() {
        return integration_name;
    }

    public void setIntegration_name(String integration_name) {
        this.integration_name = integration_name;
    }

    public int getExpires_in() {
        return expires_in;
    }

    public void setExpires_in(int expires_in) {
        this.expires_in = expires_in;
    }

    public String getAccess_token() {
        return access_token;
    }

    public void setAccess_token(String access_token) {
        this.access_token = access_token;
    }
}
