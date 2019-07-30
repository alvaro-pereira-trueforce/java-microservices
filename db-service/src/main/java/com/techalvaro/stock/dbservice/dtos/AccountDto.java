package com.techalvaro.stock.dbservice.dtos;

import com.techalvaro.stock.dbservice.model.Instagram;

public class AccountDto extends BaseDto<Instagram> {
    private String access_token;
    private String company_id;

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
