package com.techalvaro.stock.dbservice.ExceptionHandler.Models;

public class Message {
    private String cause;
    private String message;

    public Message(String cause, String message) {
        this.cause = cause;
        this.message = message;
    }

    public String getCause() {
        return cause;
    }

    public void setCause(String cause) {
        this.cause = cause;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }
}
