package com.techalvaro.stock.dbservice.dtos.customDtos;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.fasterxml.jackson.annotation.JsonInclude;
import com.fasterxml.jackson.annotation.JsonProperty;
import org.springframework.http.HttpStatus;

import java.util.Date;
import java.util.List;

@JsonInclude(JsonInclude.Include.NON_NULL)
public class ResponseDto {
    private List<MessageDto> errors;
    private String message;
    private HttpStatus status;
    private Date timestamp;
    private Integer code;


    public ResponseDto(String message, HttpStatus status, Integer code) {
        this.message = message;
        this.status = status;
        this.code = code;
    }

    public ResponseDto(List<MessageDto> errors, String message, HttpStatus status) {
        this.errors = errors;
        this.message = message;
        this.status = status;
    }

    public List<MessageDto> getErrors() {
        return errors;
    }

    public void setErrors(List<MessageDto> errors) {
        this.errors = errors;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    @JsonIgnore
    public HttpStatus getStatus() {
        return status;
    }

    @JsonProperty("status")
    public String statuAsString() {
        return status.getReasonPhrase();
    }

    public void setStatus(HttpStatus status) {
        this.status = status;
    }

    @JsonIgnore
    public Date getTimestamp() {
        return timestamp == null ? new Date() : timestamp;
    }

    @JsonProperty("timestamp")
    public long getTimestampAsInt() {
        return timestamp == null ? new Date().getTime() : timestamp.getTime();
    }

    public void setTimestamp(Date timestamp) {
        this.timestamp = timestamp;
    }

    public Integer getCode() {
        return code;
    }

    public void setCode(Integer code) {
        this.code = code;
    }
}
