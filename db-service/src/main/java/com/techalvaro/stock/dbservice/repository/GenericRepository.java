package com.techalvaro.stock.dbservice.repository;

import com.techalvaro.stock.dbservice.model.ModelBase;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;

@SuppressWarnings("rawtypes")
public interface GenericRepository<T extends ModelBase> extends JpaRepository<T, String>, JpaSpecificationExecutor<T> {
}
