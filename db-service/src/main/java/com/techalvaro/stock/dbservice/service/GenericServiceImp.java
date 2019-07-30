package com.techalvaro.stock.dbservice.service;

import com.techalvaro.stock.dbservice.exceptions.webExceptions.NotFoundException;
import com.techalvaro.stock.dbservice.utilities.PropertyAccesor;
import com.techalvaro.stock.dbservice.model.ModelBase;
import com.techalvaro.stock.dbservice.repository.GenericRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

@Service
@SuppressWarnings("rawtypes")
public abstract class GenericServiceImp<T extends ModelBase> implements GenericService<T> {

    protected Logger logger = LoggerFactory.getLogger(this.getClass());

    @Override
    public List<T> findAll() {
        return new ArrayList<>(getRepository().findAll());
    }

    @Override
    public T findById(UUID id) {
        final Optional<T> optional = getRepository().findById(id);
        if (!optional.isPresent()) {
            throw new NotFoundException(PropertyAccesor.getInstance().getNotFoundExceptionMessage());
        } else {
            return optional.get();
        }
    }

    @Override
    public T save(T model) {
        T t = getRepository().save(model);
        return findById(t.getUuid());
    }

    @Override
    public T saveAndFlush(T model) {
        T t = getRepository().saveAndFlush(model);
        return findById(t.getUuid());
    }

    @Override
    public T deleteById(UUID id) {
        try {
            getRepository().deleteById(id);
        } catch (Exception ex) {
            logger.error("Error reading file", ex);
            throw new NotFoundException(PropertyAccesor.getInstance().getNotFoundExceptionMessage());
        }
        return null;
    }


    @Override
    public Page<T> findAll(Pageable pageable) {
        return getRepository().findAll(pageable);
    }

    protected abstract GenericRepository<T> getRepository();
}
