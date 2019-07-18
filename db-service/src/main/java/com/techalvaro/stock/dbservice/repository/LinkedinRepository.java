package com.techalvaro.stock.dbservice.repository;

import com.techalvaro.stock.dbservice.model.Linkedin;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.UUID;

@Repository
public interface LinkedinRepository extends JpaRepository<Linkedin, UUID> {
}
