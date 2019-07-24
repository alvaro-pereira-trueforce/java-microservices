package com.techalvaro.stock.stockservice.controller;

import com.techalvaro.stock.stockservice.dto.InstagramAccount;
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
    public List<InstagramAccount> getAll() throws Exception {
        return instagramService.getAllAccounts();
    }

    @GetMapping("/{id}")
    public Object getById(@PathVariable("id") @NotNull final UUID id) throws Exception {
        return instagramService.getById(id);
    }

    @GetMapping("/all")
    public Object getAllPost() throws Exception {
        return instagramService.getPosts();
    }

}
