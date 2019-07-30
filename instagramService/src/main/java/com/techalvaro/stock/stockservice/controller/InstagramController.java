package com.techalvaro.stock.stockservice.controller;

import com.techalvaro.stock.stockservice.dto.Account;
import com.techalvaro.stock.stockservice.services.InstagramService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import javax.validation.constraints.NotNull;
import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/rest")
public class InstagramController {
    @Autowired
    InstagramService instagramService;

    @GetMapping("")
    @ResponseBody
    public List<Account> getAll() throws Exception {
        return instagramService.getAccounts();
    }

    @GetMapping("/{id}")
    @ResponseBody
    public Object getById(@PathVariable("id") @NotNull final UUID id) throws Exception {
        return instagramService.getAccountById(id);
    }

    @GetMapping("/instagram/{id}")
    @ResponseBody
    public Object getPosts(@PathVariable("id") @NotNull final UUID id) throws Exception {
        return instagramService.getPosts(id);
    }

}
