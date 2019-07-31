package com.techalvaro.stock.dbservice.dtos;

import com.techalvaro.stock.dbservice.model.Linkedin;
import org.modelmapper.ModelMapper;

public class LinkedinDto extends BaseDto<Linkedin> {

    private String integration_name;
    private Long expires_in;
    private String access_token;
    private String subdomain;
    private String company_id;

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

    @Override
    public LinkedinDto toDto(Linkedin item, ModelMapper mapper) {
        super.toDto(item, mapper);
        setIntegration_name(item.getIntegration_name());
        setExpires_in(item.getExpires_in());
        setAccess_token(item.getAccess_token());
        setSubdomain(item.getSubdomain());
        setCompany_id(item.getCompany_id());
        return this;
    }
}
