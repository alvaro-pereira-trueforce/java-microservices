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
    @ResponseBody
    public List<Instagram> getAllAccounts() {
        return instagramService.getAllInstagramAccount();
    }

    @GetMapping(value = "/instagram/{id}")
    @ResponseBody
    public Instagram getAccountById(@PathVariable("id") final UUID uuid) throws Exception {
        return instagramService.getInstagramAccountById(uuid);
    }

    @PostMapping("/instagram")
    @ResponseBody
    public Instagram saveAccount(@RequestBody Instagram ins) throws Exception {
        return instagramService.saveNewInstagramAccount(ins);
    }


    @DeleteMapping("/instagram/{id}")
    @ResponseBody
    public String deleteById(@PathVariable("id") final UUID uuid) {
        return instagramService.deleteInstagramAccountById(uuid);
    }

    @DeleteMapping("/instagram/all")
    @ResponseBody
    public String resetAccounts() {
        return instagramService.deleteAllAccounts();
    }

    @PutMapping("/instagram/update")
    @ResponseBody
    public Instagram updateAccount(@RequestBody Instagram ins) throws Exception {
        return instagramService.updateInstagraAccoun(ins);
    }
}
