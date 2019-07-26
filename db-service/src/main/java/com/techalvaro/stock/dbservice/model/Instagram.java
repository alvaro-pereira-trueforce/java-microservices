package com.techalvaro.stock.dbservice.model;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Table;

@Entity
@Table(name = "instagram_integration")
public class Instagram extends ModelBase {

    @Column(name = "integration_name")
    private String integration_name;

    @Column(name = "expires_in")
    private Long expires_in;

    @Column(name = "access_token")
    private String access_token;

    @Column(name = "company_id")
    private String company_id;

    public Instagram() {
    }

    public Instagram(String integration_name, Long expires_in, String access_token, String company_id) {
        this.integration_name = integration_name;
        this.expires_in = expires_in;
        this.access_token = access_token;
        this.company_id = company_id;
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

    public String getCompany_id() {
        return company_id;
    }

    public void setCompany_id(String company_id) {
        this.company_id = company_id;
    }
}
