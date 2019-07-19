package com.techalvaro.stock.dbservice.ExceptionHandler.Models;

import com.fasterxml.jackson.annotation.JsonIgnore;
import com.fasterxml.jackson.annotation.JsonInclude;
import com.fasterxml.jackson.annotation.JsonProperty;
import org.springframework.http.HttpStatus;

import java.util.Date;
import java.util.List;

@JsonInclude(JsonInclude.Include.NON_NULL)
public class ErrorMessage {
    private List<Message> errors;
    private String message;
    private HttpStatus status;
    private Date timestamp;

    public ErrorMessage(String message, HttpStatus status) {
        this.message = message;
        this.status = status;
    }

    public ErrorMessage(List<Message> errors, String message, HttpStatus status) {
        this.errors = errors;
        this.message = message;
        this.status = status;
    }

    public List<Message> getErrors() {
        return errors;
    }

    public void setErrors(List<Message> errors) {
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
    public String statuAsString(){
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
    public long getTimestampAsInt(){
        return timestamp == null ? new Date().getTime() : timestamp.getTime();
    }

    public void setTimestamp(Date timestamp) {
        this.timestamp = timestamp;
    }
}
