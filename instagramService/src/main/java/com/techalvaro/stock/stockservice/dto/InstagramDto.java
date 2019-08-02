package com.techalvaro.stock.stockservice.dto;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.fasterxml.jackson.annotation.JsonInclude;

import java.util.Date;

@JsonInclude(JsonInclude.Include.NON_NULL)
public class InstagramDto {

    private String integration_name;
    private Long expires_in;
    private String access_token;
    private String subdomain;
    private String company_id;
    private System uuid;
    private Date created_at;
    private Date updated_at;


    public InstagramDto(String integration_name, Long expires_in, String access_token, String subdomain, String company_id, System uuid, Date created_at, Date updated_at) {
        this.integration_name = integration_name;
        this.expires_in = expires_in;
        this.access_token = access_token;
        this.subdomain = subdomain;
        this.company_id = company_id;
        this.uuid = uuid;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }

    public InstagramDto() {

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

    public System getUuid() {
        return uuid;
    }

    public void setUuid(System uuid) {
        this.uuid = uuid;
    }
    @JsonIgnore
    public Date getCreated_at() {
        return created_at;
    }

    public void setCreated_at(Date created_at) {
        this.created_at = created_at;
    }
    @JsonIgnore
    public Date getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(Date updated_at) {
        this.updated_at = updated_at;
    }
}
