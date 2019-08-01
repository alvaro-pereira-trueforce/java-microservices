package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.dto.CredentialsDto;
import com.techalvaro.stock.stockservice.utils.StringUtility;
import org.json.JSONObject;

import java.util.Map;

abstract class BaseService {

    protected DBApi dbApi;

    public BaseService(DBApi dbApi) {
        this.dbApi = dbApi;
    }

    protected <T> T getIngramCredentials(String id) throws Exception {
        CredentialsDto a = new CredentialsDto();
        Map<T, T> dbInstance;
        dbInstance = dbApi.getById(id);
        a.setAccess_token((String) StringUtility.filterByParameter(dbInstance, "access_token"));
        a.setCompany_id((String) StringUtility.filterByParameter(dbInstance, "company_id"));
        return (T) a;
    }

    protected JSONObject parseEntity(String s) {
        return new JSONObject(s);
    }
}
