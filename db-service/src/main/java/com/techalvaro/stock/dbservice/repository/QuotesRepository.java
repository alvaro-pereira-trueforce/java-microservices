package com.techalvaro.stock.dbservice.repository;

import com.techalvaro.stock.dbservice.model.Quote;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface QuotesRepository extends JpaRepository<Quote, Long> {
    List<Quote> findByUsername(String username);
}
