package com.techalvaro.stock.dbservice.model;

import javax.persistence.*;
import javax.validation.constraints.Email;
import javax.validation.constraints.NotNull;

@Entity
@Table(name = "channel_settings")
public class Settings extends ModelBase {

    @NotNull
    @Column(name = "ticket_tag")
    private String[] ticket_tag;

    @NotNull
    @Column(name = "channel_uuid")
    private String channel_uuid;

    @NotNull
    @Column(name = "ticket_priority")
    private String ticket_priority;

    @NotNull
    @Column(name = "ticket_type")
    private String ticket_type;

    @NotNull
    @Email
    @Column(name = "email")
    private String email;


    public Settings() {
    }

    public Settings(@NotNull String[] ticket_tag, @NotNull String channel_uuid, @NotNull String ticket_priority, @NotNull String ticket_type, @NotNull @Email String email) {
        this.ticket_tag = ticket_tag;
        this.channel_uuid = channel_uuid;
        this.ticket_priority = ticket_priority;
        this.ticket_type = ticket_type;
        this.email = email;
    }

    public String[] getTicket_tag() {
        return ticket_tag;
    }

    public void setTicket_tag(String[] ticket_tag) {
        this.ticket_tag = ticket_tag;
    }

    public String getChannel_uuid() {
        return channel_uuid;
    }

    public void setChannel_uuid(String channel_uuid) {
        this.channel_uuid = channel_uuid;
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
}
