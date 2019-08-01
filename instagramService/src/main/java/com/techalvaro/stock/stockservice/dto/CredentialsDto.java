package com.techalvaro.stock.stockservice.dto;

public class CredentialsDto {
    private String access_token;
    private String company_id;

    public CredentialsDto(String access_token, String company_id) {
        this.access_token = access_token;
        this.company_id = company_id;
    }

    public CredentialsDto() {

    }

    public String getAccess_token() {
        return access_token;
    }

    public void setAccess_token(String access_token) {
        this.access_token = access_token;
    }

    public String getCompany_id() {
        return company_id;
    }

    public void setCompany_id(String company_id) {
        this.company_id = company_id;
    }
}
