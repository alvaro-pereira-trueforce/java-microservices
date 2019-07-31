package com.techalvaro.stock.dbservice.service;

import com.techalvaro.stock.dbservice.model.Settings;
import com.techalvaro.stock.dbservice.repository.GenericRepository;
import com.techalvaro.stock.dbservice.repository.SettingsRepository;
import org.springframework.stereotype.Service;

@Service
public class SettingsServiceImp extends GenericServiceImp<Settings> implements SettingsService {

    private SettingsRepository settingsRepository;

    public SettingsServiceImp(SettingsRepository settingsRepository) {
        this.settingsRepository = settingsRepository;
    }

    @Override
    protected GenericRepository<Settings> getRepository() {
        return settingsRepository;
    }
}