package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.LinkedInService;
import com.techalvaro.stock.dbservice.model.Linkedin;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/rest-api")
public class LinkedInController {

    private LinkedInService linkedInService;

    public LinkedInController(LinkedInService linkedInService) {
        this.linkedInService = linkedInService;
    }

    @GetMapping("/linkedin")
    @ResponseBody
    public List<Linkedin> getAllAccounts() {
        return linkedInService.getAllLinkedInAccount();
    }

    @GetMapping("/linkedin/{id}")
    @ResponseBody
    public Linkedin getAccountById(@PathVariable("id") final UUID uuid) throws Exception {
        return linkedInService.getLinkedInAccountById(uuid);
    }

    @PostMapping("/linkedin")
    @ResponseBody
    public Linkedin saveAccount(@RequestBody Linkedin link) throws Exception {
        return linkedInService.saveNewLinkedInAccount(link);
    }

    @PutMapping("/linkedin")
    @ResponseBody
    public Linkedin updateAccount(@RequestBody Linkedin link) throws Exception {
        return linkedInService.updateInstagraAccoun(link);
    }

    @DeleteMapping("/linkedin/{id}")
    @ResponseBody
    public String deleteAccountById(@PathVariable("id") final UUID id) {
        return linkedInService.deleteLinkedInAccountById(id);
    }

    @DeleteMapping("/linkedin/all")
    @ResponseBody
    public String resetAccounts() {
        return linkedInService.deleteAllAccounts();
    }

}
