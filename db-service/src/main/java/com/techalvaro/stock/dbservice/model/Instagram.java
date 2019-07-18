package com.techalvaro.stock.dbservice.model;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Table;

@Entity
@Table(name = "intagram_integration")
public class Instagram extends ModelBase {

    @Column(name = "integration_name")
    private String integration_name;

    @Column(name = "expired_in")
    private int expires_in;

    @Column(name = "access_token")
    private String access_token;

    public Instagram() {
    }

    public Instagram(String integration_name, int expires_in, String access_token) {
        this.integration_name = integration_name;
        this.expires_in = expires_in;
        this.access_token = access_token;
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
