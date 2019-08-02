package com.techalvaro.stock.stockservice.services;

import com.techalvaro.stock.stockservice.api.DBApi;
import com.techalvaro.stock.stockservice.dto.InstagramDto;
import com.techalvaro.stock.stockservice.dto.customDtos.ResponseDto;
import com.techalvaro.stock.stockservice.exceptions.webExceptions.BadRequestException;
import com.techalvaro.stock.stockservice.exceptions.webExceptions.NotFoundException;
import com.techalvaro.stock.stockservice.model.Instagram;
import com.techalvaro.stock.stockservice.repository.PersistenceRepository;
import org.json.JSONArray;
import org.json.JSONObject;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;


@Service
public class PersistenceService extends BaseService implements PersistenceRepository {

    public PersistenceService(DBApi dbApi) {
        super(dbApi);
    }

    public List<Instagram> getAccounts() {
        try {
            List<Instagram> list = new ArrayList<>();
            List<Map<String, String>> instagramDto = dbApi.getAccounts();
            System.out.println();
            instagramDto.stream()
                    .map(x -> x.get("uuid"))
                    .map(p -> new Object());

//            for (InstagramDto instagram : instagramDto) {
//                System.out.println(instagram);
//                Instagram instagramInstance = new Instagram();
//                instagramInstance.setAccess_token(instagram.getAccess_token());
//                instagramInstance.setCompany_id(instagram.getCompany_id());
//                instagramInstance.setExpires_in(instagram.getExpires_in());
//                instagramInstance.setIntegration_name(instagram.getIntegration_name());
//                instagramInstance.setSettings(dbApi.getSettings(instagram.getCompany_id()));
//                list.add(instagramInstance);

            return list;
        } catch (Exception ex) {
            logger.error("Error deleting record", ex);
            throw new NotFoundException("No records were found");
        }

    }

    public <T> T getAccountById(String id) {
        try {
            return dbApi.getById(id);
        } catch (Exception ex) {
            logger.error("Error deleting record", ex);
            throw new NotFoundException("record was not found");
        }
    }

    public <T> T saveNewAccount(String body) {
        try {
            JSONObject obj = parseEntity(body);
            InstagramDto i = new InstagramDto();
            i.setIntegration_name(obj.getString("integration_name"));
            i.setExpires_in(obj.getLong("expires_in"));
            i.setAccess_token(obj.getString("access_token"));
            i.setSubdomain(obj.getString("subdomain"));
            i.setCompany_id(obj.getString("company_id"));
            dbApi.saveAccount(i);
            return this.syncSettingsEntity(obj.getJSONObject("SettingsDto").toString(), obj.getString("company_id"));
        } catch (Exception ex) {
            logger.error("Error deleting record", ex);
            throw new BadRequestException("data structure do not accomplish contracts");
        }
    }


    private <T> T syncSettingsEntity(String object, String uuid) throws Exception {
        JSONObject obj = parseEntity(object);
//        SettingsDto s = new SettingsDto();
//        s.setChannel_uuid(uuid);
//        s.setTicket_type(obj.getString("ticket_type"));
//        s.setTicket_priority(obj.getString("ticket_priority"));
//        s.setEmail(obj.getString("email"));
//        JSONArray tagList = (JSONArray) obj.get("ticket_tag");
//        String[] array = new String[tagList.length()];
//        for (int i = 0; i < tagList.length(); i++) {
//            array[i] = (String) tagList.get(i);
//        }
//        s.setTicket_tag(array);
//        dbApi.saveSettings(s);
        return (T) new ResponseDto("Created successfully", HttpStatus.OK, 200);
    }

    public <T> T deleteAccount(String id) {
        try {
            return null;
        } catch (Exception ex) {
            logger.error("Error deleting record", ex);
            throw new NotFoundException("record no found");
        }
    }

    public <T> T updateAccount(String id) {
        try {
            return null;
        } catch (Exception ex) {
            logger.error("Error deleting record", ex);
            throw new NotFoundException("record no found");
        }

    }
}
