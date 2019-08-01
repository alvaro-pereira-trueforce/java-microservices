package com.techalvaro.stock.dbservice.controller;

import com.techalvaro.stock.dbservice.dtos.SettingsDto;
import com.techalvaro.stock.dbservice.model.Settings;
import com.techalvaro.stock.dbservice.service.GenericService;
import com.techalvaro.stock.dbservice.service.SettingsServiceImp;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/settings")
public class SettingsController extends GenericController<Settings, SettingsDto> {
    private final SettingsServiceImp settingsServiceImp;

    public SettingsController(SettingsServiceImp settingsServiceImp) {
        this.settingsServiceImp = settingsServiceImp;
    }

    @Override
    protected GenericService getService() {
        return settingsServiceImp;
    }
}
