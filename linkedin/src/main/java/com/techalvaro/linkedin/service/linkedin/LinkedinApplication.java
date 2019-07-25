package com.techalvaro.linkedin.service.linkedin;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.cloud.netflix.eureka.EnableEurekaClient;

@EnableEurekaClient
@SpringBootApplication
public class LinkedinApplication {

	public static void main(String[] args) {
		SpringApplication.run(LinkedinApplication.class, args);
	}

}
