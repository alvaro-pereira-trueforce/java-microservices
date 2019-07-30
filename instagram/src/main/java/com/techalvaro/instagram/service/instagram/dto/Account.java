package com.techalvaro.instagram.service.instagram.dto;

public class Account {
    private String company_id;
    private String access_token;

    public Account() {
    }

    public Account(String company_id, String access_token) {
        this.company_id = company_id;
        this.access_token = access_token;
    }

    public String getCompany_id() {
        return company_id;
    }

    public void setCompany_id(String company_id) {
        this.company_id = company_id;
    }

    public String getAccess_token() {
        return access_token;
    }

    public void setAccess_token(String access_token) {
        this.access_token = access_token;
    }
}
