package com.techalvaro.instagram.service.instagram.utilities;

import java.util.Map;
import java.util.stream.Collectors;

public class StringUtilitiy {
    public static <T> T filterByParameter(Map<T, T> mapping, String value) throws Exception {
        Map<T, T> result = mapping.entrySet()
                .stream()
                .filter(map -> map.getKey().equals(value))
                .collect(Collectors.toMap(map -> map.getKey(), map -> map.getValue()));
        return (T) result;
    }

}
