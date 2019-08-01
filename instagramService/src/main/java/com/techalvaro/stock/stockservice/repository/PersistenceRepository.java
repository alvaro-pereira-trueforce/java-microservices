package com.techalvaro.stock.stockservice.repository;

import org.springframework.stereotype.Repository;

@Repository
public interface PersistenceRepository {

    <T> T getAccounts() throws Exception;

    <T> T getAccountById(String id) throws Exception;

    <T> T saveNewAccount(String id) throws Exception;

    <T> T deleteAccount(String id) throws Exception;

    <T> T updateAccount(String id) throws Exception;
}
