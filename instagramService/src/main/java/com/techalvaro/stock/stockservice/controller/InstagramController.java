package com.techalvaro.stock.stockservice.controller;

import com.techalvaro.stock.stockservice.model.Instagram;
import com.techalvaro.stock.stockservice.services.InstagramService;
import com.techalvaro.stock.stockservice.services.PersistenceService;
import org.springframework.web.bind.annotation.*;

import javax.validation.constraints.NotNull;
import java.util.List;

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
    public List<Instagram> getAll() throws Exception {
        return persistenceService.getAccounts();
    }

    @PostMapping
    @ResponseBody
    public Instagram save(@RequestBody Instagram body) throws Exception {
        return body;
    }

    @GetMapping("/{id}")
    @ResponseBody
    public Instagram getById(@PathVariable("id") @NotNull final String id) throws Exception {
        return persistenceService.getAccountById(id);
    }

    @GetMapping("/instagram/{id}")
    @ResponseBody
    public Object getPosts(@PathVariable("id") @NotNull final String id) throws Exception {
        return instagramService.getPosts(id);
    }
}
