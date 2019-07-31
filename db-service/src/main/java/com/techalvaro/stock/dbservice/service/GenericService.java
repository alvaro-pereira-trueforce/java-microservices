package com.techalvaro.stock.dbservice.service;

import com.techalvaro.stock.dbservice.model.ModelBase;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.List;

@SuppressWarnings("rawtypes")
public interface GenericService<T extends ModelBase> {
    List<T> findAll();

    T findById(String id);

    T save(T model);

    T saveAndFlush(T model);

    T deleteById(String id);

    Page<T> findAll(Pageable pageable);


}
