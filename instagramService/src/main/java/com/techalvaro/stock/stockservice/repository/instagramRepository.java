package com.techalvaro.stock.stockservice.repository;

import org.springframework.stereotype.Repository;

import java.util.UUID;


@Repository
public interface instagramRepository {

    <T> T getAccounts() throws Exception;

    <T> T getAccountById(UUID id) throws Exception;

    <T> T getPosts(UUID id) throws Exception;

}
