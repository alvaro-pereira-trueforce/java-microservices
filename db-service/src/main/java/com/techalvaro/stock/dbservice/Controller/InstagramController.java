package com.techalvaro.stock.dbservice.Controller;

import com.techalvaro.stock.dbservice.Service.GenericService;
import com.techalvaro.stock.dbservice.Service.InstagramService;
import com.techalvaro.stock.dbservice.model.Instagram;
import org.springframework.http.*;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.client.RestTemplate;


@RestController
@RequestMapping("/rest-api/instagram")
public class InstagramController extends GenericController<Instagram> {

    private final String TOKEN = "EAAZAFufKCCSUBAOk9CQG2LZBPeUeoBqHr64ypXAxcuUZC4SMZB37JgYYeyfZBUTRLyXfVCjZBYmyPCvuBcLtmPSSC7h4hT7CRREP8b1oeS9M0oPJqgQmK68ZA5oswrloeVK95MZBBgGvjZCNsY0xKxB0H8kcJZBzsH60YW99FaZBpEMFNFJUIfc4jzL1E57qcVSZBrgZD";

    private final String TARGET_URL = "https://graph.facebook.com/v3.3/17841412758051552/media?fields=id,media_type,caption,media_url,thumbnail_url,permalink,username,timestamp,comments_count&limit= 30";

    private InstagramService instagramService;

    public InstagramController(InstagramService instagramService) {
        this.instagramService = instagramService;
    }

    @GetMapping("/all")
    public Object getAllPost() throws Exception {
        return this.getInstagramPost(Object.class);
    }

    public <T> T getInstagramPost(Class<T> responseType) throws Exception {
        RestTemplate httpClient = new RestTemplate();
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_JSON);
        headers.set("Authorization", "Bearer " + TOKEN);
        ResponseEntity<T> response = httpClient.exchange(
                TARGET_URL,
                HttpMethod.GET,
                new HttpEntity<>(headers),
                responseType
        );
        return response.getBody();
    }


    @Override
    protected GenericService getService() {
        return instagramService;
    }
}
