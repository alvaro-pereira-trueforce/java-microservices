package com.techalvaro.stock.dbservice.Service;

import com.techalvaro.stock.dbservice.model.ModelBase;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;

import java.util.List;
import java.util.UUID;

@SuppressWarnings("rawtypes")
public interface GenericService<T extends ModelBase> {
    List<T> findAll();

    T findById(UUID id);

    T save(T model);

    T saveAndFlush(T model);

    T deleteById(UUID id);

    Page<T> findAll(Pageable pageable);


}
