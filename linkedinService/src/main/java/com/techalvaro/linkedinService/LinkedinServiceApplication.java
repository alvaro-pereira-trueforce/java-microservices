package com.techalvaro.linkedinService;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.cloud.netflix.eureka.EnableEurekaClient;

@EnableEurekaClient
@SpringBootApplication
public class LinkedinServiceApplication {

    public static void main(String[] args) {
        SpringApplication.run(LinkedinServiceApplication.class, args);
    }

}
