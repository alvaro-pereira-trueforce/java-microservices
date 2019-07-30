package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.dto.Account;
import com.techalvaro.stock.stockservice.utils.StringUtility;

import java.util.Map;
import java.util.UUID;

abstract class BaseService {

    protected DBApi dbApi;

    public BaseService(DBApi dbApi) {
        this.dbApi = dbApi;
    }

    protected  <T> T getInstagramCredencials(UUID id) throws Exception {
        Account a = new Account();
        Map<T, T> dbInstance;
        dbInstance = dbApi.getById(id);
        a.setAccess_token((String) StringUtility.filterByParameter(dbInstance, "access_token"));
        a.setCompany_id((String) StringUtility.filterByParameter(dbInstance, "company_id"));
        return (T) a;
    }

}
