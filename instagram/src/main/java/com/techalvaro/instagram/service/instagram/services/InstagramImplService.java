package com.techalvaro.instagram.service.instagram.services;

import com.techalvaro.instagram.service.instagram.api.InstagramApi;
import com.techalvaro.instagram.service.instagram.repository.InstagramImplRepository;
import org.springframework.stereotype.Service;

@Service
public class InstagramImplService implements InstagramImplRepository {

    private final InstagramApi api;

    public InstagramImplService(InstagramApi api) {
        this.api = api;
    }

    public <T> T getUser(String id, String token) throws Exception {
        return api.getUser(id, token);
    }

    public <T> T getPageInstagram(String pageID, String token) throws Exception {
        return api.getPageInstagram(pageID, token);
    }

    public <T> T getPageAccessToken(String pageID) throws Exception {
        return api.getPageAccessToken(pageID);
    }

    public <T> T getPosts(String pageID, String token) throws Exception {
        return api.getPosts(pageID, token);
    }

    public <T> T getComments(String postID, String token) throws Exception {
        return api.getComments(postID, token);
    }

    public <T> T postComment(String postID, String body, String token) throws Exception {
        return api.postComment(postID, body, token);
    }

    public <T> T getInstagramMediaByID(String mediaID, String token) throws Exception {
        return api.getInstagramMediaByID(mediaID, token);
    }

    public <T> T getInstagramCommentByID(String commentId, String token) throws Exception {
        return api.getInstagramCommentByID(commentId, token);
    }

    public <T> T getMediaWithCommentsAndReplies(String mediaId, String token) throws Exception {
        return api.getMediaWithCommentsAndReplies(mediaId, token);
    }
}
