<?php

function print_array($arr)
{
  echo '<pre>';
  print_r($arr);
  echo '</pre>';
}

function redirect_to($to)
{
  header("Location: $to");
  die();
}