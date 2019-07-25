package com.techalvaro.linkedinService.http;

import org.springframework.http.HttpEntity;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpMethod;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Component;
import org.springframework.web.client.RestTemplate;

@Component
public class HttpClient {

    private HttpHeaders httpHeaders;
    private RestTemplate httpTemplate;

    public HttpClient(HttpHeaders httpHeaders, RestTemplate restTemplate) {
        this.httpHeaders = httpHeaders;
        this.httpTemplate = restTemplate;
    }


    public void setHttpHeaders(HttpHeaders httpHeaders) {
        this.httpHeaders = httpHeaders;
    }

    public void setHttpTemplate(RestTemplate httpTemplate) {
        this.httpTemplate = httpTemplate;
    }

    public <T> T makeGetRequest(String URL, Class<T> responseType) throws Exception {
        ResponseEntity<T> response = httpTemplate.exchange(
                URL,
                HttpMethod.GET,
                new HttpEntity<>(httpHeaders),
                responseType
        );
        return response.getBody();
    }

    public <T> T makePostRequest(String URL, Object body, Class<T> responseType) throws Exception {

        ResponseEntity<T> response = httpTemplate.exchange(
                URL,
                HttpMethod.POST,
                new HttpEntity<>(body, httpHeaders),
                responseType
        );
        return response.getBody();
    }
}
