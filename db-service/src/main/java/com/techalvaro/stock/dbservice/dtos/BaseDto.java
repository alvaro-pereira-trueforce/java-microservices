package com.techalvaro.stock.dbservice.dtos;

import com.fasterxml.jackson.annotation.JsonFormat;
import com.techalvaro.stock.dbservice.model.ModelBase;
import org.modelmapper.ModelMapper;

import java.util.*;
import java.util.stream.Collectors;

@SuppressWarnings("rawtypes")
public class BaseDto<E extends ModelBase> {

    private UUID uuid;
    @JsonFormat(shape = JsonFormat.Shape.STRING, pattern = "yyyy-MM-dd'T'HH:mm:ss.SSSXXX")
    private Date created_at;
    @JsonFormat(shape = JsonFormat.Shape.STRING, pattern = "yyyy-MM-dd'T'HH:mm:ss.SSSXXX")
    private Date updated_at;

    protected void beforeConversion(E element, ModelMapper mapper) {
        // Do nothing
    }

    protected void afterConversion(E element, ModelMapper mapper) {
        // Do nothing
    }

    public BaseDto toDto(E element, ModelMapper mapper) {
        beforeConversion(element, mapper);
        if (element != null) {
            mapper.map(element, this);
        }
        afterConversion(element, mapper);
        return this;
    }

    public <D extends BaseDto> List<D> toListDto(Collection<E> elements, ModelMapper mapper) {
        if (elements == null || elements.isEmpty()) {
            return Collections.emptyList();
        }
        return convert(elements, mapper);
    }

    @SuppressWarnings("unchecked")
    private <D extends BaseDto> List<D> convert(Collection<E> elements, ModelMapper mapper) {
        return (List<D>) elements.stream().map(element -> {
            try {
                return this.getClass().newInstance().toDto(element, mapper);
            } catch (InstantiationException | IllegalAccessException e) {
                return new BaseDto<>();
            }
        }).sorted(Comparator.comparing(BaseDto::getUuid)).collect(Collectors.toList());
    }

    public UUID getUuid() {
        return uuid;
    }

    public void setUuid(UUID uuid) {
        this.uuid = uuid;
    }

    public Date getCreated_at() {
        return created_at;
    }

    public void setCreated_at(Date created_at) {
        this.created_at = created_at;
    }

    public Date getUpdated_at() {
        return updated_at;
    }

    public void setUpdatedAt(Date updated_at) {
        this.updated_at = updated_at;
    }

}
