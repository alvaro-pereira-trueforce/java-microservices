package com.techalvaro.stock.dbservice.dtos;

import com.techalvaro.stock.dbservice.model.Settings;
import org.modelmapper.ModelMapper;

public class SettingsDto extends BaseDto<Settings> {

    private String channel_uuid;
    private String[] ticket_tag;
    private String ticket_priority;
    private String ticket_type;
    private String email;

    public String getChannel_uuid() {
        return channel_uuid;
    }

    public void setChannel_uuid(String channel_uuid) {
        this.channel_uuid = channel_uuid;
    }

    public String[] getTicket_tag() {
        return ticket_tag;
    }

    public void setTicket_tag(String[] ticket_tag) {
        this.ticket_tag = ticket_tag;
    }

    public String getTicket_priority() {
        return ticket_priority;
    }

    public void setTicket_priority(String ticket_priority) {
        this.ticket_priority = ticket_priority;
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

    @Override
    public SettingsDto toDto(Settings item, ModelMapper mapper) {
        super.toDto(item, mapper);
        setChannel_uuid(item.getChannel_uuid());
        setTicket_tag(item.getTicket_tag());
        setTicket_priority(item.getTicket_priority());
        setTicket_type(item.getTicket_type());
        setEmail(item.getEmail());
        return this;
    }
}
