<?php

function validateBooking()
{
  $errors = [];
  $seatMin = 1;
  $seatMax = 10;

  // Validates user name field
  if ($_POST['user']['name'] == '') {
    $errors['user']['name'] = "Name can't be blank";
  } elseif (!preg_match("/^[a-zA-Z .'-]{1,255}$/", $_POST['user']['name'])) {
    $errors['user']['name'] = "Name can only included characters {Aa - Zz} or {.'-}";
  }

  // Validates user email field
  if ($_POST['user']['email'] == '') {
    $errors['user']['email'] = "Email can't be blank";
  } elseif (!filter_var($_POST['user']['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['user']['email'] = "Incorrect email format (eg. name@mail.com)";
  }

  // Validates user mobile field
  if ($_POST['user']['mobile'] == '') {
    $errors['user']['mobile'] = "Mobile can't be blank";
  } elseif (!preg_match("/^(\(04\)|04|\+614)( ?\d){8}$/", $_POST['user']['mobile'])) {
    $errors['user']['mobile'] = "Incorrect mobile number format (eg. 0412345678)";
  }

  // Validates seat number inputs
  foreach ($_POST['seats'] as $value) {
    if (!empty($value)) {
      if (!is_numeric($value)) {
        $errors['seats'] = "Seats entered must be a number";
      } else {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
          $errors['seats'] = "Seats entered must be whole numbers";
        } elseif (($value < $seatMin) || ($value > $seatMax)) {
          $errors['seats'] = "Seats entered must be from 1 to 10 or blank";
        }
      }
    }
  }

  return $errors;
}
