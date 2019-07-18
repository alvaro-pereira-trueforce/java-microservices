package com.techalvaro.stock.dbservice.Service;

import com.techalvaro.stock.dbservice.ExceptionHandler.WebExceptions.NotFoundException;
import com.techalvaro.stock.dbservice.model.Instagram;
import com.techalvaro.stock.dbservice.repository.InstagramRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.UUID;

@Service
public class InstagramService {

    private InstagramRepository instagramRepository;

    public InstagramService(InstagramRepository instagramRepository) {
        this.instagramRepository = instagramRepository;
    }

    public List<Instagram> getAllInstagramAccount() {
        return instagramRepository.findAll();
    }

    public Instagram getInstagramAccountById(UUID id) throws Exception {
        return instagramRepository.findById(id).orElseThrow(() -> new NotFoundException("The instagram account doesn't exist."));
    }

    public Instagram saveNewInstagramAccount(Instagram ins) throws Exception{
        instagramRepository.save(ins);
        return getInstagramAccountById(ins.getUuid());
    }

    public String deleteInstagramAccountById(UUID id) {
        instagramRepository.deleteById(id);
        return "the account has been deleted successfully";
    }

    public Instagram updateInstagraAccoun(Instagram ins) throws Exception{
        Instagram instagram = getInstagramAccountById(ins.getUuid());
        instagram.setAccess_token(ins.getAccess_token());
        instagram.setExpires_in(ins.getExpires_in());
        instagram.setIntegration_name(ins.getIntegration_name());
        return saveNewInstagramAccount(instagram);
    }

    public String deleteAllAccounts() {
        List<Instagram> accounts = getAllInstagramAccount();
        instagramRepository.deleteAll(accounts);
        return "delete all account successfully ";
    }
}
