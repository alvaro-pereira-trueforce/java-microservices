package com.techalvaro.instagram.service.instagram.repository;

import org.springframework.stereotype.Repository;

@Repository
public interface InstagramImplRepository {

    <T> T getUser(String id, String token) throws Exception;

    <T> T getPageInstagram(String pageID, String token) throws Exception;

    <T> T getPageAccessToken(String pageID) throws Exception;

    <T> T getPosts(String pageID, String token) throws Exception;

    <T> T getComments(String postID, String token) throws Exception;

    <T> T postComment(String postID, String body, String token) throws Exception;

    <T> T getInstagramMediaByID(String mediaID, String token) throws Exception;

    <T> T getInstagramCommentByID(String commentId, String token) throws Exception;

    <T> T getMediaWithCommentsAndReplies(String mediaId, String token) throws Exception;

}
