package com.techalvaro.stock.dbservice.model;

import javax.persistence.*;
import javax.validation.constraints.Email;
import javax.validation.constraints.NotNull;

@Entity
@Table(name = "channel_settings")
public class Settings extends ModelBase {

    @NotNull
    @Column(name = "ticket_tag")
    private String ticket_tag;

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

    public String getTicket_tag() {
        return ticket_tag;
    }

    public void setTicket_tag(String ticket_tag) {
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
}
