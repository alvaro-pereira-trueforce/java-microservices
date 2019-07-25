package com.techalvaro.linkedin.service.linkedin.repository;

import org.springframework.stereotype.Repository;

@Repository
public interface LinkedInImplRepository {

    <T> T getPosts(String id) throws Exception;

    <T> T getPostsByLimit(String id, String limit) throws Exception;

    <T> T getComments(String id) throws Exception;

    <T> T getCommentsByLimit(String id, String limit) throws Exception;

    <T> T geReply(String id) throws Exception;

    <T> T getEntities(String id) throws Exception;

    <T> T postComment(String id, Object body) throws Exception;

}
