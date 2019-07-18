package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.InstagramService;
import com.techalvaro.stock.dbservice.model.Instagram;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/rest-api")
public class InstagramController {

    private InstagramService instagramService;

    public InstagramController(InstagramService instagramService) {
        this.instagramService = instagramService;
    }

    @GetMapping("/instagram")
    public List<Instagram> getAllAccounts() {
        return instagramService.getAllInstagramAccount();
    }


    @GetMapping("/instagram/{id}")
    public Instagram getAccountById(@PathVariable("id") final UUID uuid) {
        return instagramService.getInstagramAccountById(uuid);
    }

    @PostMapping("/instagram")
    public Instagram saveAccount(@RequestBody Instagram ins) {
        return instagramService.saveNewInstagramAccount(ins);
    }

    @DeleteMapping("/instagram/{id}")
    public String deleteById(@PathVariable("id") final UUID uuid) {
        return instagramService.deleteInstagramAccountById(uuid);
    }

    @DeleteMapping("/instagram/all")
    public String resetAccounts() {
        return instagramService.deleteAllAccounts();
    }

    @PutMapping("/instagram/update")
    public Instagram updateAccount(@RequestBody Instagram ins) {
        return instagramService.updateInstagraAccoun(ins);
    }
}
