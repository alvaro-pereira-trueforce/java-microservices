package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.LinkedInService;
import com.techalvaro.stock.dbservice.model.Linkedin;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/res-api")
public class LinkedInController {

    private LinkedInService linkedInService;

    public LinkedInController(LinkedInService linkedInService) {
        this.linkedInService = linkedInService;
    }

    @GetMapping("/linkedin")
    public List<Linkedin> getAllAccounts() {
        return linkedInService.getAllLinkedInAccount();
    }

    @GetMapping("/linkedin/{id}")
    public Linkedin getAccountById(@PathVariable("id") final UUID uuid) {
        return linkedInService.getLinkedInAccountById(uuid);
    }

    @PostMapping("/linkedin")
    public Linkedin saveAccount(@RequestBody Linkedin link) {
        return linkedInService.saveNewLinkedInAccount(link);
    }
    @PutMapping("/linkedin")
    public Linkedin updateAccount(@RequestBody Linkedin link){
        return linkedInService.updateInstagraAccoun(link);
    }

    @DeleteMapping("/linkedin/{id}")
    public Linkedin deleteAccountById(@PathVariable("id") final UUID id) {
        return linkedInService.deleteLinkedInAccountById(id);
    }

    @DeleteMapping("/linkedin/all")
    public String resetAccounts() {
        return linkedInService.deleteAllAccounts();
    }

}
