package com.techalvaro.stock.stockservice.utils;

import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

public class StringUtility {

    private static final String NO_ALPHANUMERIC_PATTERN = "[^\\d-A-Za-z ]";

    public static <T> T filterByParameter(Map<T, T> mapping, String value) throws Exception {
        Map<T, T> result = mapping.entrySet()
                .stream()
                .filter(token -> token.getKey().equals(value))
                .collect(Collectors.toMap(Map.Entry::getKey, Map.Entry::getValue));
        return (T) result.get(value).toString();
    }

    public static String listAsString(List<String> list) {
        StringBuilder builder = new StringBuilder();
        for (String text : list) {
            builder.append(text);
        }
        return builder.toString();
    }

    public static boolean textMatchesWord(String text, String word) {
        return sanitizeString(text).replaceAll(NO_ALPHANUMERIC_PATTERN, "").trim()
                .equalsIgnoreCase(sanitizeString(word).trim());
    }

    private static String sanitizeString(String text) {
        return text != null ? text : "";
    }

}
