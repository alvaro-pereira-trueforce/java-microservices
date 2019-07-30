package com.techalvaro.instagram.service.instagram.api;

import com.techalvaro.instagram.service.instagram.http.HttpClient;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.stereotype.Component;

@Component
public class InstagramApi {

    @Value("${api.persistence.service}")
    private String persistence;

    @Value("${api.facebook.url}")
    private String facebookURL;

    private final HttpClient httpClient;

    public InstagramApi(HttpClient httpClient) {
        this.httpClient = httpClient;
    }

    public <T> T getUser(String id, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + "me/accounts";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPageInstagram(String pageID, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + pageID + "?fields=instagram_business_account";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPageAccessToken(String pageID) throws Exception {
        String URL = facebookURL + pageID + "/?fields=access_token,name";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPosts(String pageID, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + pageID + "/media?fields=id,media_type,caption,media_url,thumbnail_url,permalink,username,timestamp,comments_count&limit=100";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getComments(String postID, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + postID + "/comments?fields=id,text,username,timestamp,replies{id,text,username,timestamp}&limit=100";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T postComment(String postID, String body, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + postID + "/comments?message=" + body;
        return (T) this.httpClient.makePostRequest(URL, Object.class);
    }

    public <T> T getInstagramMediaByID(String mediaID, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + mediaID + "?fields=id,comments_count,caption,like_count,media_ty  pe,media_url,permalink,username,is_comment_enabled,thumbnail_url,timestamp,owner,shortcode,ig_id,comments{id,text,username,like_count,timestamp,replies{id,text,username,like_count,timestamp,hidden,media,user},media},children{media_type,media_url,id,shortcode,timestamp,permalink,thumbnail_url}";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getInstagramCommentByID(String commentId, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + commentId + "?fields=id,media,text,username,timestamp,hidden,like_count";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getMediaWithCommentsAndReplies(String mediaId, String token) throws Exception {
        setHeader(token);
        String URL = facebookURL + mediaId + "?fields=comments{replies{id}}";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }


    private void setHeader(String token) {
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_JSON);
        headers.set("Authorization", "Bearer " + token);
        httpClient.setHttpHeaders(headers);
    }

}
