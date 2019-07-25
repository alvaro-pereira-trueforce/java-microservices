package com.techalvaro.instagram.service.instagram.api;

import com.techalvaro.instagram.service.instagram.http.HttpClient;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.stereotype.Component;

@Component
public class InstagramApi {

    @Value("${api.token}")
    private String token;

    private final HttpClient httpClient;

    private final String STATIC_URL = "https://graph.facebook.com/v3.3/";

    public InstagramApi(HttpClient httpClient) {
        this.httpClient = httpClient;
    }

    public <T> T getUser(String id) throws Exception {
        setHeader();
        String URL = STATIC_URL + "me/accounts";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPageInstagram(String pageID) throws Exception {
        setHeader();
        String URL = STATIC_URL + pageID + "?fields=instagram_business_account";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPageAccessToken(String pageID) throws Exception {
        setHeader();
        String URL = STATIC_URL + pageID + "/?fields=access_token,name";
        return (T) httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getPosts(String pageID) throws Exception {
        setHeader();
        String URL = STATIC_URL + pageID + "/media?fields=id,media_type,caption,media_url,thumbnail_url,permalink,username,timestamp,comments_count&limit=100";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getComments(String postID) throws Exception {
        setHeader();
        String URL = STATIC_URL + postID + "/comments?fields=id,text,username,timestamp,replies{id,text,username,timestamp}&limit=100";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T postComment(String postID, String body) throws Exception {
        setHeader();
        String URL = STATIC_URL + postID + "/comments?message=" + body;
        return (T) this.httpClient.makePostRequest(URL, Object.class);
    }

    public <T> T getInstagramMediaByID(String mediaID) throws Exception {
        setHeader();
        String URL = STATIC_URL + mediaID + "?fields=id,comments_count,caption,like_count,media_ty  pe,media_url,permalink,username,is_comment_enabled,thumbnail_url,timestamp,owner,shortcode,ig_id,comments{id,text,username,like_count,timestamp,replies{id,text,username,like_count,timestamp,hidden,media,user},media},children{media_type,media_url,id,shortcode,timestamp,permalink,thumbnail_url}";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getInstagramCommentByID(String commentId) throws Exception {
        setHeader();
        String URL = STATIC_URL + commentId + "?fields=id,media,text,username,timestamp,hidden,like_count";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }

    public <T> T getMediaWithCommentsAndReplies(String mediaId) throws Exception {
        setHeader();
        String URL = STATIC_URL + mediaId + "?fields=comments{replies{id}}";
        return (T) this.httpClient.makeGetRequest(URL, Object.class);
    }


    private void setHeader() {
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_JSON);
        headers.set("Authorization", "Bearer " + token);
        httpClient.setHttpHeaders(headers);
    }

}
