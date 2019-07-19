package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.ResquestModel.CustomizeMessage;
import com.techalvaro.stock.dbservice.Service.GenericService;
import com.techalvaro.stock.dbservice.Utilities.PropertyAccesor;
import com.techalvaro.stock.dbservice.model.ModelBase;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;

import javax.validation.constraints.NotNull;
import java.util.List;
import java.util.UUID;


@SuppressWarnings({"rawtypes", "unchecked"})
public abstract class GenericController<E extends ModelBase> {

    @DeleteMapping(value = "/{id}")
    @ResponseBody
    protected CustomizeMessage deleteById(@PathVariable("id") @NotNull UUID id) {
        getService().deleteById(id);
        return new CustomizeMessage(PropertyAccesor.getInstance().getDeleteSuccessfullyMessage(), HttpStatus.ACCEPTED, 202);
    }

    @GetMapping(value = "/{id}")
    @ResponseBody
    public E findModelById(@PathVariable("id") @NotNull UUID id) {
        return (E) getService().findById(id);
    }

    @GetMapping(value = "")
    @ResponseBody
    protected List<E> getAll() {
        return getService().findAll();
    }

    @PostMapping("")
    @ResponseBody
    protected E save(@RequestBody E element) {
        return (E) getService().save(element);
    }

    @PutMapping("")
    @ResponseBody
    protected E update(@RequestBody E element) {
        return (E) getService().save(element);
    }

    protected abstract GenericService getService();
}
