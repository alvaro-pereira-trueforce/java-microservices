package com.techalvaro.stock.dbservice.Utilities;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

public class PropertyAccesor {

    private static PropertyAccesor ourInstance = new PropertyAccesor();
    private Properties prop;

    public static PropertyAccesor getInstance() {
        return ourInstance;
    }

    private PropertyAccesor() {
        try (InputStream input = new FileInputStream("DBservice.properties")) {
            prop = new Properties();
            prop.load(input);
        } catch (FileNotFoundException ex) {
            ex.printStackTrace();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
    }

    public String getNotFoundExceptionMessage() {
        return prop.getProperty("INSTAGRAM_NO_FOUND_EXCEPTION");
    }

    public String getDeleteSuccessfullyMessage() {
        return prop.getProperty("INSTAGRAM_DELETED_ACCOUNT_SUCCESSFULLY");
    }

    public String getResetSuccessfullyMessage() {
        return prop.getProperty("INSTAGRAM_RESET_ACCOUNTs_SUCCESSFULLY");
    }

    public String getBadRequestExceptionMessage() {
        return prop.getProperty("INSTAGRAM_BAD_REQUEST_EXCEPTION");
    }
}
