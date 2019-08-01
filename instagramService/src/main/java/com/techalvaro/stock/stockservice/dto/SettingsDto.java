package com.techalvaro.stock.stockservice.dto;

public class SettingsDto {
    private String channel_uuid;
    private String email;
    private String ticket_priority;
    private String[] ticket_tag;
    private String ticket_type;

    public SettingsDto(String channel_uuid, String email, String ticket_priority, String[] ticket_tag, String ticket_type) {
        this.channel_uuid = channel_uuid;
        this.email = email;
        this.ticket_priority = ticket_priority;
        this.ticket_tag = ticket_tag;
        this.ticket_type = ticket_type;
    }

    public SettingsDto() {

    }

    public String getChannel_uuid() {
        return channel_uuid;
    }

    public void setChannel_uuid(String channel_uuid) {
        this.channel_uuid = channel_uuid;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTicket_priority() {
        return ticket_priority;
    }

    public void setTicket_priority(String ticket_priority) {
        this.ticket_priority = ticket_priority;
    }

    public String[] getTicket_tag() {
        return ticket_tag;
    }

    public void setTicket_tag(String[] ticket_tag) {
        this.ticket_tag = ticket_tag;
    }

    public String getTicket_type() {
        return ticket_type;
    }

    public void setTicket_type(String ticket_type) {
        this.ticket_type = ticket_type;
    }
}
