package com.techalvaro.instagram.service.instagram.repository;

import org.springframework.stereotype.Repository;

@Repository
public interface ImstagramImplRepository {

    <T> T getPosts() throws Exception;
}
