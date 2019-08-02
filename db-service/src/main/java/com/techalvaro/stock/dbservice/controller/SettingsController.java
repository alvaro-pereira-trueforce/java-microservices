package com.techalvaro.stock.dbservice.controller;

import com.techalvaro.stock.dbservice.dtos.SettingsDto;
import com.techalvaro.stock.dbservice.model.Settings;
import com.techalvaro.stock.dbservice.service.GenericService;
import com.techalvaro.stock.dbservice.service.SettingsServiceImp;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.stream.Collectors;

@RestController
@RequestMapping("/api/settings")
public class SettingsController extends GenericController<Settings, SettingsDto> {
    private final SettingsServiceImp settingsServiceImp;

    public SettingsController(SettingsServiceImp settingsServiceImp) {
        this.settingsServiceImp = settingsServiceImp;
    }

    @GetMapping(value = "/company/{id}")
    @ResponseBody
    protected Settings getByComId(@PathVariable("id") final String id) {
        List<Settings> list = settingsServiceImp.findAll();
        return list.stream()
                .filter(x -> x.getChannel_uuid().equalsIgnoreCase(id))
                .collect(Collectors.toList()).get(0);

    }

    @Override
    protected GenericService getService() {
        return settingsServiceImp;
    }
}
