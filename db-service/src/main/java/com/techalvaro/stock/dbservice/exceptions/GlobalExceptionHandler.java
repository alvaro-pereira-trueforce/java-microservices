package com.techalvaro.stock.dbservice.exceptions;

import com.techalvaro.stock.dbservice.dtos.customDtos.ResponseDto;
import com.techalvaro.stock.dbservice.exceptions.webExceptions.BadRequestException;
import com.techalvaro.stock.dbservice.exceptions.webExceptions.NotFoundException;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.HttpRequestMethodNotSupportedException;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.context.request.WebRequest;
import org.springframework.web.servlet.mvc.method.annotation.ResponseEntityExceptionHandler;

@RestControllerAdvice
public class GlobalExceptionHandler extends ResponseEntityExceptionHandler {
    @ExceptionHandler({NotFoundException.class})
    @ResponseStatus(HttpStatus.NOT_FOUND)
    @ResponseBody
    public ResponseDto handleNotFoundException(NotFoundException ex) {
        return new ResponseDto(ex.getMessage(), HttpStatus.NOT_FOUND, 404);
    }

    @ExceptionHandler({BadRequestException.class})
    @ResponseStatus(HttpStatus.BAD_REQUEST)
    @ResponseBody
    public ResponseDto handleBadResquestException(BadRequestException ex) {
        return new ResponseDto(ex.getMessage(), HttpStatus.BAD_REQUEST, 400);
    }

    @ExceptionHandler({Exception.class})
    @ResponseStatus(HttpStatus.INTERNAL_SERVER_ERROR)
    @ResponseBody
    public Object handleDefaultException(NotFoundException ex, HttpHeaders headers, WebRequest request) {
        return null;
    }

    @Override
    protected ResponseEntity<Object> handleHttpRequestMethodNotSupported(HttpRequestMethodNotSupportedException ex, HttpHeaders headers, HttpStatus status, WebRequest request) {
        return super.handleHttpRequestMethodNotSupported(ex, headers, status, request);
    }
}
