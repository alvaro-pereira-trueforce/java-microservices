package com.techalvaro.stock.dbservice.controller;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.techalvaro.stock.dbservice.dtos.BaseDto;
import com.techalvaro.stock.dbservice.dtos.customDtos.ResponseDto;
import com.techalvaro.stock.dbservice.exceptions.customExceptions.InternalErrorException;
import com.techalvaro.stock.dbservice.service.GenericService;
import com.techalvaro.stock.dbservice.utilities.PropertyAccesor;
import com.techalvaro.stock.dbservice.model.ModelBase;
import org.modelmapper.ModelMapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import javax.servlet.http.HttpServletResponse;
import javax.validation.constraints.NotNull;
import java.lang.reflect.ParameterizedType;
import java.util.Collection;
import java.util.List;
import java.util.stream.Collectors;

@SuppressWarnings({"rawtypes", "unchecked"})
public abstract class GenericController<E extends ModelBase, D extends BaseDto<E>> {

    @Autowired
    protected ModelMapper modelMapper;
    @Autowired
    protected ObjectMapper objectMapper;

    protected Logger logger = LoggerFactory.getLogger(this.getClass());


    @DeleteMapping(value = "/{id}")
    @ResponseBody
    protected ResponseDto deleteById(@PathVariable("id") @NotNull final String id) throws Exception {
        getService().deleteById(id);
        return new ResponseDto(PropertyAccesor.getInstance().getDeleteSuccessfullyMessage(), HttpStatus.ACCEPTED, 202);
    }

    @GetMapping(value = "model/{id}")
    @ResponseBody
    public E findModelById(@PathVariable("id") @NotNull final String id) {
        return (E) getService().findById(id);
    }

    @GetMapping
    @ResponseBody
    protected List<D> getAll() {
        return toDto(getService().findAll());
    }

    @GetMapping(value = "/{id}")
    @ResponseBody
    protected D getById(@PathVariable("id") @NotNull final String id) {
        return toDto((E) (getService().findById(id)));
    }

    @PostMapping
    @ResponseBody
    protected D save(@RequestBody D element) throws Exception {
        return toDto((E) getService().save(toModel(element)));
    }

    @PutMapping
    @ResponseBody
    protected D update(@RequestBody D element) throws Exception {
        return toDto((E) getService().save(toModel(element)));
    }

    private D getInstanceOfD() {
        Class<D> type = getDtoClass();
        try {
            return type.newInstance();
        } catch (Exception e) {
            throw new InternalErrorException("No default constructor.", e);
        }
    }

    private E getInstanceOfE() {
        Class<E> type = getDomainClass();
        try {
            return type.newInstance();
        } catch (Exception e) {
            throw new InternalErrorException("No default constructor.", e);
        }
    }

    private Class<E> getDomainClass() {
        ParameterizedType superClass = (ParameterizedType) getClass().getGenericSuperclass();
        return (Class<E>) superClass.getActualTypeArguments()[0];
    }

    private Class<D> getDtoClass() {
        ParameterizedType superClass = (ParameterizedType) getClass().getGenericSuperclass();
        return (Class<D>) superClass.getActualTypeArguments()[1];
    }

    protected D toDto(E entity) {
        return (D) getInstanceOfD().toDto(entity, modelMapper);
    }

    protected E toModel(D dto) {
        return (E) getInstanceOfE().toDomain(dto, modelMapper);
    }

    protected List<D> toDto(Collection<E> entities) {
        return entities.stream().map(this::toDto).collect(Collectors.toList());
    }


    protected abstract GenericService getService();

    @RequestMapping(method = RequestMethod.OPTIONS)
    public ResponseEntity preflight(HttpServletResponse response) {
        response.setHeader("Allow", "HEAD,GET,POST,PUT,DELETE,OPTIONS");
        return new ResponseEntity(HttpStatus.OK);
    }
}
