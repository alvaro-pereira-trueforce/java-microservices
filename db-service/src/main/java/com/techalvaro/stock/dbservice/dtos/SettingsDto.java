package com.techalvaro.stock.dbservice.dtos;

import com.techalvaro.stock.dbservice.model.Settings;

public class SettingsDto extends BaseDto<Settings> {

    private String channel_uuid;
    private String tag;
    private String priority;
    private String ticket_type;
    private String email;

    public String getChannel_uuid() {
        return channel_uuid;
    }

    public void setChannel_uuid(String channel_uuid) {
        this.channel_uuid = channel_uuid;
    }

    public String getTag() {
        return tag;
    }

    public void setTag(String tag) {
        this.tag = tag;
    }

    public String getPriority() {
        return priority;
    }

    public void setPriority(String priority) {
        this.priority = priority;
    }

    public String getTicket_type() {
        return ticket_type;
    }

    public void setTicket_type(String ticket_type) {
        this.ticket_type = ticket_type;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

}
