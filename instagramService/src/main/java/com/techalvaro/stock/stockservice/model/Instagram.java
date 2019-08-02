package com.techalvaro.stock.stockservice.model;

public class Instagram {
    private String integration_name;
    private Long expires_in;
    private String access_token;
    private String subdomain;
    private String company_id;
    private Settings settings;

    public Instagram(String integration_name, Long expires_in, String access_token, String subdomain, String company_id, Settings settings) {
        this.integration_name = integration_name;
        this.expires_in = expires_in;
        this.access_token = access_token;
        this.subdomain = subdomain;
        this.company_id = company_id;
        this.settings = settings;
    }

    public Instagram() {

    }

    public String getIntegration_name() {
        return integration_name;
    }

    public void setIntegration_name(String integration_name) {
        this.integration_name = integration_name;
    }

    public Long getExpires_in() {
        return expires_in;
    }

    public void setExpires_in(Long expires_in) {
        this.expires_in = expires_in;
    }

    public String getAccess_token() {
        return access_token;
    }

    public void setAccess_token(String access_token) {
        this.access_token = access_token;
    }

    public String getSubdomain() {
        return subdomain;
    }

    public void setSubdomain(String subdomain) {
        this.subdomain = subdomain;
    }

    public String getCompany_id() {
        return company_id;
    }

    public void setCompany_id(String company_id) {
        this.company_id = company_id;
    }

    public Settings getSettings() {
        return settings;
    }

    public void setSettings(Settings settings) {
        this.settings = settings;
    }
}
