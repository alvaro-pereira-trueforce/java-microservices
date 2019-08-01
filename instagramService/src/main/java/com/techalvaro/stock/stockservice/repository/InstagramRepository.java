package com.techalvaro.stock.stockservice.repository;

import org.springframework.stereotype.Repository;


@Repository
public interface InstagramRepository {

    <T> T getPosts(String id) throws Exception;

}
