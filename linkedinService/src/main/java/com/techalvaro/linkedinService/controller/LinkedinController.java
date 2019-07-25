package com.techalvaro.linkedinService.controller;

import com.techalvaro.linkedinService.services.LinkedinService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import javax.validation.constraints.NotNull;
import java.util.UUID;

@RestController
@RequestMapping("/rest")
public class LinkedinController {
    @Autowired
    LinkedinService linkedinService;

    @GetMapping("")
    public Object getAccounts() throws Exception {
        return linkedinService.getAllAccounts();
    }

    @GetMapping("/{id}")
    public Object getById(@PathVariable("id") @NotNull final UUID id) throws Exception {
        return linkedinService.getById(id);
    }

    @GetMapping("/all")
    public Object getAllPost() throws Exception {
        return linkedinService.getPosts();
    }

}
