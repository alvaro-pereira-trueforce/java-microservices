package com.techalvaro.linkedin.service.linkedin.controller;

import com.techalvaro.linkedin.service.linkedin.services.LinkedInImplService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/rest")
public class LinkedInImplController {

    @Autowired
    LinkedInImplService linkedInImplService;

    @GetMapping("/{id}")
    public Object getAllPost(@PathVariable("id") final String id) throws Exception {
        return linkedInImplService.getPosts(id);
    }

    @GetMapping("/{id}/limit/{limit}")
    public Object getByLimit(@PathVariable("id") final String id, @PathVariable("limit") final String limit) throws Exception {
        return linkedInImplService.getPostsByLimit(id, limit);
    }

    @GetMapping("/comments/{id}")
    public Object getComments(@PathVariable("id") final String id) throws Exception {
        return linkedInImplService.getComments(id);
    }

    @GetMapping("/comments/{id}/limit/{limit}")
    public Object getCommentsByLimit(@PathVariable("id") final String id, @PathVariable("limit") final String limit) throws Exception {
        return linkedInImplService.getCommentsByLimit(id, limit);
    }

    @GetMapping("/reply/{id}")
    public Object getReply(@PathVariable("id") final String id) throws Exception {
        return linkedInImplService.geReply(id);
    }

    @GetMapping("/entities/{id}")
    public Object getEntities(@PathVariable("id") final String id) throws Exception {
        return linkedInImplService.getEntities(id);
    }

    @PostMapping("/{id}")
    public Object postComment(@RequestBody Object body, @PathVariable("id") final String id) throws Exception {
        return linkedInImplService.postComment(id, body);
    }

}
