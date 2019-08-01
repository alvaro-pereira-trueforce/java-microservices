package com.techalvaro.stock.stockservice.dto.customDtos;

public class MessageDto {
    private String cause;
    private String message;

    public MessageDto(String message) {
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
