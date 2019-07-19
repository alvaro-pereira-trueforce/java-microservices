package com.techalvaro.stock.dbservice.ResquestModel;

public class Message {
    private String cause;
    private String message;

    public Message(String message) {
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
