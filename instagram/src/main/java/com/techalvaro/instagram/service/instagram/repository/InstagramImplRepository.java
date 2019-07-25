package com.techalvaro.instagram.service.instagram.repository;

import org.springframework.stereotype.Repository;

@Repository
public interface InstagramImplRepository {

    <T> T getUser(String id) throws Exception;

    <T> T getPageInstagram(String pageID) throws Exception;

    <T> T getPageAccessToken(String pageID) throws Exception;

    <T> T getPosts(String pageID) throws Exception;

    <T> T getComments(String postID) throws Exception;

    <T> T postComment(String postID, String body) throws Exception;

    <T> T getInstagramMediaByID(String mediaID) throws Exception;

    <T> T getInstagramCommentByID(String commentId) throws Exception;

    <T> T getMediaWithCommentsAndReplies(String mediaId) throws Exception;

}
