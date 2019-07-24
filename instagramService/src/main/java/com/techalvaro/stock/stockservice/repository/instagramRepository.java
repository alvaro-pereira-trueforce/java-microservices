package com.techalvaro.stock.stockservice.repository;

import org.springframework.stereotype.Repository;

import java.util.UUID;


@Repository
public interface instagramRepository {

    <T> T getAllAccounts() throws Exception;

    <T> T getById(UUID id) throws Exception;

    <T> T getPosts() throws Exception;

}
