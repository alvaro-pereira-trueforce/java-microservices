package com.techalvaro.stock.dbservice.repository;

import com.techalvaro.stock.dbservice.model.Instagram;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.UUID;

@Repository
public interface InstagramRepository extends JpaRepository<Instagram, UUID> {
}
