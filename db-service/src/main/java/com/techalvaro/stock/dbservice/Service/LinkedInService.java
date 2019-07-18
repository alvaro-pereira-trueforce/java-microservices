package com.techalvaro.stock.dbservice.Service;

import com.techalvaro.stock.dbservice.model.Linkedin;
import com.techalvaro.stock.dbservice.repository.LinkedinRepository;
import org.springframework.stereotype.Service;

import javax.persistence.EntityNotFoundException;
import java.util.List;
import java.util.UUID;

@Service
public class LinkedInService {
    private LinkedinRepository linkedinRepository;

    public LinkedInService(LinkedinRepository linkedinRepository) {
        this.linkedinRepository = linkedinRepository;
    }

    public List<Linkedin> getAllLinkedInAccount() {
        return linkedinRepository.findAll();
    }

    public Linkedin getLinkedInAccountById(UUID uuid) {
        return linkedinRepository.findById(uuid).orElseThrow(EntityNotFoundException::new);
    }

    public Linkedin saveNewLinkedInAccount(Linkedin link) {
        linkedinRepository.save(link);
        return getLinkedInAccountById(link.getUuid());
    }

    public String deleteLinkedInAccountById(UUID uuid) {
        linkedinRepository.deleteById(uuid);
        return "the account has been deleted successfully";
    }

    public Linkedin updateInstagraAccoun(Linkedin ins) {
        Linkedin linkedin = getLinkedInAccountById(ins.getUuid());
        linkedin.setAccess_token(ins.getAccess_token());
        linkedin.setExpires_in(ins.getExpires_in());
        linkedin.setIntegration_name(ins.getIntegration_name());
        return saveNewLinkedInAccount(linkedin);
    }

    public String deleteAllAccounts() {
        List<Linkedin> accounts = getAllLinkedInAccount();
        linkedinRepository.deleteAll(accounts);
        return "delete all account successfully ";
    }
}
