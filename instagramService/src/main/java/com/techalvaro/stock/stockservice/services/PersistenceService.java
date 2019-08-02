package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.dto.InstagramDto;
import com.techalvaro.stock.stockservice.dto.SettingsDto;
import com.techalvaro.stock.stockservice.dto.customDtos.ResponseDto;
import com.techalvaro.stock.stockservice.repository.PersistenceRepository;
import org.json.JSONArray;
import org.json.JSONObject;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;


@Service
public class PersistenceService extends BaseService implements PersistenceRepository {

    public PersistenceService(DBApi dbApi) {
        super(dbApi);
    }

    public <T> T getAccounts() throws Exception {
        return dbApi.getAccounts();
    }

    public <T> T getAccountById(String id) throws Exception {
        return dbApi.getById(id);
    }

    public <T> T saveNewAccount(String body) throws Exception {
        JSONObject obj = parseEntity(body);
        InstagramDto i = new InstagramDto();
        i.setIntegration_name(obj.getString("integration_name"));
        i.setExpires_in(obj.getLong("expires_in"));
        i.setAccess_token(obj.getString("access_token"));
        i.setSubdomain(obj.getString("subdomain"));
        i.setCompany_id(obj.getString("company_id"));
        dbApi.saveAccount(i);
        return this.syncSettingsEntity(obj.getJSONObject("Settings").toString(), obj.getString("company_id"));
    }

    private <T> T syncSettingsEntity(String object, String uuid) throws Exception {
        JSONObject obj = parseEntity(object);
        SettingsDto s = new SettingsDto();
        s.setChannel_uuid(uuid);
        s.setTicket_type(obj.getString("ticket_type"));
        s.setTicket_priority(obj.getString("ticket_priority"));
        s.setEmail(obj.getString("email"));
        JSONArray tagList = (JSONArray) obj.get("ticket_tag");
        String[] array = new String[tagList.length()];
        for (int i = 0; i < tagList.length(); i++) {
            array[i] = (String) tagList.get(i);
        }
        s.setTicket_tag(array);
        dbApi.saveSettings(s);
        return (T) new ResponseDto("Created successfully", HttpStatus.OK, 200);
    }

    public <T> T deleteAccount(String id) throws Exception {
        return null;
    }

    public <T> T updateAccount(String id) throws Exception {
        return null;
    }
}
