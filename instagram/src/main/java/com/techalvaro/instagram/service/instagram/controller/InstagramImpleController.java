package com.techalvaro.instagram.service.instagram.controller;

import com.techalvaro.instagram.service.instagram.services.InstagramImplService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/rest")
public class InstagramImpleController {

    @Autowired
    InstagramImplService instagramImplService;

    @GetMapping("/posts")
    public Object getAllPost() throws Exception {
        return instagramImplService.getPosts();
    }
}
