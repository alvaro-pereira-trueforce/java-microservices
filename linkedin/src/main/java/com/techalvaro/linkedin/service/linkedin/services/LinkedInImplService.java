package com.techalvaro.linkedin.service.linkedin.services;

import com.techalvaro.linkedin.service.linkedin.api.LinkedInApi;
import com.techalvaro.linkedin.service.linkedin.repository.LinkedInImplRepository;
import org.springframework.stereotype.Service;

@Service
public class LinkedInImplService implements LinkedInImplRepository {

    private final LinkedInApi api;

    public LinkedInImplService(LinkedInApi api) {
        this.api = api;
    }

    public <T> T getPosts(String id) throws Exception {
        return api.getPosts(id);
    }


    public <T> T getPostsByLimit(String id, String limit) throws Exception {
        return api.getPostsByLimit(id, limit);
    }


    public <T> T getComments(String id) throws Exception {
        return api.getComments(id);
    }


    public <T> T getCommentsByLimit(String id, String limit) throws Exception {
        return api.getCommentsByLimit(id, limit);
    }


    public <T> T geReply(String id) throws Exception {
        return api.geReply(id);
    }


    public <T> T getEntities(String id) throws Exception {
        return api.getEntities(id);
    }


    public <T> T postComment(String id, Object body) throws Exception {
        return api.postComment(id, body);
    }
}
