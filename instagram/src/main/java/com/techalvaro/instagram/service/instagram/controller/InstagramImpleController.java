package com.techalvaro.instagram.service.instagram.controller;

import com.techalvaro.instagram.service.instagram.dto.Account;
import com.techalvaro.instagram.service.instagram.services.InstagramImplService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/rest")
public class InstagramImpleController {

    @Autowired
    InstagramImplService instagramImplService;

    @PostMapping(value = "/users")
    @ResponseBody
    public Object getUser(@RequestBody final Account a) throws Exception {
        return instagramImplService.getUser(a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/page")
    @ResponseBody
    public Object getPageInstagram(@RequestBody final Account a) throws Exception {
        return instagramImplService.getPageInstagram(a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/token")
    @ResponseBody
    public Object getPageAccessToken(@RequestBody final Account a) throws Exception {
        return instagramImplService.getPageAccessToken(a.getCompany_id());
    }

    @PostMapping(value = "/posts")
    @ResponseBody
    public Object getPosts(@RequestBody final Account a) throws Exception {
        return instagramImplService.getPosts(a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/comments")
    @ResponseBody
    public Object getComments(@RequestBody final Account a) throws Exception {
        return instagramImplService.getComments(a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/create/{pageId}")
    @ResponseBody
    public Object postComment(@RequestBody final Account a, @PathVariable("pageId") final String pageId) throws Exception {
        return instagramImplService.postComment(pageId, a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/media")
    @ResponseBody
    public Object getInstagramMediaByID(@RequestBody final Account a) throws Exception {
        return instagramImplService.getInstagramMediaByID(a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/media/comments")
    @ResponseBody
    public Object getInstagramCommentByID(@RequestBody final Account a) throws Exception {
        return instagramImplService.getInstagramCommentByID(a.getCompany_id(), a.getAccess_token());
    }

    @PostMapping(value = "/media/reply")
    @ResponseBody
    public Object getMediaWithCommentsAndReplies(@RequestBody final Account a) throws Exception {
        return instagramImplService.getMediaWithCommentsAndReplies(a.getCompany_id(), a.getAccess_token());
    }

}
