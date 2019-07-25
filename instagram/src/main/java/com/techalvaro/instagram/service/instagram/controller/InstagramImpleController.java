package com.techalvaro.instagram.service.instagram.controller;

import com.techalvaro.instagram.service.instagram.services.InstagramImplService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/rest")
public class InstagramImpleController {

    @Autowired
    InstagramImplService instagramImplService;

    @GetMapping("/users/{id}")
    public Object getUser(@PathVariable("id") final String id) throws Exception {
        return instagramImplService.getUser(id);
    }

    @GetMapping("/page/{pageID}")
    public Object getPageInstagram(@PathVariable("pageID") final String pageID) throws Exception {
        return instagramImplService.getPageInstagram(pageID);
    }

    @GetMapping("/page/access/{pageID}")
    public Object getPageAccessToken(@PathVariable("pageID") final String pageID) throws Exception {
        return instagramImplService.getPageAccessToken(pageID);
    }

    @GetMapping("/posts/{pageId}")
    public Object getPosts(@PathVariable("pageId") final String pageID) throws Exception {
        return instagramImplService.getPosts(pageID);
    }

    @GetMapping("/comments/{pageID}")
    public Object getComments(@PathVariable("pageID") final String pageID) throws Exception {
        return instagramImplService.getComments(pageID);
    }

    @GetMapping("/post/comment/{pageId}/body/{body}")
    public Object postComment(@PathVariable("pageId") final String pageId, @PathVariable("body") final String body) throws Exception {
        return instagramImplService.postComment(pageId, body);
    }

    @GetMapping("/media/{id}")
    public Object getInstagramMediaByID(@PathVariable("id") final String id) throws Exception {
        return instagramImplService.getInstagramMediaByID(id);
    }

    @GetMapping("/media/comment/{id}")
    public Object getInstagramCommentByID(@PathVariable("id") final String id) throws Exception {
        return instagramImplService.getInstagramCommentByID(id);
    }

    @GetMapping("/media/reply/{id}")
    public Object getMediaWithCommentsAndReplies(@PathVariable("id") final String id) throws Exception {
        return instagramImplService.getMediaWithCommentsAndReplies(id);
    }

}
