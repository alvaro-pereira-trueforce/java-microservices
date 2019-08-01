package com.techalvaro.stock.dbservice.model;

import javax.persistence.*;
import javax.validation.constraints.NotNull;

@Entity
@Table(name = "linkedin_integration")
public class Linkedin extends ModelBase {

    @NotNull
    @Column(name = "integration_name")
    private String integration_name;

    @NotNull
    @Column(name = "expires_in")
    private Long expires_in;

    @NotNull
    @Column(name = "access_token", length = 500)
    private String access_token;

    @NotNull
    @Column(name = "subdomain")
    private String subdomain;

    @NotNull
    @Column(name = "company_id")
    private String company_id;


    public Linkedin() {
    }

    public Linkedin(@NotNull String integration_name, @NotNull Long expires_in, @NotNull String access_token, @NotNull String subdomain, @NotNull String company_id) {
        this.integration_name = integration_name;
        this.expires_in = expires_in;
        this.access_token = access_token;
        this.subdomain = subdomain;
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
}
