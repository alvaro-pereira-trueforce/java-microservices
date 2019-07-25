package com.techalvaro.linkedin.service.linkedin.api;

import com.techalvaro.linkedin.service.linkedin.http.HttpClient;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpHeaders;
import org.springframework.stereotype.Component;

import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;

@Component
public class LinkedInApi {

    @Value("${api.token}")
    private String token;

    @Value("${api.client.id}")
    private String clientID;

    @Value("${api.return.url}")
    private String returnUrl;

    @Value("${api.account.id}")
    private String accountId;

    private final HttpClient httpClient;

    public LinkedInApi(HttpClient httpClient) {
        this.httpClient = httpClient;
    }

    public <T> T getPosts(String id) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/ugcPosts?q=authors&authors=List(" + this.getEncodedValue(id) + ")";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPostsByLimit(String id, String limit) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/ugcPosts?q=authors&start=0&count=" + limit + "&authors=List(" + this.getEncodedValue(id) + ")";
        return (T) httpClient.makeGetRequest(URL, Object.class);

    }

    public <T> T getComments(String id) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/socialActions/" + this.getEncodedValue(id) + "/comments";
        return (T) httpClient.makeGetRequest(URL, Object.class);

    }

    public <T> T getCommentsByLimit(String id, String limit) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/socialActions/" + this.getEncodedValue(id) + "/comments?count=" + limit + "&start=0";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T geReply(String id) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/socialActions/" + this.getEncodedValue(id) + "/comments";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getEntities(String id) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/organizations/" + this.getEncodedValue(id);
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T postComment(String id, Object body) throws Exception {
        this.setRequestHeader();
        String URL = "https://api.linkedin.com/v2/socialActions/" + this.getEncodedValue(id) + "/comments";
        return (T) httpClient.makePostRequest(URL, body, Object.class);
    }

    public <T> T RequestToken() throws Exception {
        String URL = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=" + clientID + "&redirect_uri=" + returnUrl + "&state=" + accountId + "&scope=r_liteprofile,rw_organization_admin,r_organization_social,w_organization_social";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    private String getEncodedValue(String url) {
        try {
            return URLEncoder.encode(url, StandardCharsets.UTF_8.toString());
        } catch (Exception ex) {
            return url;
        }
    }

    private void setRequestHeader() {
        HttpHeaders headers = new HttpHeaders();
        headers.set("X-Restli-Protocol-Version", "2.0.0");
        headers.set("Authorization", "Bearer " + token);
        httpClient.setHttpHeaders(headers);
    }


}
