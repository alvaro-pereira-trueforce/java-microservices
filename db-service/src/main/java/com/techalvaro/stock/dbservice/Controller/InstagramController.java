package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.InstagramService;
import com.techalvaro.stock.dbservice.model.Instagram;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
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
    public ResponseEntity<List<Instagram>> getAllAccounts() {

        List<Instagram> response = instagramService.getAllInstagramAccount();
        return new ResponseEntity<>(response, HttpStatus.OK);

    }

    @GetMapping(value = "/instagram/{id}")
    @ResponseBody
    public Instagram getAccountById(@PathVariable("id") final UUID uuid) throws Exception {
        Instagram response = instagramService.getInstagramAccountById(uuid);
        return response;
    }

    @PostMapping("/instagram")
    public ResponseEntity<Instagram> saveAccount(@RequestBody Instagram ins)throws Exception {
            Instagram response = instagramService.saveNewInstagramAccount(ins);
            return new ResponseEntity<>(response, HttpStatus.CREATED);
    }


    @DeleteMapping("/instagram/{id}")
    public ResponseEntity<String> deleteById(@PathVariable("id") final UUID uuid) {
            String response = instagramService.deleteInstagramAccountById(uuid);
            return new ResponseEntity<>(response, HttpStatus.OK);
    }

    @DeleteMapping("/instagram/all")
    public ResponseEntity<String> resetAccounts() {
            String response = instagramService.deleteAllAccounts();
            return new ResponseEntity<>(response, HttpStatus.OK);
    }

    @PutMapping("/instagram/update")
    public ResponseEntity<Instagram> updateAccount(@RequestBody Instagram ins) throws Exception{
            Instagram response = instagramService.updateInstagraAccoun(ins);
            return new ResponseEntity<>(response, HttpStatus.OK);
    }
}
