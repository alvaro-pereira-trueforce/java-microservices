package com.techalvaro.stock.stockservice.controller;

import com.techalvaro.stock.stockservice.services.InstagramService;
import com.techalvaro.stock.stockservice.services.PersistenceService;
import org.springframework.web.bind.annotation.*;

import javax.validation.constraints.NotNull;
import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/api")
public class InstagramController {
    private final InstagramService instagramService;
    private final PersistenceService persistenceService;

    public InstagramController(InstagramService instagramService, PersistenceService persistenceService) {
        this.instagramService = instagramService;
        this.persistenceService = persistenceService;
    }

    @GetMapping
    @ResponseBody
    public List<Object> getAll() throws Exception {
        return persistenceService.getAccounts();
    }

    @PostMapping
    @ResponseBody
    public Object save(@RequestBody String body) throws Exception {
        return persistenceService.saveNewAccount(body);
    }

    @GetMapping("/{id}")
    @ResponseBody
    public Object getById(@PathVariable("id") @NotNull final String id) throws Exception {
        return persistenceService.getAccountById(id);
    }

    @GetMapping("/instagram/{id}")
    @ResponseBody
    public Object getPosts(@PathVariable("id") @NotNull final String id) throws Exception {
        return instagramService.getPosts(id);
    }

}
